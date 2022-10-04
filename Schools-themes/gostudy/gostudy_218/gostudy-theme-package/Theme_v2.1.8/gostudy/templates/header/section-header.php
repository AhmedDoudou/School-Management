<?php
defined('ABSPATH') || exit;

if (!class_exists('Gostudy_Get_Header')) {
    class Gostudy_Get_Header
    {
        public static $search_form_counter = 0;

        protected $html_render = 'bottom';
        protected $id;
        protected $side_area_enabled;

        protected $header_type;
        protected $header_page_select_id;
        protected $header_sticky_page_select_id;
        protected $enable_sign_in_form = false;

        private static $instance = null;
        private $get_menu_logo;

        public static function get_instance()
        {
            if (null == self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct()
        {
            $this->init();
        }

        public function header_vars()
        {
            $this->id = !is_category() ? get_queried_object_id() : 0;

            /**
            * Header Template
            *
            *
            * @access public
            * @since 1.0.0
            */
            $this->header_type = Gostudy_Theme_Helper::get_option('header_type');

            if ($this->header_type === 'custom') {

                $header_page_select = Gostudy_Theme_Helper::get_option('header_page_select');

                if ($header_page_select) {
                    $this->header_page_select_id = intval($header_page_select);
                    $this->header_page_select_id = $this->multiLanguageSupport( $this->header_page_select_id );
                }
            }

            /**
            * Sticky Header Template
            *
            *
            * @access public
            * @since 1.0.0
            */

            if (Gostudy_Theme_Helper::get_mb_option('header_sticky', 'mb_customize_header_layout', 'custom') == '1') {
                $header_sticky_page_select = Gostudy_Theme_Helper::get_option('header_sticky_page_select');

                if (!empty($header_sticky_page_select)) {
                    $this->header_sticky_page_select_id = intval($header_sticky_page_select);
                    $this->header_sticky_page_select_id = $this->multiLanguageSupport( $this->header_sticky_page_select_id );
                }
            }

            // RWMB opions
            if (
                class_exists('RWMB_Loader')
                && $this->id !== 0
                && rwmb_meta('mb_customize_header_layout') == 'custom'
            ) {
                if (rwmb_meta('mb_header_content_type') !== 'default') {
                    $this->header_type = 'custom';
                    $this->header_page_select_id = (int) rwmb_meta('mb_customize_header');
                    $this->header_page_select_id = $this->multiLanguageSupport( $this->header_page_select_id );
                }

                if (rwmb_meta('mb_sticky_header_content_type') !== 'default') {
                    $this->header_sticky_page_select_id = (int) rwmb_meta('mb_customize_sticky_header');
                    $this->header_sticky_page_select_id = $this->multiLanguageSupport( $this->header_sticky_page_select_id );
                }
            }
        }

        public function init()
        {
            $this->header_vars();
            $this->require_components();
            $this->header_render_html();
        }

        public function require_components()
        {
            require_once get_theme_file_path('/templates/header/components/logo.php');
        }

        /**
         * Multi Language Support
         *
         *
         * @access public
         * @since 1.0.0
         */
        public function multiLanguageSupport($id)
        {

            if (class_exists('Polylang') && function_exists('pll_current_language')) {
                $currentLanguage = pll_current_language();
                $translations = PLL()->model->post->get_translations($id);

                $polylang_id = $translations[$currentLanguage] ?? '';
                $id = !empty($polylang_id) ? $polylang_id : $id;
            }

            if (class_exists('SitePress')) {
                $id = wpml_object_id_filter($id, 'header', false, ICL_LANGUAGE_CODE);
            }

            return $id;
        }

        /**
         * Generate header class
         *
         *
         * @access public
         * @since 1.0.0
         */
        public function header_class()
        {
            $header_shadow = Gostudy_Theme_Helper::get_option('header_shadow');
            $header_on_bg = Gostudy_Theme_Helper::get_option('header_on_bg');
            $header_class = '';

            if ($this->header_type === 'custom') {
                if (
                    !empty($this->header_page_select_id)
                    && did_action('elementor/loaded')
                ) {
                    // Get the page settings manager
                    $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');

                    // Get the settings model for header post
                    $page_settings_model = $page_settings_manager->get_model($this->header_page_select_id);

                    $header_on_bg = $page_settings_model->get_settings('header_on_bg');
                }
            } elseif ($header_shadow) {
                $header_class .= ' header_shadow';
            }

            if (
                get_option('show_on_front') === 'posts'
                && (is_home() || is_front_page())
            ) {
                $header_on_bg = false;
            }

            if ($header_on_bg) {
                $header_class .= ' header_overlap';
            }

            return $header_class;
        }

        /**
         * Generate header editor
         *
         *
         * @access public
         * @since 1.0.0
         */
        public function header_bar_editor($location = null, $position = null)
        {
            if (!$position) return;

            /*
             * Define Theme options and field configurations.
            */

            ${'header_'.$position.'_editor'} = Gostudy_Theme_Helper::get_option($location.'_header_bar_'.$position.'_editor');
            $html_render = ${'header_'.$position.'_editor'};
            // Header Bar HTML Editor render
            $html = '';
            if (!empty($html_render)) {
                $html .= "<div class='".esc_attr($location)."_header ".esc_attr($position)."_editor header_render_editor header_render'>";
                    $html .= '<div class="wrapper">';
                            $html .= do_shortcode( $html_render );
                    $html .= '</div>';
                $html .= '</div>';
            }

            return $html;
        }

        /**
         * Generate header delimiter
         *
         *
         * @access public
         * @since 1.0.0
         */
        public function header_bar_delimiter($k = null)
        {
            if (!$k) return;

            /*
            * Define Theme options and field configurations.
            */

            $get_number = (int) filter_var($k, FILTER_SANITIZE_NUMBER_INT);
            $height = Gostudy_Theme_Helper::get_option('bottom_header_delimiter'.$get_number.'_height');
            $width = Gostudy_Theme_Helper::get_option('bottom_header_delimiter'.$get_number.'_width');

            $bg_color = Gostudy_Theme_Helper::get_option('bottom_header_delimiter'.$get_number.'_bg');

            $margin = Gostudy_Theme_Helper::get_option('bottom_header_delimiter'.$get_number.'_margin');

            $margin_left = !empty($margin['margin-left']) ? (int)$margin['margin-left'] : '';
            $margin_right = !empty($margin['margin-right']) ? (int)$margin['margin-right'] : '';

            $custom_sticky = '';
            if ($this->html_render === 'sticky') {
                $custom_sticky = Gostudy_Theme_Helper::get_option('bottom_header_delimiter'.$get_number.'_sticky_custom');
                if (!empty($custom_sticky)) {
                    $bg_color = Gostudy_Theme_Helper::get_option('bottom_header_delimiter'.$get_number.'_sticky_color');
                    $height  = Gostudy_Theme_Helper::get_option('bottom_header_delimiter'.$get_number.'_sticky_height');
                }
            }

            // Header Bar Delimiter render
            $style = '';
            if (is_array($height)) {
                $style .= 'height: '.esc_attr((int) $height['height'] ).'px;';
            }

            if (is_array($width)) {
                $style .= 'width: '.esc_attr((int) $width['width'] ).'px;';
            }

            if (!empty($bg_color['rgba'])) {
                $style .= 'background-color: '.esc_attr($bg_color['rgba']).';';
            }

            if (!empty($margin_left)) {
                $style .= 'margin-left:'.esc_attr((int) $margin_left).'px;';
            }

            if (!empty($margin_right)) {
                $style .= 'margin-right:'.esc_attr((int) $margin_right).'px;';
            }

            echo '<div class="delimiter-wrapper">',
                '<div class="delimiter"'.(!empty($style) ? ' style="'.$style.'"' : '').'></div>',
            '</div>';
        }


        /**
         * Generate header button
         *
         *
         * @access public
         * @since 1.0.0
         */
        public function header_bar_button($k = null)
        {
            if (!$k) {
                return;
            }

            /*
             * Define Theme options and field configurations.
             */

            $get_number = (int) filter_var($k, FILTER_SANITIZE_NUMBER_INT);
            $button_text = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_title');

            $link = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_link');
            $target = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_target');

            $size = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_size') ?? 'md';

            $customize = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_custom');
            $customize = empty($customize) ? 'def' : 'color';

            $text_color_idle = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_color_txt')['rgba'] ?? '';
            $text_color_hover = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_hover_color_txt')['rgba'] ?? '';

            $border_color = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_border')['rgba'] ?? '';
            $border_color_hover = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_hover_border')['rgba'] ?? '';
            $border_radius = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_radius') ?? false;

            $bg_color = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_bg')['rgba'] ?? '';
            $bg_color_hover = Gostudy_Theme_Helper::get_option('bottom_header_button'.$get_number.'_hover_bg')['rgba'] ?? '';

            $button_css_id = uniqid('gostudy_button_');

            $settings = [
                'link' => [
                    'url' => $link,
                    'is_external' => $target,
                    'nofollow' => '',
                ],
                'button_css_id' => $button_css_id,
            ];

            // Start Custom CSS
            ob_start();
            if ($customize == 'color') {
                echo "#$button_css_id {
                        color: ".($text_color_idle ? esc_attr($text_color_idle) : 'transparent').";
                    }";
                echo "#$button_css_id:hover {
                        color: ".($text_color_hover ? esc_attr($text_color_hover) : 'transparent').";
                    }";
                $border_color = $border_color ? esc_attr($border_color) : 'transparent';
                echo "#$button_css_id {
                        border-color: $border_color;
                        background-color: $bg_color;
                    }";
                echo "#$button_css_id:hover {
                        border-color: ".($border_color_hover ? esc_attr($border_color_hover) : 'transparent').";
                        background-color: $bg_color_hover;
                    }";
            }
            $styles = ob_get_clean();

            // Register css
            if ($styles) Gostudy_Theme_Helper::enqueue_css($styles);

            unset($this->render_attributes);


            // Attributes
            $this->add_render_attribute('wrapper', 'class', 'button-wrapper');

            if (!empty($settings['link']['url'])) {
                $this->add_render_attribute('button', 'href', $settings['link']['url']);
                $this->add_render_attribute('button', 'class', 'elementor-button-link');
                if ($settings['link']['is_external']) {
                    $this->add_render_attribute('button', 'target', '_blank');
                }
                if ($settings['link']['nofollow']) {
                    $this->add_render_attribute('button', 'rel', 'nofollow');
                }
            }

            $this->add_render_attribute('button', 'id', $button_css_id);
            $this->add_render_attribute('button', 'class', 'rt-button');
            $this->add_render_attribute('button', 'class', 'btn-size-' . $size);
            $this->add_render_attribute('button', 'role', 'button');

            if (isset($settings['hover_animation'])) {
                $this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['hover_animation']);
            }

            if ($border_radius = (int) $border_radius) {
                $this->add_render_attribute('button', 'style', 'border-radius: '.$border_radius.';' );
            }

            // Render
            echo '<div class="header_button">';
                echo '<div class="wrapper">';

                    echo '<div', $this->get_render_attribute_string('wrapper'), '>',
                        '<a', $this->get_render_attribute_string('button'), '>',
                            $this->render_text($button_text),
                        '</a>',
                    '</div>';

                echo '</div>';
            echo '</div>';
        }


        public function render_text($button_text)
        {
            $this->add_render_attribute( [
                'content-wrapper' => [
                    'class' => 'button-content-wrapper',
                ],
                'text' => [
                    'class' => 'rt-button-text',
                ],
            ] );

            // Render
            echo '<span', $this->get_render_attribute_string('content-wrapper'), '>',
                '<span', $this->get_render_attribute_string('text'), '>',
                    esc_html($button_text),
                '</span>',
            '</span>';
        }


        /**
         * Add render attribute.
         *
         * Used to add attributes to a specific HTML element.
         *
         * The HTML tag is represented by the element parameter, then you need to
         * define the attribute key and the attribute key. The final result will be:
         * `<element attribute_key="attribute_value">`.
         *
         * Example usage:
         *
         * `$this->add_render_attribute( 'wrapper', 'class', 'custom-widget-wrapper-class' );`
         * `$this->add_render_attribute( 'widget', 'id', 'custom-widget-id' );`
         * `$this->add_render_attribute( 'button', [ 'class' => 'custom-button-class', 'id' => 'custom-button-id' ] );`
         *
         * @since 1.0.0
         * @access public
         *
         * @param array|string $element   The HTML element.
         * @param array|string $key       Optional. Attribute key. Default is null.
         * @param array|string $value     Optional. Attribute value. Default is null.
         * @param bool         $overwrite Optional. Whether to overwrite existing
         *                                attribute. Default is false, not to overwrite.
         *
         * @return Element_Base Current instance of the element.
         */
        public function add_render_attribute($element, $key = null, $value = null, $overwrite = false)
        {
            if (is_array($element)) {
                foreach ($element as $element_key => $attributes) {
                    $this->add_render_attribute($element_key, $attributes, null, $overwrite);
                }

                return $this;
            }

            if (is_array($key)) {
                foreach ($key as $attribute_key => $attributes) {
                    $this->add_render_attribute($element, $attribute_key, $attributes, $overwrite);
                }

                return $this;
            }

            if (empty( $this->render_attributes[ $element ][ $key ] )) {
                $this->render_attributes[ $element ][ $key ] = [];
            }

            settype($value, 'array');

            if ($overwrite) {
                $this->render_attributes[ $element ][ $key ] = $value;
            } else {
                $this->render_attributes[ $element ][ $key ] = array_merge( $this->render_attributes[ $element ][ $key ], $value );
            }

            return $this;
        }

        public function get_render_attribute_string($element)
        {
            if (empty($this->render_attributes[$element])) {
                return '';
            }

            return ' ' . Gostudy_Theme_Helper::render_html_attributes( $this->render_attributes[ $element ] );
        }

        /**
        * Generate header spacer
        *
        *
        * @access public
        * @since 1.0.0
        */
        public function header_bar_spacer($location = null, $key = null)
        {
            if (!$key) return;

            /*
             * Define Theme options and field configurations.
             */

            $get_number = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $spacer = Gostudy_Theme_Helper::get_option($location.'_header_spacer'.$get_number);
            // Header Bar Spacer render
            $html = '';
            if (is_array($spacer)) {
                $html .= "<div class='header_spacing spacer_".$get_number."' style='width:".esc_attr( (int) $spacer['width'] )."px;'>";
                $html .= '</div>';
            }

            return $html;
        }

        public function header_bar_spacer_height($location = null, $key = null)
        {
            if (!$key) return;

            /*
             * Define Theme options and field configurations.
             */

            $get_number = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $spacer = Gostudy_Theme_Helper::get_option($location.'_header_spacer'.$get_number);
            // Header Bar Spacer render
            $html = '';
            if (is_array($spacer)) {
                $html .= "<div class='header_spacing spacer_".$get_number."' style='display:block;height:".esc_attr( (int) $spacer['width'] )."px;'>";
                $html .= '</div>';
            }

            return $html;
        }

        /**
         * Generate header builder layout
         *
         * @access public
         * @since 1.0.0
         */
        public function build_header_layout($section = 'bottom')
        {
            $sticky = '';

            if ($this->html_render == 'sticky') {
                if (!empty($this->header_sticky_page_select_id)) {
                    $sticky = '_sticky';
                    $this->header_type = 'custom';
                }
                $section = 'bottom';
            }

            if ($this->header_type === 'custom' && $section === 'bottom') {
                require_once get_theme_file_path('/templates/header/elementor-builder/header-builder' . $sticky . '.php');
            } else {
                $this->header_default($section);
            }
        }

        public function header_default($section = 'bottom')
        {
            $header_layout = Gostudy_Theme_Helper::get_option($section . '_header_layout');
            $lavalamp_active = Gostudy_Theme_Helper::get_option('lavalamp_active');

            // Get item from recycle bin
            $j = 0;
            $header_layout_top = $header_layout_middle = $header_layout_bottom = [];

            // Build Row Item
            $counter = 1;
            if ($section == 'bottom') {
                $header_layout = array_slice($header_layout, 1);
                // $count = count($header_layout);
                $half = 3;
                for ($i = 0; $i < 3; $i++) {
                    switch ($i) {
                        case 0:
                            $header_layout_top = array_slice($header_layout, $j, $half);
                            break;
                        case 1:
                            $header_layout_middle = array_slice($header_layout, $j, $half);
                            break;
                        case 2:
                            $header_layout_bottom = array_slice($header_layout, $j, $half);
                            break;
                    }

                    $j = $j+$half;
                }

                // RT Header Builder Row
                $counter = 3;
            }

            /**
            * Generate sticky builder(default)
            */
            $inc_sticky = 0;
            $sticky_present_element = false;
            $sticky_last_row = '';
            $sticky_key_last_row = [];

            for ($i = 1; $i <= $counter; $i++) {
                if ($section == 'bottom') {
                    switch ($i) {
                        case 1:
                            $sticky_loc = '_top';
                            break;
                        case 2:
                            $sticky_loc = '_middle';
                            break;
                        case 3:
                            $sticky_loc = '_bottom';
                            break;
                    }
                    $sticky_header_layout = ${"header_layout" . $sticky_loc};

                    // Disabled Sticky Options
                    $disabled_sticky = false;
                    foreach ($sticky_header_layout as $s => $d) {
                        if (
                            isset($sticky_header_layout[$s]['disable_row'])
                            && $sticky_header_layout[$s]['disable_row'] == 'true'
                        ) {
                            $disabled_sticky = true;
                            continue;
                        }
                    }
                    if (!$disabled_sticky) {
                        foreach ($sticky_header_layout as $key => $v) {
                            if (isset($sticky_header_layout[$key]['disable_row'])) {
                                unset($sticky_header_layout[$key]['disable_row']);
                            }
                            if (
                                count($sticky_header_layout[$key]) == 1
                                && empty($sticky_header_layout[$key]['placebo'])
                                || count($sticky_header_layout[$key]) > 1
                            ) {
                                $sticky_present_element = true;
                                $sticky_key_last_row[] = $key;
                            }
                        }
                    }

                } else {
                    $sticky_present_element = true;
                }

                if (
                    !empty($sticky_header_layout)
                    && $sticky_present_element
                    && $this->html_render == 'sticky'
                ) {
                    $inc_sticky++;
                    $sticky_present_element = false;
                }
            }

            if (is_array($sticky_key_last_row)) {
                $last_element = end($sticky_key_last_row);
                if ($last_element) {
                    switch ($last_element) {
                        case array_key_exists($last_element, $header_layout_top):
                            $sticky_last_row = '_top';
                            break;
                        case array_key_exists($last_element, $header_layout_middle):
                            $sticky_last_row = '_middle';
                            break;
                        case array_key_exists($last_element, $header_layout_bottom):
                            $sticky_last_row = '_bottom';
                            break;
                    }
                }
            }
            /**
            * End Generate sticky builder(default)
            */

            $location = '';
            $has_element = false;
            $counter = $inc_sticky > 1 ? 1 : $counter;

            for ($i = 1; $i <= $counter; $i++) {
                if ($section == 'bottom') {
                    switch ($i) {
                        case 1: $location = '_top'; break;
                        case 2: $location = '_middle'; break;
                        case 3: $location = '_bottom'; break;
                    }

                    if ($inc_sticky > 1) {
                        $location = $sticky_last_row;
                    }

                    $header_layout = ${"header_layout" . $location};

                    // Disabled Row Options
                    $disabled_row = false;
                    foreach ($header_layout as $s => $d) {
                        if (
                            isset($header_layout[$s]['disable_row'])
                            && $header_layout[$s]['disable_row'] == 'true'
                        ) {
                            $disabled_row = true;
                            continue;
                        }
                    }

                    if (!$disabled_row) {
                        foreach ($header_layout as $key => $v) {
                            if (isset($header_layout[$key]['disable_row'])) {
                                unset($header_layout[$key]['disable_row']);
                            }
                            if (
                                count($header_layout[$key]) == 1 && empty($header_layout[$key]['placebo'])
                                || count($header_layout[$key]) > 1
                            ) {
                                $has_element = true;
                            }
                        }
                    }

                } else {
                    $has_element = true;
                }

                if (!empty($header_layout) && $has_element) {
                    switch ($section) {
                        case 'mobile_content':
                            foreach ($header_layout as $part => $value) if ($part != 'items') {
                                if (
                                    !empty($header_layout[$part])
                                    && count($header_layout[$part]) == 1
                                    && empty($header_layout[$part]['placebo'])
                                    || count($header_layout[$part]) > 1
                                ) {
                                    foreach ($header_layout[$part] as $key => $value) if ($key != 'placebo') {
                                        switch ($key) {
                                            case 'item_search':
                                                echo '<div class="header_search search_mobile_menu">';
                                                    echo '<div class="header_search-field">';
                                                        get_search_form();
                                                    echo '</div>';
                                                echo '</div>';
                                                break;

                                            case 'logo':
                                                $menu_condition = $this->get_menu_logo ?? '';
                                                if ($menu_condition) unset($this->get_menu_logo);
                                                $logo_render = $this->get_logo($menu_condition, '_menu');
                                                echo !empty($logo_render) ? $logo_render : '';
                                                break;

                                            case 'menu':
                                                $menu = 'main_menu';
                                                if (Gostudy_Theme_Helper::get_option('custom_mobile_menu')) {
                                                    $custom_menu = true;
                                                    $menu = Gostudy_Theme_Helper::get_option('mobile_menu');
                                                }
                                                if (
                                                    class_exists('RWMB_Loader')
                                                    && $this->id !== 0
                                                    && rwmb_meta('mb_customize_header_layout') == 'custom'
                                                    && rwmb_meta('mb_mobile_menu_custom') == 'custom'
                                                ) {
                                                    $custom_menu = true;
                                                    $menu = rwmb_meta('mb_mobile_menu_header');
                                                }
                                                if (has_nav_menu($menu) || isset($custom_menu)) {
                                                    echo '<nav class="primary-nav">';
                                                        gostudy_main_menu($menu, false);
                                                    echo '</nav>';
                                                }
                                                break;

                                            case stripos($key, 'html') !== false:
                                                $this_header_bar_editor = $this->header_bar_editor('mobile', $key);
                                                echo !empty($this_header_bar_editor) ? $this->header_bar_editor('mobile', $key) : '';
                                                break;

                                            case 'wpml':
                                                if (class_exists('SitePress')) {
                                                    echo '<div class="sitepress_container">';
                                                        do_action('wpml_add_language_selector');
                                                    echo '</div>';
                                                }
                                                break;

                                            case stripos($key, 'spacer') !== false:
                                                $this_header_bar_spacer = $this->header_bar_spacer_height('mobile', $key);
                                                echo !empty($this_header_bar_spacer) ? $this->header_bar_spacer_height('mobile', $key) : '';
                                                break;
                                        }
                                    }
                                }
                            }
                            break;

                        default:
                            echo '<div class="rt-header-row rt-header-row-section', esc_attr($location), '"', $this->row_style_color($location, $section), '>';
                            echo '<div class="', esc_attr($this->row_width_class($location, $section)), '">';
                            echo '<div class="rt-header-row_wrapper"', $this->row_style_height($location, $section), '>';
                                foreach ($header_layout as $part => $value) {
                                    if (!empty($header_layout[$part]) && $part != 'items') {

                                        $area_name = '';
                                        switch ($part) {
                                            case stripos($part, 'center') !== false:
                                                $area_name = 'center';
                                                break;
                                            case stripos($part, 'left') !== false:
                                                $area_name = 'left';
                                                break;
                                            case stripos($part, 'right') !== false:
                                                $area_name = 'right';
                                                break;
                                        }
                                        $column_class = $this->column_class($location, $area_name);

                                        $class_area = 'position_' . $area_name . $location;

                                        echo "<div class='", esc_attr(sanitize_html_class($class_area)), " header_side", esc_attr($column_class), "'>";

                                        if (
                                            count($header_layout[$part]) == 1
                                            && empty($header_layout[$part]['placebo'])
                                            || count($header_layout[$part]) > 1
                                        ) {
                                            echo '<div class="header_area_container">';
                                            foreach ($header_layout[$part] as $key => $value) {
                                                if ($key != 'placebo' && $key != 'pos_column') {
                                                    switch ($key) {
                                                        case 'item_search':
                                                            $this->search($this->html_render, $location, $section);
                                                            $this->html_render == 'mobile' || self::$search_form_counter++; // mobile header forms doesn't count
                                                            break;

                                                        case 'cart':
                                                            if (class_exists('WooCommerce')) {
                                                                $this->cart($location, $section);
                                                            }
                                                            break;

                                                        case 'login':
                                                            $this->login_in($location, $section);

                                                        case 'ld_login':
                                                            if (class_exists('SFWD_LMS')) {
                                                                $this->ld_login_reg($location, $section);
                                                           }
                                                            break;

                                                        case 'login_join':
                                                                $this->login_join($location, $section);
                                                            break;

                                                        case 'profile_link':
                                                                $this->profile_link($location, $section);
                                                            break;

                                                        case 'logout_btn':
                                                                $this->logout_btn($location, $section);
                                                            break;

                                                        case 'sign_in':
                                                            if (class_exists('LearnPress')) {
                                                                $this->enable_sign_in_form = true;
                                                                $this->sign_in_button($location, $section);
                                                            }
                                                            break;

                                                        case 'side_panel':
                                                            $this->side_panel_enabled = true;
                                                            $this->get_side_panel($location, $section);
                                                            break;

                                                        case 'logo':
                                                            $logo_render = $this->get_logo();
                                                            echo !empty($logo_render) ? $logo_render : '';
                                                            break;

                                                        case 'menu':
                                                            echo '<nav class="primary-nav',
                                                                ($lavalamp_active == '1' ? ' menu_line_enable' : ''), '" ',
                                                                $this->row_style_height($location, $section),
                                                                '>';
                                                            if (has_nav_menu('main_menu')) {
                                                                gostudy_main_menu('main_menu');
                                                            }
                                                            echo '</nav>';
                                                            echo '<div class="hamburger-box">',
                                                                    '<div class="hamburger-inner">',
                                                                        '<span></span>',
                                                                        '<span></span>',
                                                                        '<span></span>',
                                                                    '</div>',
                                                            '</div>';
                                                            break;

                                                        case stripos($key, 'html') !== false:
                                                            $this_header_bar_editor = $this->header_bar_editor($section, $key);
                                                            echo !empty($this_header_bar_editor) ? $this->header_bar_editor($section, $key) : '';
                                                            break;

                                                        case 'wpml':
                                                            if (class_exists('SitePress')) {
                                                                echo '<div class="sitepress_container" ', $this->row_style_height($location, $section), '>';
                                                                    do_action('wpml_add_language_selector');
                                                                echo '</div>';
                                                            }
                                                            break;

                                                        case stripos($key, 'delimiter') !== false:
                                                            $this->header_bar_delimiter($key);
                                                            break;

                                                        case stripos($key,'button') !== false:
                                                            $this->header_bar_button($key);
                                                            break;

                                                        case stripos($key,'spacer') !== false:
                                                            $this_header_bar_spacer = $this->header_bar_spacer($section, $key);
                                                            echo !empty($this_header_bar_spacer) ? $this->header_bar_spacer($section, $key) : '';
                                                            break;
                                                    }
                                                }
                                            }
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                    }
                                }
                            echo '</div>';
                            echo '</div>';
                            echo '</div>'; // rt-header-row rt-header-row-section
                        break;
                    }
                    $has_element = false;
                }
            }
        }

        /**
        * Loop Header Row Style Color
        *
        *
        * @access private
        * @since 1.0.0
        */
        private function row_width_class($s = '_middle', $section)
        {
            $width_container = '';

            switch ($section) {
                case 'bottom':
                    $width_container = Gostudy_Theme_Helper::get_option('header'.$s.'_full_width');
                    break;
                case 'sticky':
                    $width_container = Gostudy_Theme_Helper::get_option('header_custom_sticky_full_width');
                    break;
                case 'mobile':
                    $width_container = Gostudy_Theme_Helper::get_option('header_mobile_full_width');
                    break;
            }

            return $width_container ? 'fullwidth-wrapper' : 'rt-container';
        }

        private function row_style_color($s = '_middle', $section)
        {
            $style = '';

            switch ($this->html_render) {
                case 'bottom':
                case 'sticky':
                    $header_background = Gostudy_Theme_Helper::get_option('header'.$s.'_background');
                    $header_bg_image = Gostudy_Theme_Helper::get_option('header'.$s.'_background_image')['url'] ?? '';

                    $header_color = Gostudy_Theme_Helper::get_option('header'.$s.'_color');
                    $header_bottom_border = Gostudy_Theme_Helper::get_option('header'.$s.'_bottom_border');
                    $header_border_height = Gostudy_Theme_Helper::get_option('header'.$s.'_border_height')['height'] ?? '';
                    $header_bottom_border_color = Gostudy_Theme_Helper::get_option('header'.$s.'_bottom_border_color');

                    $style = !empty($header_background['rgba']) ? 'background-color: '.esc_attr($header_background['rgba']).';' : '';

                    if ($header_bg_image) {
                        $style .= 'background-size: cover;'
                            . ' background-repeat: no-repeat;'
                            . ' background-image: url('.esc_attr($header_bg_image).');';
                    }

                    if (!empty($header_bottom_border)) {
                        $style .= $header_border_height ? 'border-bottom-width: '.(int) (esc_attr($header_border_height)).'px;' : '';
                        if (!empty($header_bottom_border_color['rgba'])) {
                            $style .= 'border-bottom-color: '.esc_attr($header_bottom_border_color['rgba']).';';
                        }

                        $style .= 'border-bottom-style: solid;';
                    }

                    $style .= !empty($header_color['rgba']) ? 'color: '.esc_attr($header_color['rgba']).';' : '';

                    $customize_width = Gostudy_Theme_Helper::get_option('header'.$s.'_max_width_custom');

                    if ($customize_width == '1') {
                        $max_width = Gostudy_Theme_Helper::get_option('header'.$s.'_max_width');
                        $max_width = $max_width['width'];

                        $style .= 'max-width: '.esc_attr((int) $max_width ).'px; margin-left: auto; margin-right: auto;';
                    }

                    break;
            }

            return !empty($style) ? ' style="' . $style . '"' : '';
        }

        /**
        * Loop Row Style Height
        *
        *
        * @access private
        * @since 1.0.0
        */
        private function row_style_height($s = '_middle', $section, $width = false)
        {
            $header_mobile_height = Gostudy_Theme_Helper::get_option('header_mobile_height')['height'] ?? false;
            $header_height = Gostudy_Theme_Helper::get_option('header'.$s.'_height')['height'] ?? false;

            $style = '';

            switch ($this->html_render) {
                case 'mobile':
                    if ($header_mobile_height) {
                        $style = 'height: '. (int) esc_attr($header_mobile_height) .'px;';
                        if ($width) {
                            $style .= ' width: '. (int) esc_attr($header_mobile_height) .'px;';
                        }
                    }
                    break;

                default:
                    if ($header_height) {
                        $style = 'height: '. (int) esc_attr($header_height) .'px;';

                        if ($width) {
                            $style .= ' width: '. (int) esc_attr($header_height) .'px;';
                        }
                    }
                    break;
            }

            return $style ? ' style="'. $style .'"' : '';
        }

        protected function side_panel_style_icon()
        {
            $icon_bg = Gostudy_Theme_Helper::get_option('bottom_header_side_panel_background')['rgba'] ?? '';
            $icon_color = Gostudy_Theme_Helper::get_option('bottom_header_side_panel_color')['rgba'] ?? '';

            $style = $icon_bg ? 'background-color: ' . esc_attr($icon_bg).';' : '';
            $style .= $icon_color ? 'color: ' . esc_attr($icon_color) . ';' : '';

            return $style ? ' style="' . $style . '"' : '';
        }

        /**
         * Loop column class
         *
         *
         * @access private
         * @since 1.0.0
         */
        private function column_class($s = '_middle', $area)
        {
            $dispay = Gostudy_Theme_Helper::get_option('header_column' . $s . '_' . $area . '_display');
            $v_align = Gostudy_Theme_Helper::get_option('header_column' . $s . '_' . $area . '_vert');
            $h_align = Gostudy_Theme_Helper::get_option('header_column' . $s . '_' . $area . '_horz');

            $column_class = !empty($dispay) ? ' display_' . $dispay : '';
            $column_class .= !empty($v_align) ? ' v_align_' . $v_align : '';
            $column_class .= !empty($h_align) ? ' h_align_' . $h_align : '';

            return $column_class;
        }

        /**
         * Generate header mobile menu
         *
         *
         * @access public
         * @since 1.0.0
         */
        public function build_header_mobile_menu()
        {
            $header_queries = Gostudy_Theme_Helper::get_option('header_mobile_queris');

            if (
                $this->header_type === 'custom'
                && !empty($this->header_page_select_id)
                && did_action('elementor/loaded')
            ) {
                // Get the page settings manager
                $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');

                // Get the settings model for header post
                $page_settings_model = $page_settings_manager->get_model($this->header_page_select_id);

                $header_queries = $page_settings_model->get_settings('mobile_breakpoint') ?? $header_queries;
            }

            $mobile_header_custom = Gostudy_Theme_Helper::get_option('mobile_header');
            $menu_occurrence = $mobile_header_custom ? Gostudy_Theme_Helper::get_option('mobile_position') : '';

            echo "<div class='mobile_nav_wrapper' data-mobile-width='$header_queries'>";
            echo '<div class="container-wrapper">';
                echo '<div class="rt-menu_overlay"></div>';

                echo '<div class="rt-menu_outer rt-perfect-container',
                    ($menu_occurrence ? ' menu-position_'.esc_attr($menu_occurrence) : ''), '">';

                    echo '<div class="rt-menu-outer_header">',
                        '<div class="mobile-hamburger-close">',
                            '<div class="hamburger-box">',
                                '<div class="hamburger-inner">',
                                    '<span></span>',
                                    '<span></span>',
                                    '<span></span>',
                                '</div>',
                            '</div>',
                        '</div>',
                    '</div>';

                    echo '<div class="rt-menu-outer_content">';
                        if (!empty($mobile_header_custom)) {
                            $this->get_menu_logo = true;
                            $this->build_header_layout('mobile_content');
                        } else {
                            echo '<nav class="primary-nav">';
                                $logo = $this->get_logo(true, '_menu');
                                echo !empty($logo) ? $logo : '';
                                if (has_nav_menu('main_menu')) {
                                    gostudy_main_menu('main_menu');
                                }
                            echo '</nav>';
                        }
                    echo '</div>';

                echo '</div>';
            echo '</div>';
            echo '</div>'; // mobile_nav_wrapper
        }

        /**
         * Build Header HTML
         *
         *
         * @access public
         * @since 1.0.0
         */
        public function header_render_html()
        {
            $mobile_header_custom =  Gostudy_Theme_Helper::get_option('mobile_header');

            echo '<header class="rt-theme-header', esc_attr($this->header_class()), '">';

                // Default header
                echo '<div class="rt-site-header', (!empty($mobile_header_custom) ? ' mobile_header_custom' : ''), '">';
                    echo '<div class="container-wrapper">';
                    $this->build_header_layout();
                    echo '</div>';

                    if (empty($mobile_header_custom)) {
                        $this->build_header_mobile_menu();
                    }

                echo '</div>';

                // Sticky header
                get_template_part('templates/header/block', 'sticky');

                // Mobile header
                get_template_part('templates/header/block', 'mobile');

                // LearnPress sign-in form
                $this->enable_sign_in_form && $this->sign_in_form();

            echo '</header>';

            // Side panel
            get_template_part('templates/header/block', 'side_area');
        }

        /**
         * Get header Logotype
         */
        public function get_logo($menu_condition = false, $_prefix = '')
        {
            $location = $this->html_render;

            if (
                $this->header_type === 'custom'
                && !empty($this->header_page_select_id)
                && did_action('elementor/loaded')
            ) {
                // Settings manager
                $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers('page');

                // Settings model for header page
                $page_settings_model = $page_settings_manager->get_model($this->header_page_select_id);

                if ($page_settings_model->get_settings('use_custom'.$_prefix.'_logo')) {
                    $logo = $page_settings_model->get_settings('custom'.$_prefix.'_logo');

                    if ($page_settings_model->get_settings('enable'.$_prefix.'_logo_height')) {
                        $custom_size = $page_settings_model->get_settings('logo'.$_prefix.'_height');
                    }

                    if (!empty($logo)) $location = 'bottom';

                    $menu_condition = !empty($logo) ? false : true;
                }
            }

            new Gostudy_Get_Logo($location, $menu_condition, $logo ?? '', $custom_size ?? '');
        }

        /**
         * Get Header Search
         *
         *
         * @access public
         * @since  1.0.0
         */
        public function search($html_render = '', $location, $section)
        {
            $description = esc_html__('Type To Search', 'gostudy');
            $search_style = Gostudy_Theme_Helper::get_option('search_style') ?: 'standard';

            $render_search = true;
            if ($search_style === 'alt') {
                if ($this->html_render != 'sticky') {
                    $render_search = true;
                } else {
                    $render_search = false;
                }
            }
            if (
                $search_style === 'alt'
                && self::$search_form_counter > 0
                && $this->html_render !== 'mobile'
            ) {
                $render_search = false;
            }

            $search_class = ' search_'.$search_style;

            $customize = Gostudy_Theme_Helper::get_option('bottom_header_item_search_custom');
            $customize = empty($customize) ? 'def' : 'color';

            $text_color = Gostudy_Theme_Helper::get_option('bottom_header_item_search_color_txt')['rgba'] ?? '';
            $text_color_hover = Gostudy_Theme_Helper::get_option('bottom_header_item_search_hover_color_txt')['rgba'] ?? '';

            $search_css_id = uniqid('gostudy_search_');

            // Start Custom CSS
            $styles = '';
            if ($this->html_render !== 'mobile') {
                ob_start();
                if ($customize == 'color') {
                    echo "#$search_css_id:hover {
                            color: ".($text_color_hover ? esc_attr($text_color_hover) : 'transparent')."  !important;
                        }";
                }
                $styles .= ob_get_clean();
            }

            // Register css
            if (!empty($styles)) {
                Gostudy_Theme_Helper::enqueue_css($styles);
            }

            unset($this->render_attributes);

            $this->add_render_attribute('search', 'class', [
                'rt-search',
                'elementor-search',
                'header_search-button-wrapper'
            ]);

            $this->add_render_attribute('search', 'role', 'button');

            if (
                $this->html_render !== 'mobile'
                && $customize == 'color'
            ) {
                $this->add_render_attribute('search', 'style', [
                    'color: '.(!empty($text_color) ? esc_attr($text_color) : 'transparent').';'
                ]);
            }

            $this->add_render_attribute('search', 'id', $search_css_id);

            echo '<div class="header_search', esc_attr($search_class), '"', $this->row_style_height($location, $section), '>';

                echo '<div', $this->get_render_attribute_string('search'), '>',
                    '<div class="header_search-button flaticon-loupe"></div>',
                    '<div class="header_search-close flaticon-close"></div>',
                '</div>';

                if ($render_search) {
                    echo '<div class="header_search-field">';
                        if ($search_style === 'alt') {
                            echo '<div class="header_search-wrap">',
                                '<div class="gostudy_module_double_headings">',
                                    '<h3 class="header_search-heading_description heading_title">',
                                        apply_filters('gostudy/search/description', $description),
                                    '</h3>',
                                '</div>',
                                '<div class="header_search-close flaticon-close"></div>',
                            '</div>';
                        }
                        echo get_search_form(false);
                    echo '</div>';
                }

            echo '</div>';
        }

        /**
        * Side Panel Icon
        */
        public function get_side_panel($location, $section)
        {
            echo '<div class="side_panel"', $this->row_style_height($location, $section), '>',
                '<div class="side_panel_inner"', $this->side_panel_style_icon(), '>',
                    '<a href="#" class="side_panel-toggle">',
                        '<span class="side_panel-toggle-inner">',
                            '<span></span>',
                            '<span></span>',
                            '<span></span>',
                        '</span>',
                    '</a>',
                '</div>',
            '</div>';
        }

        /**
         * Get Header Login
         */
        public function login_in($location, $section)
        {
            $link = get_permalink( get_option('woocommerce_myaccount_page_id') );
            $query_args = [
                'action' => urlencode('signup_form'),
            ];
            $url = add_query_arg($query_args, $link);

            $link_logout = wp_logout_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) );
            echo "<div class='login-in woocommerce'", $this->row_style_height($location, $section), '>';

                echo '<span class="login-in_wrapper">';
                if (is_user_logged_in()) {
                    echo "<a class='login-in_link-logout' href='", esc_url($link_logout), "'>", esc_html__('WC LOGOUT', 'gostudy'), "</a>";
                } else {
                    echo "<a class='login-in_link' href='", esc_url_raw($url), "'>", esc_html__('WC LOGIN', 'gostudy'), '</a>';
                }

                echo '</span>';

                echo '<div class="login-modal rt_modal-window">';
                    echo '<div class="overlay"></div>';
                    echo '<div class="modal-dialog modal_window-login">';
                        echo '<div class="modal_header"></div>';
                        echo '<div class="modal_content">';
                            wc_get_template('myaccount/form-login.php');
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        }

        /**
         * Header Cart
         */
        public function cart($location, $section)
        {
            echo '<div class="rt-mini-cart_wrapper">',
                '<div class="mini-cart woocommerce"', ( $this->row_style_height($location, $section) ), '>',
                    $this->icon_cart(), self::woo_cart(),
                '</div>',
            '</div>';
        }
        /**
         * login_join
         */
        public function login_join($location, $section)
        {


        $login_page_url =  Gostudy_Theme_Helper::get_option('login_page_url_mobile');
        $join_page_url = Gostudy_Theme_Helper::get_option('join_page_url_mobile');
        $login_text = Gostudy_Theme_Helper::get_option('login_text_mobile');
        $join_text = Gostudy_Theme_Helper::get_option('join_text_mobile');
        $logout_text = Gostudy_Theme_Helper::get_option('logout_text_mobile');


        echo '<div class="login-join mobile__devices">';
        echo '<span class="login-join_wrapper">';

       if (!is_user_logged_in()) :
        
            echo '<a class="login_link_mobile', '" href="', esc_url($login_page_url), '">',
               $login_text,
                '</a>';
                
            echo '<span class="l_g_divider">';
                echo esc_attr('&nbsp;&nbsp;/&nbsp;&nbsp;');
            echo '</span>';

            echo '<a class="join_link_mobile', '" href="', esc_url($join_page_url), '">',
            $join_text,
            '</a>';

       endif;



        echo '</span>';
        echo '</div>';
        }

        /**
         * LearnDash login_join
         */
        public function ld_login_reg($location, $section)
        {


        $login_page_url =  Gostudy_Theme_Helper::get_option('login_page_url_mobile');
        $join_page_url = Gostudy_Theme_Helper::get_option('join_page_url_mobile');
        $profile_page_url = Gostudy_Theme_Helper::get_option('profile_page_url_mobile');
        $profile_text = Gostudy_Theme_Helper::get_option('profile_text_mobile');
        $login_text = Gostudy_Theme_Helper::get_option('login_text_mobile');
        $join_text = Gostudy_Theme_Helper::get_option('join_text_mobile');
        $logout_text = Gostudy_Theme_Helper::get_option('logout_text_mobile');


        echo '<div class="login-join mobile__devices">';
        echo '<span class="login-join_wrapper">';

       if (!is_user_logged_in()) :
        
         
            echo '<span class="login_link_mobile">';
               echo do_shortcode('[learndash_login]');
            echo '</span>';

       endif;



        echo '</span>';
        echo '</div>';
        }

        /**
         * Profile_link 
         */
        public function profile_link($location, $section)
        {


        $login_page_url =  Gostudy_Theme_Helper::get_option('login_page_url_mobile');
        $join_page_url = Gostudy_Theme_Helper::get_option('join_page_url_mobile');
        $profile_page_url = Gostudy_Theme_Helper::get_option('profile_page_url_mobile');
        $profile_text = Gostudy_Theme_Helper::get_option('profile_text_mobile');
        $login_text = Gostudy_Theme_Helper::get_option('login_text_mobile');
        $join_text = Gostudy_Theme_Helper::get_option('join_text_mobile');
        $logout_text = Gostudy_Theme_Helper::get_option('logout_text_mobile');


        echo '<div class="login-join mobile__devices">';
        echo '<span class="login-join_wrapper">';

       if (is_user_logged_in()) :

            echo '<a class="join_link_mobile', '" href="', esc_url($profile_page_url), '">',
            $profile_text,
            '</a>';
       endif;



        echo '</span>';
        echo '</div>';
        }

        /**
         * Logout 
         */
        public function logout_btn($location, $section)
        {


        $login_page_url =  Gostudy_Theme_Helper::get_option('login_page_url_mobile');
        $join_page_url = Gostudy_Theme_Helper::get_option('join_page_url_mobile');
        $profile_page_url = Gostudy_Theme_Helper::get_option('profile_page_url_mobile');
        $profile_text = Gostudy_Theme_Helper::get_option('profile_text_mobile');
        $login_text = Gostudy_Theme_Helper::get_option('login_text_mobile');
        $join_text = Gostudy_Theme_Helper::get_option('join_text_mobile');
        $logout_text = Gostudy_Theme_Helper::get_option('logout_text_mobile');


        echo '<div class="login-join mobile__devices">';
        echo '<span class="login-join_wrapper">';

       if (is_user_logged_in()) :

            echo '<a class="logout_link_mobile join_link" href="'.wp_logout_url(home_url()).'">',
                $logout_text,
                '</a>';
       endif;



        echo '</span>';
        echo '</div>';
        }

        public function icon_cart()
        {
            $customize = Gostudy_Theme_Helper::get_option('bottom_header_cart_custom');
            $customize = empty($customize) ? 'def' : 'color';

            $text_color_idle = Gostudy_Theme_Helper::get_option('bottom_header_cart_color_txt')['rgba'] ?? '';
            $text_color_hover = Gostudy_Theme_Helper::get_option('bottom_header_cart_hover_color_txt')['rgba'] ?? '';

            $cart_css_id = uniqid('gostudy_woo_');

            // Enqueue CSS
            if (
                $this->html_render !== 'mobile'
                && $customize == 'color'
            ) {
                ob_start();
                if ($text_color_idle) {
                    echo "#$cart_css_id {
                        color: ", esc_attr($text_color_idle), ";
                    }";
                }
                if ($text_color_hover) {
                    echo "#$cart_css_id:hover {
                        color: ", esc_attr($text_color_hover), ";
                    }";
                }
                $styles = ob_get_clean();
            }
            if (!empty($styles)) {
                Gostudy_Theme_Helper::enqueue_css($styles);
            }

            unset($this->render_attributes);

            $this->add_render_attribute('cart', 'id', $cart_css_id);
            $this->add_render_attribute('cart', 'class', 'rt-cart woo_icon elementor-cart');
            $this->add_render_attribute('cart', 'role', 'button');
            $this->add_render_attribute('cart', 'title', esc_attr__('Click to open Shopping Cart', 'gostudy'));

            ob_start();
            echo '<a', $this->get_render_attribute_string('cart'), '>',
                '<span class="woo_mini-count flaticon flaticon-shopping-cart">',
                    WC()->cart->cart_contents_count > 0 ? '<span>' . esc_html(WC()->cart->cart_contents_count) . '</span>' : '',
                '</span>',
            '</a>';

            return ob_get_clean();
        }

        public static function woo_cart()
        {
            ob_start();
                echo '<div class="rt-woo_mini_cart">';
                    woocommerce_mini_cart();
                echo '</div>';

            return ob_get_clean();
        }

        public function in_array_r($needle, $haystack, $strict = false)
        {
            if (is_array($haystack)) {
                foreach ($haystack as $item) {
                    if (
                        ($strict ? $item === $needle : $item == $needle)
                        || (is_array($item) && $this->in_array_r($needle, $item, $strict))
                    ) {
                        return true;
                    }
                }
            }

            return false;
        }

        /**
         * Get ID of the user to be signed in.
         *
         * Aimed to fix issue of empty WP_User object
         * during Front Page rendering after valid `username/password` submission.
         */
        public static function get_auth_id()
        {
            if (!is_front_page()) {
                return false;
            }

            $name = $_REQUEST['username'] ?? ($_REQUEST['reg_username'] ?? '');
            $pass = $_REQUEST['password'] ?? ($_REQUEST['reg_password'] ?? '');

            $user = $name && $pass ? wp_authenticate($name, $pass) : false;

            return is_a($user, 'WP_User') && isset($user->ID) ? $user->ID : false;
        }

        /**
         * LearnPress Sign In/Out button
         */
        protected function sign_in_button($location, $section)
        {
            $profile = LP_Global::profile();

            echo '<div class="login-in learnpress"', $this->row_style_height($location, $section), '>';
            echo '<span class="login-in_wrapper">';
            if (!is_user_logged_in()) :
                $class = '';
                $url = learn_press_user_profile_link();
                if ($profile->get_login_fields()) :
                    $class .= ' form-available';
                    $get_param = ['action' => urlencode('sign-in')];
                    $url = add_query_arg($get_param, $url);
                endif;
                echo '<a class="login-in_link', $class, '" href="', esc_url_raw($url), '">',
                    esc_html__('LP LOGIN', 'gostudy'),
                    '</a>';
            else :
                $url = $profile->logout_url();
                echo '<a class="login-in_link-logout" href="', esc_url_raw($url), '">',
                    esc_html__('LP LOGOUT', 'gostudy'),
                    '</a>';
            endif;
            echo '</span>';
            echo '</div>';
        }

        /**
         * LearnPress Sign In/Register Form
         */
        public static function sign_in_form()
        {
            $profile = LP_Global::profile();
            $current_user = wp_get_current_user();
            $auth_id = self::get_auth_id();

            if (get_user_meta($current_user->ID, 'gostudy_user_just_signed_in')[0] ?? false) {
                $is_user = true;
                $user_id = $current_user->ID;
                $user_name = $current_user->display_name ?: $current_user->nickname;
            } elseif ($auth_id) {
                $is_user = true;
                $user_id = $auth_id;
                $user_data = get_userdata($auth_id)->data ?? null;
                $user_name = $user_data->display_name ?? null;
                $user_name = $user_name ?? $user_data->display_name ?: $user_data->user_login;
            }

            // Success/error messages
            if (isset($is_user)) {
                if (isset($user_name)) {
                    $message = sprintf(esc_html__('Hello, %s!', 'gostudy'), trim($user_name));
                    learn_press_display_message($message, 'success');
                }

                delete_metadata('user', $user_id, 'gostudy_user_just_signed_in');
            } else {
                $error_messages = learn_press_session_get(learn_press_session_message_id());
                unset($error_messages['success']);
                learn_press_session_set(learn_press_session_message_id(), $error_messages);
                learn_press_print_messages();
            }

            if (
                !is_user_logged_in()
                && $login_form = $profile->get_login_fields()
            ) {
                $registartion_allowed = get_option('users_can_register');

                echo '<div class="rt-sign_in_up_form">';
                if ($registartion_allowed) {
                    echo '<ul class="sign-in-up-tabs">',
                        '<li class="active">',
                            '<a href="?tab=tab-login" data-tab=".tab-login">',
                                esc_html__('Login', 'gostudy'),
                            '</a>',
                        '</li>',
                        '<li>',
                            '<a href="?tab=tab-register" data-tab=".tab-register">',
                                esc_html__('Register', 'gostudy'),
                            '</a>',
                        '</li>',
                    '</ul>';
                }
                echo '<div class="tab-login active">';
                learn_press_get_template('global/form-login.php', ['fields' => $login_form]);
                echo '</div>';

                if (
                    $registartion_allowed
                    && $register_form = $profile->get_register_fields()
                ) {
                    echo '<div class="tab-register">';
                        learn_press_get_template('global/form-register.php', ['fields' => $register_form]);
                    echo '</div>';
                }
                echo '</div>';
            }
        }

    
    }

    new Gostudy_Get_Header();
}
