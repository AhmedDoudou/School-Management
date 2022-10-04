<?php

defined('ABSPATH') || exit;

if (!class_exists('Gostudy_Global_Functions')) {
    /**
     * Gostudy Global Functions
     *
     *
     * @package gostudy\core\class
     * @author RaisTheme <help.raistheme@gmail.com>
     * @since 1.0.0
     */
    class Gostudy_Global_Functions
    {
        function __construct()
        {
            self::declare_global_functions();
            self::declare_theme_filters();
        }

        /**
         * Declaration of Theme specific functions, which can be called globally.
         */
        public static function declare_global_functions()
        {
            if (!function_exists('gostudy_get_custom_menu')) {
                /**
                 * Retrieves all registered navigation menu.
                 */
                function gostudy_get_custom_menu()
                {
                    $nav_menus = [];
                    $terms = get_terms('nav_menu');
                    foreach ($terms as $term) {
                        $nav_menus[$term->name] = $term->name;
                    }

                    return $nav_menus;
                }
            }

            if (!function_exists('gostudy_main_menu')) {
                /**
                 * Displays a navigation menu.
                 *
                 * @since 1.0.0
                 * @param $menu             int|string|WP_Term Desired menu. Accepts a menu ID, slug,
                 *                                             name, or object.
                 * @param $children_counter bool               Whether to count submenu `li` items.
                 *                                             Default `true`.
                 * @param $submenu_disable  bool               If `true` will render only top-level menu
                 *                                             w/o submenu elements. Default `null`.
                 */
                function gostudy_main_menu($menu = '', $children_counter = false, $submenu_disable = null)
                {
                    wp_nav_menu([
                        'menu' => $menu,
                        'theme_location' => 'main_menu',
                        'container' => '',
                        'container_class' => '',
                        'after' => '',
                        'link_before' => '<span>',
                        'link_after' => '</span>',
                        'walker' => new Gostudy_Mega_Menu_Waker($children_counter, $submenu_disable)
                    ]);
                }
            }

            if (!function_exists('gostudy_get_all_sidebars')) {
                /**
                 * @since 1.0.0
                 * @return array registered sidebars
                 */
                function gostudy_get_all_sidebars()
                {
                    global $wp_registered_sidebars;

                    if (empty($wp_registered_sidebars)) return;

                    foreach ($wp_registered_sidebars as $sidebar_id => $sidebar) :
                        $out[$sidebar_id] = $sidebar['name'];
                    endforeach;

                    return $out ?? [];
                }
            }

            if (!function_exists('gostudy_quick_tip')) {
                /**
                 * Render string as a QuickTip element.
                 *
                 * @return string
                 */
                function gostudy_quick_tip($string)
                {
                    return sprintf(
                        '<span class="gostudy-tip">'
                            . '<i class="tip-icon el el-question-sign"></i>'
                            . '<span class="tip-content">%s</span>'
                            . '</span>',
                        $string
                    );
                }
            }
        }

        /**
         * Declaration of Theme specific functions,
         * which be called via filters.
         */
        private static function declare_theme_filters()
        {
            if (!function_exists('gostudy_tiny_mce_before_init')) {
                function gostudy_tiny_mce_before_init($settings)
                {
                    $settings['theme_advanced_blockformats'] = 'p,h1,h2,h3,h4';

                    $style_formats = [
                        [
                            'title' => esc_html__('Dropcap', 'gostudy'),
                            'items' => [
                                [
                                    'title' => esc_html__('Primary Text Color', 'gostudy'),
                                    'inline' => 'span',
                                    'classes' => 'dropcap-bg primary',
                                ],
                                [
                                    'title' => esc_html__('Secondary Text Color', 'gostudy'),
                                    'inline' => 'span',
                                    'classes' => 'dropcap-bg secondary',
                                ],
                                [
                                    'title' => esc_html__('Primary Background Color', 'gostudy'),
                                    'inline' => 'span',
                                    'classes' => 'dropcap-bg primary alt',
                                ],
                                [
                                    'title' => esc_html__('Secondary Background Color', 'gostudy'),
                                    'inline' => 'span',
                                    'classes' => 'dropcap-bg secondary alt',
                                ],
                            ],
                        ],
                        [
                            'title' => esc_html__('Highlighter', 'gostudy'),
                            'items' => [
                                [
                                    'title' => esc_html__('Primary Color', 'gostudy'),
                                    'inline' => 'span',
                                    'classes' => 'highlighter primary',
                                ],
                                [
                                    'title' => esc_html__('Secondary Color', 'gostudy'),
                                    'inline' => 'span',
                                    'classes' => 'highlighter secondary',
                                ],
                                [
                                    'title' => esc_html__('Header Color', 'gostudy'),
                                    'inline' => 'span',
                                    'classes' => 'highlighter header',
                                ],
                            ],
                        ],
                        [
                            'title' => esc_html__('Font Family', 'gostudy'),
                            'items' => [
                                [
                                    'title' => esc_html__('Header Font Family', 'gostudy'),
                                    'inline' => 'span',
                                    'classes' => 'theme-header-font',
                                ],
                            ],
                        ],
                        [
                            'title' => esc_html__('Font Weight', 'gostudy'),
                            'items' => [
                                [
                                    'title' => esc_html__('Default', 'gostudy'),
                                    'inline' => 'span',
                                    'styles' => ['font-weight' => 'inherit'],
                                ], [
                                    'title' => esc_html__('Lightest (100)', 'gostudy'),
                                    'inline' => 'span',
                                    'styles' => ['font-weight' => '100'],
                                ], [
                                    'title' => esc_html__('Lighter (200)', 'gostudy'),
                                    'inline' => 'span',
                                    'styles' => ['font-weight' => '200'],
                                ], [
                                    'title' => esc_html__('Light (300)', 'gostudy'),
                                    'inline' => 'span',
                                    'styles' => ['font-weight' => '300'],
                                ], [
                                    'title' => esc_html__('Normal (400)', 'gostudy'),
                                    'inline' => 'span',
                                    'styles' => ['font-weight' => '400'],
                                ], [
                                    'title' => esc_html__('Medium (500)', 'gostudy'),
                                    'inline' => 'span',
                                    'styles' => ['font-weight' => '500'],
                                ], [
                                    'title' => esc_html__('Semi-Bold (600)', 'gostudy'),
                                    'inline' => 'span',
                                    'styles' => ['font-weight' => '600'],
                                ], [
                                    'title' => esc_html__('Bold (700)', 'gostudy'),
                                    'inline' => 'span',
                                    'styles' => ['font-weight' => '700'],
                                ], [
                                    'title' => esc_html__('Bolder (800)', 'gostudy'),
                                    'inline' => 'span',
                                    'styles' => ['font-weight' => '800'],
                                ], [
                                    'title' => esc_html__('Extra Bold (900)', 'gostudy'),
                                    'inline' => 'span',
                                    'styles' => ['font-weight' => '900'],
                                ],
                            ]
                        ],
                        [
                            'title' => esc_html__('List Style', 'gostudy'),
                            'items' => [
                                [
                                    'title' => esc_html__('Dot Primary Color', 'gostudy'),
                                    'selector' => 'ul',
                                    'classes' => 'gostudy_list gostudy_dot',
                                ], [
                                    'title' => esc_html__('Dot Secondary Color', 'gostudy'),
                                    'selector' => 'ul',
                                    'classes' => 'gostudy_list gostudy_dot secondary',
                                ], [
                                    'title' => esc_html__('Check Primary Color', 'gostudy'),
                                    'selector' => 'ul',
                                    'classes' => 'gostudy_list gostudy_check',
                                ], [
                                    'title' => esc_html__('Check Secondary Color', 'gostudy'),
                                    'selector' => 'ul',
                                    'classes' => 'gostudy_list gostudy_check secondary',
                                ], [
                                    'title' => esc_html__('Check Text Color', 'gostudy'),
                                    'selector' => 'ul',
                                    'classes' => 'gostudy_list gostudy_check text_color',
                                ], [
                                    'title' => esc_html__('Plus', 'gostudy'),
                                    'selector' => 'ul',
                                    'classes' => 'gostudy_list gostudy_plus',
                                ], [
                                    'title' => esc_html__('Hyphen', 'gostudy'),
                                    'selector' => 'ul',
                                    'classes' => 'gostudy_list gostudy_hyphen',
                                ], [
                                    'title' => esc_html__('Right Style', 'gostudy'),
                                    'selector' => 'ul.gostudy_list',
                                    'classes' => 'icon_right',
                                ], [
                                    'title' => esc_html__('Disable Line - Lighter', 'gostudy'),
                                    'selector' => 'ul.gostudy_list li',
                                    'classes' => 'gostudy_disable',
                                ], [
                                    'title' => esc_html__('Disable Line - Darker', 'gostudy'),
                                    'selector' => 'ul.gostudy_list li',
                                    'classes' => 'gostudy_disable_dark',
                                ], [
                                    'title' => esc_html__('No List Style', 'gostudy'),
                                    'selector' => 'ul',
                                    'classes' => 'no-list-style',
                                ],
                            ]
                        ],
                    ];

                    $settings['style_formats'] = str_replace('"', "'", json_encode($style_formats));
                    $settings['extended_valid_elements'] = 'span[*],a[*],i[*]';

                    return $settings;
                }
            }

            if (!function_exists('gostudy_comment_form_fields')) {
                function gostudy_comment_form_fields($fields)
                {
                    $new_fields = [];

                    $myorder = ['author', 'email', 'url', 'comment'];

                    foreach ($myorder as $key) {
                        $new_fields[$key] = $fields[$key] ?? '';
                        unset($fields[$key]);
                    }

                    if ($fields) {
                        foreach ($fields as $key => $val) {
                            $new_fields[$key] = $val;
                        }
                    }

                    return $new_fields;
                }
            }

            if (!function_exists('gostudy_categories_postcount_filter')) {
                function gostudy_categories_postcount_filter($variable)
                {
	                if (strpos($variable,'</a> (')) {
		                $variable = str_replace('</a> (', '<span class="post_count">', $variable);
		                $variable = str_replace('</a>&nbsp;(', '<span class="post_count">', $variable);
		                $variable = str_replace(')', '</span></a>', $variable);
	                } else {
		                $variable = str_replace('</a> <span class="count">(', '<span class="post_count">', $variable);
		                $variable = str_replace(')', '</span></a>', $variable);
	                }

                    $pattern1 = '/cat-item-\d+/';
                    preg_match_all($pattern1, $variable, $matches);
                    if (isset($matches[0])) {
                        foreach ($matches[0] as $value) {
                            $int = (int) str_replace('cat-item-', '', $value);
                            $icon_image_id = get_term_meta($int, 'category-icon-image-id', true);
                            if (!empty($icon_image_id)) {
                                $icon_image = wp_get_attachment_image_src($icon_image_id, 'full');
                                $icon_image_alt = get_post_meta($icon_image_id, '_wp_attachment_image_alt', true);
                                $replacement = '$1<img class="cats_item-image" src="' . esc_url($icon_image[0]) . '" alt="' . (!empty($icon_image_alt) ? esc_attr($icon_image_alt) : '') . '"/>';
                                $pattern = '/(cat-item-' . $int . '+.*?><a.*?>)/';
                                $variable = preg_replace($pattern, $replacement, $variable);
                            }
                        }
                    }

                    return $variable;
                }
            }

            if (!function_exists('gostudy_render_archive_widgets')) {
                function gostudy_render_archive_widgets(
                    $link_html,
                    $url,
                    $text,
                    $format,
                    $before,
                    $after
                ) {
                    $text = wptexturize($text);
                    $url  = esc_url($url);

                    if ('link' == $format) {
                        $link_html = "\t<link rel='archives' title='" . esc_attr($text) . "' href='$url' />\n";
                    } elseif ('option' == $format) {
                        $link_html = "\t<option value='$url'>$before $text $after</option>\n";
                    } elseif ('html' == $format) {

                        $after = str_replace('(', '', $after);
                        $after = str_replace(' ', '', $after);
                        $after = str_replace('&nbsp;', '', $after);
                        $after = str_replace(')', '', $after);

                        $after = !empty($after) ? ' <span class="post_count">' . esc_html($after) . '</span> ' : '';

                        $link_html = '<li>' . esc_html($before) . '<a href="' . esc_url($url) . '">' . esc_html($text) . $after . '</a></li>';
                    } else { // custom
                        $link_html = "\t$before<a href='$url'>$text</a>$after\n";
                    }

                    return $link_html;
                }
            }

            if (!function_exists('gostudy_header_enable')) {
                function gostudy_header_enable()
                {
                    $header_switch = Gostudy_Theme_Helper::get_option('header_switch');
                    if (empty($header_switch)) {
                        return false;
                    }

                    $id = !is_archive() ? get_queried_object_id() : 0;

                    if (
                        class_exists('RWMB_Loader')
                        && $id !== 0
                        && rwmb_meta('mb_customize_header_layout') == 'hide'
                    ) {
                        // Don't render header if in metabox set to hide it.
                        return false;
                    }

                    // hide if 404 page
                    $page_not_found = Gostudy_Theme_Helper::get_option('404_show_header');
                    $layout_template = Gostudy_Theme_Helper::get_option('404_page_type');
                    if (is_404() && (!(bool) $page_not_found || $layout_template === 'custom')) {
                        return;
                    }

                    return true;
                }
            }

            if (!function_exists('gostudy_page_title_enable')) {
                function gostudy_page_title_enable()
                {
                    $id = get_queried_object_id();

	                $output['mb_page_title_switch'] = '';
	                if (is_404()) {
                        if(Gostudy_Theme_Helper::get_option('404_page_type') === 'default'){
                            $output['page_title_switch'] = Gostudy_Theme_Helper::get_option('404_page_title_switcher') ? 'on' : 'off';
                        }else{
                            $output['page_title_switch'] = 'off';
                        }
	                } else {
		                $output['page_title_switch'] = Gostudy_Theme_Helper::get_option('page_title_switch') ? 'on' : 'off';
		                if (class_exists('RWMB_Loader') && $id !== 0) {
			                $output['mb_page_title_switch'] = rwmb_meta('mb_page_title_switch');
		                }
	                }

                    $output['single'] = ['type' => '', 'layout' => ''];

                    /**
                     * Check the Post Type
                     *
                     * Aimed to prevent Page Title rendering for the following pages:
                     *	- blog single type 3;
                     *
                     * @since 1.0.0
                     */
                    if (
                        get_post_type($id) == 'post'
                        && is_single()
                    ) {
                        $output['single']['type'] = 'post';
                        $output['single']['layout'] = Gostudy_Theme_Helper::get_mb_option('single_type_layout', 'mb_post_layout_conditional', 'custom');
                        if ($output['single']['layout'] === '3') {
                            $output['page_title_switch'] = 'off';
                        }
                    }

                    if (isset($output['mb_page_title_switch']) && $output['mb_page_title_switch'] == 'on') {
                        $output['page_title_switch'] = 'on';
                    }

                    if (
                        is_home()
                        || is_front_page()
                        || isset($output['mb_page_title_switch']) && $output['mb_page_title_switch'] == 'off'
                    ) {
                        $output['page_title_switch'] = 'off';
                    }

                    return $output;
                }
            }

            if (!function_exists('gostudy_after_main_content')) {
                function gostudy_after_main_content()
                {
                    global $gostudy_dynamic_css;

                    $scroll_up = Gostudy_Theme_Helper::get_option('scroll_up');
                    $scroll_up_as_text = Gostudy_Theme_Helper::get_option('scroll_up_appearance');
                    $scroll_up_text = Gostudy_Theme_Helper::get_option('scroll_up_text');

                    // Page Socials
                    if (
                        is_page()
                        && function_exists('rt_theme_helper')
                    ) {
                        // ↓ Conditions Check
                        $render_socials = true;
                        if (
                            class_exists('WooCommerce')
                            && (is_cart() || is_checkout())
                        ) {
                            // exclude Cart and Checkout pages
                            $render_socials = false;
                        }
                        if ($render_socials) {
                            $render_socials = Gostudy_Theme_Helper::get_option('show_soc_icon_page');
                        }
                        if (
                            class_exists('RWMB_Loader')
                            && get_queried_object_id() !== 0
                        ) {
                            switch (rwmb_meta('mb_customize_soc_shares')) {
                                case 'on':
                                    $render_socials = true;
                                    break;
                                case 'off':
                                    $render_socials = false;
                                    break;
                            }
                        }
                        // ↑ conditions check

                        if ($render_socials) {
                            echo rt_theme_helper()->render_social_shares();
                        }
                    }

                    // Scroll Up Button
                    if ($scroll_up) {
                        echo '<a href="#" id="scroll_up">',
                            $scroll_up_as_text ? $scroll_up_text : '',
                            '</a>';
                    }

                    // Dynamic Styles
                    if (!empty($gostudy_dynamic_css['style'])) {
                        echo '<span',
                            ' id="gostudy-footer-inline-css"',
                            ' class="dynamic_styles-footer"',
                            '>',
                            $gostudy_dynamic_css['style'],
                            '</span>';
                    }
                }
            }

            if (!function_exists('gostudy_footer_enable')) {
                function gostudy_footer_enable()
                {
                    $output = [];
                    $output['footer_switch'] = Gostudy_Theme_Helper::get_option('footer_switch');
                    $output['copyright_switch'] = Gostudy_Theme_Helper::get_option('copyright_switch');

                    if (class_exists('RWMB_Loader') && get_queried_object_id() !== 0) {
                        $output['mb_footer_switch'] = rwmb_meta('mb_footer_switch');
                        $output['mb_copyright_switch'] = rwmb_meta('mb_copyright_switch');

                        if ($output['mb_footer_switch'] == 'on') {
                            $output['footer_switch'] = true;
                        } elseif ($output['mb_footer_switch'] == 'off') {
                            $output['footer_switch'] = false;
                        }

                        if ($output['mb_copyright_switch'] == 'on') {
                            $output['copyright_switch'] = true;
                        } elseif ($output['mb_copyright_switch'] == 'off') {
                            $output['copyright_switch'] = false;
                        }
                    }

                    // Hide on 404 page
                    $page_not_found = Gostudy_Theme_Helper::get_option('404_show_footer');
                    $layout_template = Gostudy_Theme_Helper::get_option('404_page_type');
                    if (
                        is_404()
                        && (!$page_not_found || $layout_template === 'custom')
                    ) {
                        $output['footer_switch'] = $output['copyright_switch'] = false;
                    }

                    return $output;
                }
            }
        }
    }

    new Gostudy_Global_Functions();
}
