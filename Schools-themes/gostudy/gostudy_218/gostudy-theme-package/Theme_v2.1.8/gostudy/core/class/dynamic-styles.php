<?php

defined('ABSPATH') || exit;

use Gostudy_Theme_Helper as Gostudy;
use RTAddons\Gostudy_Global_Variables as Gostudy_Globals;

/**
 * Gostudy Dynamic Styles
 *
 *
 * @package gostudy\core\class
 * @author RaisTheme <help.raistheme@gmail.com>
 * @since 1.0.0
 */
class Gostudy_Dynamic_Styles
{
    protected static $instance;

    private $theme_slug;
    private $template_directory_uri;
    private $use_minified;
    private $enqueued_stylesheets = [];
    private $header_page_id;

    public function __construct()
    {
        $this->theme_slug = $this->get_theme_slug();
        $this->template_directory_uri = get_template_directory_uri();
        $this->use_minified = Gostudy::get_option('use_minified') ? '.min' : '';
        $this->header_type = Gostudy::get_option('header_type');
        $this->gradient_enabled = Gostudy::get_mb_option('use-gradient', 'mb_page_colors_switch', 'custom');

        $this->enqueue_styles_and_scripts();
        $this->add_body_classes();
    }

    public function get_theme_slug()
    {
        return str_replace('-child', '', wp_get_theme()->get('TextDomain'));
    }

    public function enqueue_styles_and_scripts()
    {
        //* Elementor Compatibility
        add_action('wp_enqueue_scripts', [$this, 'get_elementor_css_theme_builder']);
        add_action('wp_enqueue_scripts', [$this, 'elementor_column_fix']);
        add_action('wp_enqueue_scripts', [$this, 'learnpress_lms_custom_styles']);
        add_action('wp_enqueue_scripts', [$this, 'learnpress_custom_colors']);
        add_action('wp_head', [$this, 'gostudy_extra_custom_css']);
        // add_action('wp_head', 'edubin_customizer_theme_style');
        add_action('wp_enqueue_scripts', [$this, 'frontend_stylesheets']);
        add_action('wp_enqueue_scripts', [$this, 'frontend_scripts']);

        add_action('admin_enqueue_scripts', [$this, 'admin_stylesheets']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
    }

    public function get_elementor_css_theme_builder()
    {
        $current_post_id = get_the_ID();
        $css_files = [];

        $locations[] = $this->get_elementor_css_cache_header();
        $locations[] = $this->get_elementor_css_cache_header_sticky();
        $locations[] = $this->get_elementor_css_cache_footer();
        $locations[] = $this->get_elementor_css_cache_side_panel();

        foreach ($locations as $location) {
            //* Don't enqueue current post here (let the preview/frontend components to handle it)
            if ($location && $current_post_id !== $location) {
                $css_file = new \Elementor\Core\Files\CSS\Post($location);
                $css_files[] = $css_file;
            }
        }

        if (!empty($css_files)) {
            \Elementor\Plugin::$instance->frontend->enqueue_styles();
            foreach ($css_files as $css_file) {
                $css_file->enqueue();
            }
        }
    }

    public function get_elementor_css_cache_header()
    {
        if (
            !apply_filters('gostudy/header/enable', true)
            || !class_exists('\Elementor\Core\Files\CSS\Post')
        ) {
            // Bailtout.
            return;
        }

        if (
            $this->RWMB_is_active()
            && 'custom' === rwmb_meta('mb_customize_header_layout')
            && 'default' !== rwmb_meta('mb_header_content_type')
        ) {
            $this->header_type = 'custom';
            $this->header_page_id = rwmb_meta('mb_customize_header');
        } else {
            $this->header_page_id = Gostudy::get_option('header_page_select');
        }

        if ('custom' === $this->header_type) {
            return $this->multi_language_support($this->header_page_id, 'header');
        }
    }

    public function get_elementor_css_cache_header_sticky()
    {
        if (
            !apply_filters('gostudy/header/enable', true)
            || 'custom' !== $this->header_type
            || !class_exists('\Elementor\Core\Files\CSS\Post')
        ) {
            // Bailtout.
            return;
        }

        if (
            $this->RWMB_is_active()
            && 'custom' === rwmb_meta('mb_customize_header_layout')
            && 'default' !== rwmb_meta('mb_sticky_header_content_type')
        ) {
            $header_sticky_page_id = rwmb_meta('mb_customize_sticky_header');
        } elseif (Gostudy::get_option('header_sticky')) {
            $header_sticky_page_id = Gostudy::get_option('header_sticky_page_select');
        }

        return $this->multi_language_support($header_sticky_page_id, 'header');
    }

    public function get_elementor_css_cache_footer()
    {
        $footer = apply_filters('gostudy/footer/enable', true);
        $footer_switch = $footer['footer_switch'] ?? '';

        if (
            !$footer_switch
            || 'pages' !== Gostudy::get_mb_option('footer_content_type', 'mb_footer_switch', 'on')
            || !class_exists('\Elementor\Core\Files\CSS\Post')
        ) {
            // Bailout.
            return;
        }

        $footer_page_id = Gostudy::get_mb_option('footer_page_select', 'mb_footer_switch', 'on');

        return $this->multi_language_support($footer_page_id, 'footer');
    }

    public function get_elementor_css_cache_side_panel()
    {
        if (
            !Gostudy::get_option('side_panel_enable')
            || 'pages' !== Gostudy::get_mb_option('side_panel_content_type', 'mb_customize_side_panel', 'custom')
            || !class_exists('\Elementor\Core\Files\CSS\Post')
        ) {
            // Bailout.
            return;
        }

        $sp_page_id = Gostudy::get_mb_option('side_panel_page_select', 'mb_customize_side_panel', 'custom');

        return $this->multi_language_support($sp_page_id, 'side-panel');
    }

    public function multi_language_support($page_id, $page_type)
    {
        if (!$page_id) {
            // Bailout.
            return;
        }

        $page_id = intval($page_id);

        if (class_exists('Polylang') && function_exists('pll_current_language')) {
            $currentLanguage = pll_current_language();
            $translations = PLL()->model->post->get_translations($page_id);

            $polylang_id = $translations[$currentLanguage] ?? '';
            $page_id = $polylang_id ?: $page_id;
        }

        if (class_exists('SitePress')) {
            $page_id = wpml_object_id_filter($page_id, $page_type, false, ICL_LANGUAGE_CODE);
        }

        return $page_id;
    }

    public function elementor_column_fix()
    {
        $css = '.elementor-container > .elementor-row > .elementor-column > .elementor-element-populated,'
            . '.elementor-container > .elementor-column > .elementor-element-populated {'
                . 'padding-top: 0;'
                . 'padding-bottom: 0;'
            . '}';

        $css .= '.elementor-column-gap-default > .elementor-row > .elementor-column > .elementor-element-populated,'
            . '.elementor-column-gap-default > .elementor-column > .elementor-element-populated {'
                . 'padding-left: 15px;'
                . 'padding-right: 15px;'
            . '}';
        wp_add_inline_style('elementor-frontend', $css);
    }

    public function learnpress_lms_custom_styles()
    {

    if ( $this->RWMB_is_active()) {
        $mb_featured_image_replace = rwmb_meta( 'mb_featured_image_replace', array( 'size' => 'full' ) );

        if (is_array($mb_featured_image_replace) || is_object($mb_featured_image_replace)){
            foreach ( $mb_featured_image_replace as $image ) {

                $featured_thumb_replace_url = $image['url'];
            }
        }
        $image = rwmb_meta('mb_page_title_bg');
        $src = $image['image'] ?? '';

        $header_thumb_url = $src;
    // $feature_thumb_url = get_the_post_thumbnail_url(); 
    }
    $learnpress_single__page_title_bg_image = Gostudy_Theme_Helper::get_mb_option('learnpress_single__page_title_bg_image', 'background-image', true);

    $page_title_bg_image = Gostudy_Theme_Helper::get_mb_option('page_title_bg_image', 'background-image', true)['background-image'];


    if (!empty($featured_thumb_replace_url)) {
        $thumbnail_url = $featured_thumb_replace_url;
    }
    elseif (!empty($header_thumb_url)) {
        $thumbnail_url = $header_thumb_url;
    } 
     elseif (!empty($learnpress_single__page_title_bg_image['background-image'])) {
         $thumbnail_url = $learnpress_single__page_title_bg_image['background-image']; 
     }
    else {
       $thumbnail_url = $page_title_bg_image; 
    }

        $css = 'body.single-lp_course .lp-archive-courses .course-summary .course-summary-content .course-detail-info {'
                . 'background: url('.$thumbnail_url.');'
            . '}';

        wp_add_inline_style('elementor-frontend', $css);
    }

    public function gostudy_extra_custom_css()
    { 
        $page_title_overlay_color_value = Gostudy::get_mb_option('page_title_overlay_color');

        ?>

        <style>

            <?php if ($page_title_overlay_color_value): ?>
                .page-header:before {
                    background: <?php echo esc_attr( $page_title_overlay_color_value["rgba"] ); ?>;
                }
            <?php endif ?>

        </style>

      <?php
    }

    public function learnpress_custom_colors()
    {
         $lp_replace_primary_color = Gostudy::get_mb_option('lp_replace_primary_color');
         $lp_replace_secondary_color = Gostudy::get_mb_option('lp_replace_secondary_color');


        $css = ':root {'
                . '--lp-primary-color: '.$lp_replace_primary_color.' !important;'
                . '--lp-secondary-color: '.$lp_replace_secondary_color.' !important;'
            . '}';

        wp_add_inline_style('elementor-frontend', $css);
        
  
    }

    public function frontend_stylesheets()
    {
        wp_enqueue_style($this->theme_slug . '-theme-info', get_bloginfo('stylesheet_url'));

        $this->enqueue_css_variables();
        $this->enqueue_additional_styles();
        $this->enqueue_style('main', '/css/');
        $this->enqueue_pluggable_styles();
        $this->enqueue_style('responsive', '/css/', $this->enqueued_stylesheets);
        $this->enqueue_style('dynamic', '/css/', $this->enqueued_stylesheets);
    }

    public function enqueue_css_variables()
    {
        return wp_add_inline_style(
            $this->theme_slug . '-theme-info',
            $this->retrieve_css_variables_and_extra_styles()
        );
    }

    public function enqueue_additional_styles()
    {
        wp_enqueue_style('font-awesome-5-all', $this->template_directory_uri . '/css/font-awesome-5.min.css');
        wp_enqueue_style('gostudy-flaticon', $this->template_directory_uri . '/fonts/flaticon/flaticon.css');
    }

    public function retrieve_css_variables_and_extra_styles()
    {
        $root_vars = $extra_css = '';

        /**
         * Color Variables
         */
        if (
            class_exists('RWMB_Loader')
            && 'custom' == Gostudy::get_mb_option('page_colors_switch', 'mb_page_colors_switch', 'custom')
        ) {
            $theme_primary_color = Gostudy::get_mb_option('theme-primary-color');
            $theme_secondary_color = Gostudy::get_mb_option('theme-secondary-color');
            $theme_tertiary_color = Gostudy::get_mb_option('theme-tertiary-color');

	        $button_color_idle = Gostudy::get_mb_option('button-color-idle');
            $button_color_hover = Gostudy::get_mb_option('button-color-hover');

            $bg_body = Gostudy::get_mb_option('body_background_color');

            $scroll_up_arrow_color = Gostudy::get_mb_option('scroll_up_arrow_color');
            $scroll_up_bg_color = Gostudy::get_mb_option('scroll_up_bg_color');

            $this->gradient_enabled && $theme_gradient_from = Gostudy::get_mb_option('theme-gradient-from');
            $this->gradient_enabled && $theme_gradient_to = Gostudy::get_mb_option('theme-gradient-to');
        } else {
            $theme_primary_color = Gostudy_Globals::get_primary_color();
            $theme_secondary_color = Gostudy_Globals::get_secondary_color();
            $theme_tertiary_color = Gostudy_Globals::get_tertiary_color();

            $button_color_idle = Gostudy_Globals::get_btn_color_idle();
            $button_color_hover = Gostudy_Globals::get_btn_color_hover();

            $bg_body = Gostudy::get_option('body-background-color');

            $scroll_up_arrow_color = Gostudy::get_option('scroll_up_arrow_color');
            $scroll_up_bg_color = Gostudy::get_option('scroll_up_bg_color');

            $this->gradient_enabled && $theme_gradient = Gostudy::get_option('theme-gradient');
        }

        $root_vars .= '--gostudy-primary-color: ' . esc_attr($theme_primary_color) . ';';
        $root_vars .= '--gostudy-secondary-color: ' . esc_attr($theme_secondary_color) . ';';
        $root_vars .= '--gostudy-tertiary-color: ' . esc_attr($theme_tertiary_color) . ';';

        $root_vars .= '--gostudy-button-color-idle: ' . esc_attr($button_color_idle) . ';';
        $root_vars .= '--gostudy-button-color-hover: ' . esc_attr($button_color_hover) . ';';

        $root_vars .= '--gostudy-back-to-top-color: ' . esc_attr($scroll_up_arrow_color) . ';';
        $root_vars .= '--gostudy-back-to-top-background: ' . esc_attr($scroll_up_bg_color) . ';';

        $root_vars .= '--gostudy-body-background: ' . esc_attr($bg_body) . ';';

        $root_vars .= '--gostudy-primary-rgb: ' . esc_attr(Gostudy::HexToRGB($theme_primary_color)) . ';';
        $root_vars .= '--gostudy-secondary-rgb: ' . esc_attr(Gostudy::HexToRGB($theme_secondary_color)) . ';';
        $root_vars .= '--gostudy-button-rgb-idle: ' . esc_attr(Gostudy::HexToRGB($button_color_idle)) . ';';
        $root_vars .= '--gostudy-button-rgb-hover: ' . esc_attr(Gostudy::HexToRGB($button_color_hover)) . ';';
        //* ↑ color variables

        /**
         * Headings Variables
         */
        $header_font = Gostudy::get_option('header-font');
        $root_vars .= '--gostudy-header-font-family: ' . (esc_attr($header_font['font-family'] ?? '')) . ';';
        $root_vars .= '--gostudy-header-font-weight: ' . (esc_attr($header_font['font-weight'] ?? '')) . ';';
        $root_vars .= '--gostudy-header-font-color: ' . (esc_attr($header_font['color'] ?? '')) . ';';

        for ($i = 1; $i <= 6; $i++) {
            ${'header-h' . $i} = Gostudy::get_option('header-h' . $i);

            $root_vars .= '--gostudy-h' . $i . '-font-family: ' . (esc_attr(${'header-h' . $i}['font-family'] ?? '')) . ';';
            $root_vars .= '--gostudy-h' . $i . '-font-size: ' . (esc_attr(${'header-h' . $i}['font-size'] ?? '')) . ';';
            $root_vars .= '--gostudy-h' . $i . '-line-height: ' . (esc_attr(${'header-h' . $i}['line-height'] ?? '')) . ';';
            $root_vars .= '--gostudy-h' . $i . '-font-weight: ' . (esc_attr(${'header-h' . $i}['font-weight'] ?? '')) . ';';
            $root_vars .= '--gostudy-h' . $i . '-text-transform: ' . (esc_attr(${'header-h' . $i}['text-transform'] ?? '')) . ';';
        }
        //* ↑ headings variables

        /**
         * Content Variables
         */
        $main_font = Gostudy::get_option('main-font');
        $content_font_size = $main_font['font-size'] ?? '';
        $content_line_height = $main_font['line-height'] ?? '';
        $content_line_height = $content_line_height ? round(((int) $content_line_height / (int) $content_font_size), 3) : '';

        $root_vars .= '--gostudy-content-font-family: ' . (esc_attr($main_font['font-family'] ?? '')) . ';';
        $root_vars .= '--gostudy-content-font-size: ' . esc_attr($content_font_size) . ';';
        $root_vars .= '--gostudy-content-line-height: ' . esc_attr($content_line_height) . ';';
        $root_vars .= '--gostudy-content-font-weight: ' . (esc_attr($main_font['font-weight'] ?? '')) . ';';
        $root_vars .= '--gostudy-content-color: ' . (esc_attr($main_font['color'] ?? '')) . ';';
        //* ↑ content variables

        /**
         * Menu Variables
         */
        $menu_font = Gostudy::get_option('menu-font');
        $root_vars .= '--gostudy-menu-font-family: ' . (esc_attr($menu_font['font-family'] ?? '')) . ';';
        $root_vars .= '--gostudy-menu-font-size: ' . (esc_attr($menu_font['font-size'] ?? '')) . ';';
        $root_vars .= '--gostudy-menu-line-height: ' . (esc_attr($menu_font['line-height'] ?? '')) . ';';
        $root_vars .= '--gostudy-menu-font-weight: ' . (esc_attr($menu_font['font-weight'] ?? '')) . ';';
        //* ↑ menu variables

        /**
         * Submenu Variables
         */
        $sub_menu_font = Gostudy::get_option('sub-menu-font');
        $root_vars .= '--gostudy-submenu-font-family: ' . (esc_attr($sub_menu_font['font-family'] ?? '')) . ';';
        $root_vars .= '--gostudy-submenu-font-size: ' . (esc_attr($sub_menu_font['font-size'] ?? '')) . ';';
        $root_vars .= '--gostudy-submenu-line-height: ' . (esc_attr($sub_menu_font['line-height'] ?? '')) . ';';
        $root_vars .= '--gostudy-submenu-font-weight: ' . (esc_attr($sub_menu_font['font-weight'] ?? '')) . ';';
        $root_vars .= '--gostudy-submenu-color: ' . (esc_attr(Gostudy::get_option('sub_menu_color') ?? '')) . ';';
        $root_vars .= '--gostudy-submenu-background: ' . (esc_attr(Gostudy::get_option('sub_menu_background')['rgba'] ?? '')) . ';';

        $root_vars .= '--gostudy-submenu-mobile-color: ' . (esc_attr(Gostudy::get_option('mobile_sub_menu_color') ?? '')) . ';';
        $root_vars .= '--gostudy-submenu-mobile-background: ' . (esc_attr(Gostudy::get_option('mobile_sub_menu_background')['rgba'] ?? '')) . ';';
        $root_vars .= '--gostudy-submenu-mobile-overlay: ' . (esc_attr(Gostudy::get_option('mobile_sub_menu_overlay')['rgba'] ?? '')) . ';';

        $sub_menu_border = Gostudy::get_option('header_sub_menu_bottom_border');
        if ($sub_menu_border) {
            $sub_menu_border_height = Gostudy::get_option('header_sub_menu_border_height')['height'] ?? '';
            $sub_menu_border_color = Gostudy::get_option('header_sub_menu_bottom_border_color')['rgba'] ?? '';

            $extra_css .= '.primary-nav ul li ul li:not(:last-child),'
                . '.sitepress_container > .wpml-ls ul ul li:not(:last-child) {'
                    . ($sub_menu_border_height ? 'border-bottom-width: ' . (int) esc_attr($sub_menu_border_height) . 'px;' : '')
                    . ($sub_menu_border_color ? 'border-bottom-color: ' . esc_attr($sub_menu_border_color) . ';' : '')
                    . 'border-bottom-style: solid;'
                . '}';
        }
        //* ↑ submenu variables

        /**
         * Additional Font Variables
         */
        $extra_font = Gostudy::get_option('additional-font');
        empty($extra_font['font-family']) || $root_vars .= '--gostudy-additional-font-family: ' . esc_attr($extra_font['font-family']) . ';';
        empty($extra_font['font-weight']) || $root_vars .= '--gostudy-additional-font-weight: ' . esc_attr($extra_font['font-weight']) . ';';
        empty($extra_font['color']) || $root_vars .= '--gostudy-additional-font-color: ' . esc_attr($extra_font['color']) . ';';
        //* ↑ additional font variables

        /**
         * Footer Variables
         */
        if (
            Gostudy::get_option('footer_switch')
            && 'widgets' === Gostudy::get_option('footer_content_type')
        ) {
            $root_vars .= '--gostudy-footer-content-color: ' . (esc_attr(Gostudy::get_option('footer_text_color') ?? '')) . ';';
            $root_vars .= '--gostudy-footer-heading-color: ' . (esc_attr(Gostudy::get_option('footer_heading_color') ?? '')) . ';';
            $root_vars .= '--gostudy-copyright-content-color: ' . (esc_attr(Gostudy::get_mb_option('copyright_text_color', 'mb_copyright_switch', 'on') ?? '')) . ';';
        }
        //* ↑ footer variables

        /**
         * Side Panel Variables
         */
        if (
            $this->RWMB_is_active()
            && 'custom' === rwmb_meta('mb_customize_side_panel')
        ) {
            $sidepanel_title_color = rwmb_meta('mb_side_panel_title_color');
        } else {
            $sidepanel_title_color = Gostudy::get_option('side_panel_title_color')['rgba'] ?? '';
        }
        $root_vars .= '--gostudy-sidepanel-title-color: ' . esc_attr($sidepanel_title_color) . ';';
        //* ↑ side panel variables

        /**
         * Elementor Container
         */
        $root_vars .= '--gostudy-elementor-container-width: ' . $this->get_elementor_container_width() . 'px;';
        //* ↑ elementor container

        $css_variables = ':root {' . $root_vars . '}';

        $extra_css .= $this->get_mobile_header_extra_css();
        $extra_css .= $this->get_page_title_responsive_extra_css();

        return $css_variables . $extra_css;
    }

    public function get_elementor_container_width()
    {
        if (
            did_action('elementor/loaded')
            && defined('ELEMENTOR_VERSION')
        ) {
            if (version_compare(ELEMENTOR_VERSION, '3.0', '<')) {
                $container_width = get_option('elementor_container_width') ?: 1140;
            } else {
                $kit_id = (new \Elementor\Core\Kits\Manager())->get_active_id();
                $meta_key = \Elementor\Core\Settings\Page\Manager::META_KEY;
                $kit_settings = get_post_meta($kit_id, $meta_key, true);
                $container_width = $kit_settings['container_width']['size'] ?? 1140;
            }
        }

        return $container_width ?? 1170;
    }

    protected function get_mobile_header_extra_css()
    {
        $extra_css = '';

        if (Gostudy::get_option('mobile_header')) {
            $mobile_background = Gostudy::get_option('mobile_background')['rgba'] ?? '';
            $mobile_color = Gostudy::get_option('mobile_color');

            $extra_css .= '.rt-theme-header {'
                    . 'background-color: ' . esc_attr($mobile_background) . ' !important;'
                    . 'color: ' . esc_attr($mobile_color) . ' !important;'
                . '}';
        }

        $extra_css .= 'header.rt-theme-header .rt-mobile-header {'
                . 'display: block;'
            . '}'
            . '.rt-site-header,'
            . '.rt-theme-header .primary-nav {'
                . 'display: none;'
            . '}'
            . '.rt-theme-header .hamburger-box {'
                . 'display: inline-flex;'
            . '}'
            . 'header.rt-theme-header .mobile_nav_wrapper .primary-nav {'
                . 'display: block;'
            . '}'
            . '.rt-theme-header .rt-sticky-header {'
                . 'display: none;'
            . '}'
            . '.rt-page-socials {'
                . 'display: none;'
            . '}';

        $mobile_sticky = Gostudy::get_option('mobile_sticky');

        if (Gostudy::get_option('mobile_over_content')) {
            $extra_css .= '.rt-theme-header {'
                    . 'position: absolute;'
                    . 'z-index: 99;'
                    . 'width: 100%;'
                    . 'left: 0;'
                    . 'top: 0;'
                . '}';

            if ($mobile_sticky) {
                $extra_css .= 'body .rt-theme-header .rt-mobile-header {'
                        . 'position: absolute;'
                        . 'left: 0;'
                        . 'width: 100%;'
                    . '}';
            }

        } else {
            $extra_css .= 'body .rt-theme-header.header_overlap {'
                    . 'position: relative;'
                    . 'z-index: 2;'
                . '}';
        }

        if ($mobile_sticky) {
            $extra_css .= 'body .rt-theme-header,'
                . 'body .rt-theme-header.header_overlap {'
                    . 'position: sticky;'
                    . 'top: 0;'
                . '}';
        }

        return '@media only screen and (max-width: ' . $this->get_header_mobile_breakpoint() . 'px) {' . $extra_css . '}';
    }

    protected function get_header_mobile_breakpoint()
    {
        $elementor_breakpoint = '';

        if (
            'custom' === $this->header_type
            && $this->header_page_id
            && did_action('elementor/loaded')
        ) {
            $settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');
            $settings_model = $settings_manager->get_model($this->header_page_id);

            $elementor_breakpoint = $settings_model->get_settings('mobile_breakpoint');
        }

        return $elementor_breakpoint ?: (int) Gostudy::get_option('header_mobile_queris');
    }

    protected function get_page_title_responsive_extra_css()
    {
        $page_title_resp = Gostudy::get_option('page_title_resp_switch');

        if (
            $this->RWMB_is_active()
            && 'on' === rwmb_meta('mb_page_title_switch')
            && rwmb_meta('mb_page_title_resp_switch')
        ) {
            $page_title_resp = true;
        }

        if (!$page_title_resp) {
            // Bailout, if no any responsive logic
            return;
        }

        $pt_breakpoint = (int) Gostudy::get_mb_option('page_title_resp_resolution', 'mb_page_title_resp_switch', true);
        $pt_padding = Gostudy::get_mb_option('page_title_resp_padding', 'mb_page_title_resp_switch', true);
        $pt_font = Gostudy::get_mb_option('page_title_resp_font', 'mb_page_title_resp_switch', true);

        $breadcrumbs_font = Gostudy::get_mb_option('page_title_resp_breadcrumbs_font', 'mb_page_title_resp_switch', true);
        $breadcrumbs_switch = Gostudy::get_mb_option('page_title_resp_breadcrumbs_switch', 'mb_page_title_resp_switch', true);

        //* Title styles
        $pt_color = !empty($pt_font['color']) ? 'color: ' . esc_attr($pt_font['color']) . ' !important;' : '';
        $pt_f_size = !empty($pt_font['font-size']) ? ' font-size: ' . esc_attr((int) $pt_font['font-size']) . 'px !important;' : '';
        $pt_line_height = !empty($pt_font['line-height']) ? ' line-height: ' . esc_attr((int) $pt_font['line-height']) . 'px !important;' : '';
        $pt_additional_style = !(bool) $breadcrumbs_switch ? ' margin-bottom: 0 !important;' : '';
        $title_style = $pt_color . $pt_f_size . $pt_line_height . $pt_additional_style;

        //* Breadcrumbs Styles
        $breadcrumbs_color = !empty($breadcrumbs_font['color']) ? 'color: ' . esc_attr($breadcrumbs_font['color']) . ' !important;' : '';
        $breadcrumbs_f_size = !empty($breadcrumbs_font['font-size']) ? 'font-size: ' . esc_attr((int) $breadcrumbs_font['font-size']) . 'px !important;' : '';
        $breadcrumbs_line_height = !empty($breadcrumbs_font['line-height']) ? 'line-height: ' . esc_attr((int) $breadcrumbs_font['line-height']) . 'px !important;' : '';
        $breadcrumbs_display = !(bool) $breadcrumbs_switch ? 'display: none !important;' : '';
        $breadcrumbs_style = $breadcrumbs_color . $breadcrumbs_f_size . $breadcrumbs_line_height . $breadcrumbs_display;

        //* Blog Single Type 3
        $blog_t3_padding_top = Gostudy::get_option('single_padding_layout_3')['padding-top'] > 150 ? 150 : '';

        $extra_css = '.page-header {'
                . (!empty($pt_padding['padding-top']) ? 'padding-top: ' . esc_attr((int) $pt_padding['padding-top']) . 'px !important;' : '')
                . (!empty($pt_padding['padding-bottom']) ? 'padding-bottom: ' . esc_attr((int) $pt_padding['padding-bottom']) . 'px  !important;' : '')
                . 'min-height: auto !important;'
            . '}'
            . '.page-header_content .page-header_title {'
                . $title_style
            . '}'
            . '.page-header_content .page-header_breadcrumbs {'
                . $breadcrumbs_style
            . '}'
            . '.page-header_breadcrumbs .divider:not(:last-child):before {'
                . 'width: 10px;'
            . '}';

        if ($blog_t3_padding_top) {
            $extra_css .= '.single-post .post_featured_bg > .blog-post {'
                    . 'padding-top: ' . $blog_t3_padding_top . 'px !important;'
                . '}';
        }

        return '@media (max-width: ' . $pt_breakpoint . 'px) {' . $extra_css . '}';
    }

    /**
     * Enqueue theme stylesheets
     *
     * Function keeps track of already enqueued stylesheets and stores them in `enqueued_stylesheets[]`
     *
     * @param string   $tag      Unprefixed handle.
     * @param string   $file_dir Path to stylesheet folder, relative to root folder.
     * @param string[] $deps     Optional. An array of registered stylesheet handles this stylesheet depends on.
     */
    public function enqueue_style($tag, $file_dir, $deps = [])
    {
        $prefixed_tag = $this->theme_slug . '-' . $tag;
        $this->enqueued_stylesheets[] = $prefixed_tag;

        wp_enqueue_style(
            $prefixed_tag,
            $this->template_directory_uri . $file_dir . $tag . $this->use_minified . '.css',
            $deps
        );
    }

    public function enqueue_pluggable_styles()
    {
        //* Preloader
        if (Gostudy::get_option('preloader')) {
            $this->enqueue_style('preloader', '/css/pluggable/');
        }

        //* Page 404|Search
        if (is_404() || is_search()) {
            $this->enqueue_style('page-404', '/css/pluggable/');
        }

        //* Gutenberg
        if (Gostudy::get_option('disable_wp_gutenberg')) {
            wp_dequeue_style('wp-block-library');
        } else {
            $this->enqueue_style('gutenberg', '/css/pluggable/');
        }

        //* Post Single (blog, portfolio)
        if (is_single()) {
            $post_type = get_post()->post_type;
            if ('post' === $post_type || 'portfolio' === $post_type) {
                $this->enqueue_style('blog-single-post', '/css/pluggable/');
            }
        }

        //* Gostudy Core
        if (class_exists('RT_Addons_Elementor')) {
            $this->enqueue_style('gostudy-core', '/css/pluggable/');
        }

        //* WooCommerce Plugin
        if (class_exists('WooCommerce')) {
            $this->enqueue_style('woocommerce', '/css/pluggable/');
        }

        //* LearnPress Plugin
        if (class_exists('LearnPress')) {
            $this->enqueue_style('learnpress', '/css/pluggable/');
        }
        //* LearnDash Plugin
        if (class_exists('SFWD_LMS')) {
            $this->enqueue_style('learndash', '/css/pluggable/');
        }
        //* LearnDash Plugin Color
        if (class_exists('SFWD_LMS') && Gostudy::get_option('learndash_plugin_settings_colors')) {
            $this->enqueue_style('learndash-color', '/css/pluggable/');
        }
        //* Tutor Plugin
        if (function_exists('tutor')) {
            $this->enqueue_style('tutor', '/css/pluggable/');
        }
        //* Tutor Theme Color 
        if (function_exists('tutor') && Gostudy::get_option('tutor_plugin_settings_colors')) {
            $this->enqueue_style('tutor-color', '/css/pluggable/');
        }

        //* Side Panel
        if (Gostudy::get_option('side_panel_enable')) {
            $this->enqueue_style('side-panel', '/css/pluggable/');
        }
        //* WPML plugin
        if (class_exists('SitePress')) {
            $this->enqueue_style('wpml', '/css/pluggable/');
        }
        // VCZoom plugin
        if (class_exists('Zoom_Video_Conferencing_Api')) {
            $this->enqueue_style('vczoom', '/css/pluggable/');
        }
        //* Paid Membership Pro
        if (class_exists( 'PMPro_Membership_Level' )) {
            $this->enqueue_style('pmp', '/css/pluggable/');
        }
        //* Contact Froms 7 plugin
        if (class_exists( 'WPCF7_ContactForm' )) {
            $this->enqueue_style('wpcf-seven', '/css/pluggable/');
        }
        //* bbPress 
        if (class_exists('bbPress')) {
            $this->enqueue_style('bbpress', '/css/pluggable/');
        }
        //*  Modern Events Calendar
        if (class_exists('MEC')) {
            $this->enqueue_style('mec', '/css/pluggable/');
        }
        //*  Modern Events Calendar
        if (class_exists('MEC') && Gostudy::get_option('enable_mec_theme_color')) {
            $this->enqueue_style('mec-color', '/css/pluggable/');
        }
        //*  Profile Builder plugin
        //if (class_exists('UserRegistration') ) {
            $this->enqueue_style('userreg', '/css/pluggable/');
        //}

        $this->enqueue_style('lity', '/css/pluggable/');
    
    }

    public function frontend_scripts()
    {
        wp_enqueue_script('gostudy-theme-addons', $this->template_directory_uri . '/js/theme-addons' . $this->use_minified . '.js', ['jquery'], false, true);
        wp_enqueue_script('gostudy-theme', $this->template_directory_uri . '/js/theme.js', ['jquery'], false, true);

        wp_localize_script('gostudy-theme', 'rt_core', [
            'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
        ]);

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
        wp_enqueue_script('lity', $this->template_directory_uri . '/js/lity.min.js');

        wp_enqueue_script('perfect-scrollbar', $this->template_directory_uri . '/js/perfect-scrollbar.min.js');

    }

    public function admin_stylesheets()
    {
        wp_enqueue_style('gostudy-admin', $this->template_directory_uri . '/core/admin/css/admin.css');
        wp_enqueue_style('font-awesome-5-all', $this->template_directory_uri . '/css/font-awesome-5.min.css');
        wp_enqueue_style('wp-color-picker');
    }

    public function admin_scripts()
    {
        wp_enqueue_media();

        wp_enqueue_script('wp-color-picker');
	    wp_localize_script('wp-color-picker', 'wpColorPickerL10n', [
		    'clear' => esc_html__('Clear', 'gostudy'),
		    'clearAriaLabel' => esc_html__('Clear color', 'gostudy'),
		    'defaultString' => esc_html__('Default', 'gostudy'),
		    'defaultAriaLabel' => esc_html__('Select default color', 'gostudy'),
		    'pick' => esc_html__('Select', 'gostudy'),
		    'defaultLabel' => esc_html__('Color value', 'gostudy'),
        ]);

        wp_enqueue_script('gostudy-admin', $this->template_directory_uri . '/core/admin/js/admin.js');

        // For envato setup 
        wp_enqueue_script('gostudy-admin-envato', $this->template_directory_uri . '/core/admin/js/admin_envato.js');
        wp_enqueue_script('gostudy-blockUI-envato', $this->template_directory_uri . '/core/admin/js/jquery.blockUI.js');

        if (class_exists('RWMB_Loader')) {
            wp_enqueue_script('gostudy-metaboxes', $this->template_directory_uri . '/core/admin/js/metaboxes.js');
        }


    }

    protected function add_body_classes()
    {
        add_filter('body_class', function (Array $classes) {
            if ($this->gradient_enabled) {
                $classes[] = 'theme-gradient';
            }

            if (
                is_single()
                && 'post' === get_post_type(get_queried_object_id())
                && '3' === Gostudy::get_mb_option('single_type_layout', 'mb_post_layout_conditional', 'custom')
            ) {
                $classes[] = 'gostudy-blog-type-overlay';
            }

            return $classes;
        });
    }

    public function RWMB_is_active()
    {
        $id = !is_archive() ? get_queried_object_id() : 0;

        return class_exists('RWMB_Loader') && 0 !== $id;
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

new Gostudy_Dynamic_Styles();
