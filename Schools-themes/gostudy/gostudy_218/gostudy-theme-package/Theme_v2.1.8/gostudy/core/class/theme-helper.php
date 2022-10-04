<?php

defined('ABSPATH') || exit;

if (!class_exists('Gostudy_Theme_Helper')) {
    /**
     * Gostudy Theme Helper
     *
     *
     * @package gostudy\core\class
     * @author RaisTheme <help.raistheme@gmail.com>
     * @since 1.0.0
     */
    class Gostudy_Theme_Helper
    {
        private static $instance;

        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Retrieves Redux option.
         *
         * @param string $name  Desired option name.
         * @return string|null  Option value or `null` if value wasn't set.
         */
        public static function get_option(String $name)
        {
            if (class_exists('Redux')) {
                // Customizer
                if (!empty($GLOBALS['gostudy_set'])) {
                    $theme_options = $GLOBALS['gostudy_set'];
                } else {
                    $theme_options = get_option('gostudy_set');
                }
            }

            if (empty($theme_options)) {
                $theme_options = get_option('gostudy_default_options');
            }

            return $theme_options[$name] ?? null;
        }

        /**
         * Retrieves Metabox option.
         *
         * Assumes that all RWMB options share same name
         * as their redux analogues, prefixed with `mb_` string.
         *
         * @param string $name              Desired option name.
         * @param string $dependency_key    Optional. Key of related metabox option,
         *                                  on which desired option depends.
         * @param string $dependency_value  Optional. Value of related metabox option,
         *                                  on which desired option depends.
         *
         * @return string rwmb value.
         * @return string redux value, if condition isn't met or rwmb value wasn't set.
         */
        public static function get_mb_option(
            String $name,
            $dependency_key = null,
            $dependency_value = null
        ) {
            $mb_option = '';

            if (
                class_exists('RWMB_Loader')
                && get_queried_object_id() !== 0
            ) {
                if (
                    $dependency_key
                    && $dependency_value
                ) {
                    if ($dependency_value == rwmb_meta($dependency_key)) {
                        $mb_option = rwmb_meta('mb_' . $name);
                    }
                } else {
                    $mb_option = rwmb_meta('mb_' . $name);
                }
            }

            return '' !== $mb_option
                ? $mb_option
                : self::get_option($name);
        }

        public static function bg_render(
            String $name,
            $dependency_key = false
        ) {
            $id = !is_archive() ? get_queried_object_id() : 0;

            if (
                class_exists('RWMB_Loader')
                && 0 !== $id
                && $dependency_key
                && 'on' === rwmb_meta($dependency_key)
            ) {
                $image = rwmb_meta('mb_' . $name . '_bg');

                $src = $image['image'] ?? '';
                $repeat = $image['repeat'] ?? '';
                $size = $image['size'] ?? '';
                $attachment = $image['attachment'] ?? '';
                $position = $image['position'] ?? '';
            } else {
                $image = self::get_option($name . '_bg_image');

                $src = $image['background-image'] ?? '';
                $repeat = $image['background-repeat'] ?? '';
                $size = $image['background-size'] ?? '';
                $attachment = $image['background-attachment'] ?? '';
                $position = $image['background-position'] ?? '';
            }

            // Collect attributes
            if ($src) {
                $style = 'background-image: url(' . esc_url($src) . ');';
                $style .= !empty($size) ? ' background-size:' . esc_attr($size) . ';' : '';
                $style .= !empty($repeat) ? ' background-repeat:' . esc_attr($repeat) . ';' : '';
                $style .= !empty($attachment) ? ' background-attachment:' . esc_attr($attachment) . ';' : '';
                $style .= !empty($position) ? ' background-position:' . esc_attr($position) . ';' : '';
            }

            return $style ?? '';
        }

        public static function preloader()
        {
            if (!self::get_option('preloader')) {
                return;
            }

            $wrapper_bg = self::get_option('preloader_background');
            $element_bg = self::get_option('preloader_color');

            $wrapper_style = $wrapper_bg ? ' style="background-color: ' . esc_attr($wrapper_bg) . ';"' : '';
            $element_style = $element_bg ? ' style="background-color: ' . esc_attr($element_bg) . ';"' : '';

            echo '<div id="preloader-wrapper" ', $wrapper_style, '>',
                '<div class="preloader-container">',
                    '<div ', $element_style, '></div>',
                    '<div ', $element_style, '></div>',
                    '<div ', $element_style, '></div>',
                    '<div ', $element_style, '></div>',
                    '<div ', $element_style, '></div>',
                    '<div ', $element_style, '></div>',
                    '<div ', $element_style, '></div>',
                    '<div ', $element_style, '></div>',
                    '<div ', $element_style, '></div>',
                    '</div>',
                // '</div>',
            '</div>';
        }

        public static function pagination($query = false, $alignment = 'left')
        {
            if ($query != false) {
                $wp_query = $query;
            } else {
                global $paged, $wp_query;
            }
            if (empty($paged)) {
                $query_vars = $wp_query->query_vars;
                $paged = $query_vars['paged'] ?? 1;
            }

            $max_page = $wp_query->max_num_pages;

            if ($max_page < 2) {
                // Abort, if no need for pagination
                return;
            }

            switch ($alignment) {
                case 'right':
                    $class_alignment = ' aright';
                    break;
                case 'center':
                    $class_alignment = ' acenter';
                    break;
                default:
                case 'left':
                    $class_alignment = '';
                    break;
            }

            $big = 999999999;

            $test_pag = paginate_links([
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'type' => 'array',
                'current' => max(1, $paged),
                'total' => $max_page,
                'prev_text' => '<i class="flaticon-back"></i>',
                'next_text' => '<i class="flaticon-next"></i>',
            ]);
            $test_comp = '';
            foreach ($test_pag as $value) {
                $test_comp .= "<li class='page'>{$value}</li>";
            }

            return '<ul class="rt-pagination' . esc_attr($class_alignment) . '">' . $test_comp . '</ul>';
        }

        /**
         * Retrieves array of options
         * to be passed as argument in native `wp_link_pages` function
         */
        public static function pagination_wrapper()
        {
            return [
               'before' => '<div class="rt-pagination"><span class="rt-pagination-text">' . __( 'Pages:', 'gostudy' ) . '&nbsp;</span>',
                'after' => '</div>',
                'link_before' => '<span class="page-link post-page-link">',
                'link_after' => '</span>'
            ];
        }

        public static function hexToRGB($hex = "#ffffff")
        {
            if (strlen($hex) < 1) {
                $hex = "#ffffff";
            }

            $color['r'] = hexdec(substr($hex, 1, 2));
            $color['g'] = hexdec(substr($hex, 3, 2));
            $color['b'] = hexdec(substr($hex, 5, 2));

            return $color['r'] . "," . $color['g'] . "," . $color['b'];
        }

        /**
         * Given a Hue, returns corresponding RGB value
         *
         * @param int $v1
         * @param int $v2
         * @param int $vH
         * @return int
         */
        private static function _huetorgb($v1, $v2, $vH)
        {
            if ($vH < 0) $vH += 1;
            if ($vH > 1) $vH -= 1;

            if ((6 * $vH) < 1) {
                return ($v1 + ($v2 - $v1) * 6 * $vH);
            }

            if ((2 * $vH) < 1) return $v2;

            if ((3 * $vH) < 2) {
                return ($v1 + ($v2 - $v1) * ((2 / 3) - $vH) * 6);
            }

            return $v1;
        }

        /**
         *  Given a HSL associative array returns the equivalent HEX string
         *
         * @param array $hsl
         * @return string HEX string
         * @throws Exception "Bad HSL Array"
         */
        public static function hslToHex($hsl = [])
        {
            list($H, $S, $L) = [$hsl['H'] / 360, $hsl['S'], $hsl['L']];

            if ($S == 0) {
                $r = $L * 255;
                $g = $L * 255;
                $b = $L * 255;
            } else {
                if ($L < 0.5) {
                    $var_2 = $L * (1 + $S);
                } else {
                    $var_2 = ($L + $S) - ($S * $L);
                }

                $var_1 = 2 * $L - $var_2;

                $r = round(255 * self::_huetorgb($var_1, $var_2, $H + (1 / 3)));
                $g = round(255 * self::_huetorgb($var_1, $var_2, $H));
                $b = round(255 * self::_huetorgb($var_1, $var_2, $H - (1 / 3)));
            }

            // Convert to hex
            $r = dechex($r);
            $g = dechex($g);
            $b = dechex($b);

            // Make sure we get 2 digits for decimals
            $r = (strlen('' . $r) === 1) ? '0' . $r : $r;
            $g = (strlen('' . $g) === 1) ? '0' . $g : $g;
            $b = (strlen('' . $b) === 1) ? '0' . $b : $b;

            return $r . $g . $b;
        }

        /**
         * @link https://github.com/opensolutions/smarty/blob/master/plugins/modifier.truncate.php
         */
        public static function modifier_character(
            $string,
            $length = 80,
            $etc = '... ',
            $break_words = false
        ) {
            if (0 == $length) {
                return '';
            }

            if (mb_strlen($string, 'utf8') > $length) {
                if (!$break_words) {
                    $string = preg_replace('/\s+\S+\s*$/su', '', mb_substr($string, 0, $length + 1, 'utf8'));
                }

                return mb_substr($string, 0, $length, 'utf8') . $etc;
            } else {
                return $string;
            }
        }

        public static function load_more(
            $query = false,
            $load_more_text = ''
        ) {
            $load_more_text = !empty($load_more_text) ? $load_more_text : esc_html__('Load More', 'gostudy');

            $uniq = uniqid();
            $ajax_data_str = htmlspecialchars(json_encode($query), ENT_QUOTES, 'UTF-8'); ?>
            <div class="clear"></div>
                <div class="load_more_wrapper">
                <div class="button_wrapper">
                    <button class="load_more_item">
                        <span><?php echo esc_html($load_more_text); ?></span>
                    </button>
                </div>
                <form class="posts_grid_ajax">
                    <input type='hidden' class='ajax_data' name='<?php echo esc_attr($uniq); ?>_ajax_data' value='<?php echo esc_attr($ajax_data_str); ?>' />
                </form>
            </div><?php
        }

        public static function render_html($args)
        {
            return $args ?? '';
        }

        public static function in_array_r($needle, $haystack, $strict = false)
        {
            if (is_array($haystack)) {
                foreach ($haystack as $item) {
                    if (
                        ($strict ? $item === $needle : $item == $needle)
                        || (is_array($item) && self::in_array_r($needle, $item, $strict))
                    ) {
                        return true;
                    }
                }
            }

            return false;
        }

        public static function get_sidebar_data($name = 'page')
        {
	        if ( is_front_page() && is_home() ) {
	        } elseif ( is_front_page()){
	        } elseif ( is_home()){ // default Wordpress blog
		        $name = 'blog_list';
	        }
	
	        $name = apply_filters('gostudy/theme-helper/get-sidebar-params/cpt', $name);

            $layout = self::get_option($name . '_sidebar_layout');
            $sidebar = self::get_option($name . '_sidebar_def');
            $sidebar_width = self::get_option($name . '_sidebar_def_width');
            $sticky_sidebar = self::get_option($name . '_sidebar_sticky');
            $sidebar_gap = self::get_option($name . '_sidebar_gap');

            if (!class_exists('Gostudy_Core')) {
                list(
                    $sidebar,
                    $layout,
                    $sidebar_width
                ) = self::get_sidebar_defaults();
            }

            $id = !is_archive() ? get_queried_object_id() : 0;

            if (
                class_exists('RWMB_Loader')
                && 0 !== $id
            ) {
                $mb_layout = rwmb_meta('mb_page_sidebar_layout');
                if ($mb_layout && $mb_layout != 'default') {
                    $layout = $mb_layout;
                    $sidebar = rwmb_meta('mb_page_sidebar_def');
                    $sidebar_width = rwmb_meta('mb_page_sidebar_def_width');
                    $sticky_sidebar = rwmb_meta('mb_sticky_sidebar');
                    $sidebar_gap = rwmb_meta('mb_sidebar_gap');
                }
            }

            if ($sticky_sidebar) {
                wp_enqueue_script('theia-sticky-sidebar', get_template_directory_uri() . '/js/theia-sticky-sidebar.min.js');
                $sidebar_class = ' sticky-sidebar';
            }

            if (
                isset($sidebar_gap)
                && 'def' != $sidebar_gap
                && 'default' != $layout
            ) {
                $layout_pos = $layout == 'left' ? 'right' : 'left';
                $sidebar_style = 'style="padding-' . $layout_pos . ': ' . $sidebar_gap . 'px;"';
            }

            $column = 12;
            if ($layout == 'left' || $layout == 'right') {
                $column = (int) $sidebar_width;
            } else {
                $sidebar = '';
            }

            //* GET Params sidebar
            if (!empty($_GET['shop_sidebar'])) {
                $layout = $_GET['shop_sidebar'];
                $sidebar = 'shop_products';
                $column = 9;
            }

            if (!is_active_sidebar($sidebar)) {
                $column = 12;
                $sidebar = '';
                $layout = 'none';
            }

            if (
                is_active_sidebar($sidebar)
                && ($layout == 'left' || $layout == 'right')
            ) {
                $sb_data['column'] = $column;
                $sb_data['row_class'] = $layout != 'none' ? ' sidebar_' . esc_attr($layout) : '';
                $sb_data['container_class'] = $layout != 'none' ? ' rt-content-sidebar' : '';
                $sb_data['layout'] = $layout;
                $sb_data['id'] = $sidebar;
                $sb_data['class'] = $sidebar_class ?? '';
                $sb_data['style'] = $sidebar_style ?? '';
            }

            return $sb_data ?? [];
        }

        public static function get_sidebar_defaults()
        {
            if (is_active_sidebar('sidebar_main-sidebar')) {
                $sidebar = 'sidebar_main-sidebar';
                $layout = 'right';
                $sidebar_width = 9;
            }

            if (
                class_exists('WooCommerce')
                && is_woocommerce()
            ) {
                if (is_shop()) {
                    if (is_active_sidebar('shop_products')) {
                        $sidebar = 'shop_products';
                        $layout = 'right';
                        $sidebar_width = 9;
                    } else {
                        $sidebar = '';
                        $layout = 'none';
                        $sidebar_width = 0;
                    }
                } elseif (
                    is_product()
                    && is_active_sidebar('shop_single')
                ) {
                    $sidebar = 'shop_single';
                    $layout = 'right';
                    $sidebar_width = 9;
                }
            } elseif ( function_exists('is_learnpress') && is_learnpress() ) {
                if ( is_active_sidebar('learnpress_single') && learn_press_is_course() ) {
                    $layout = 'right';
                    $sidebar = 'learnpress_single';
                    $sidebar_width = 9;
                } elseif ( is_active_sidebar('learnpress_archive') && learn_press_is_courses() ) {
                    $layout = 'right';
                    $sidebar = 'learnpress_archive';
                    $sidebar_width = 9;
                }
            }

            elseif ( function_exists('tutor') ) {
                if ( is_active_sidebar('tutor_single') ) {
                    $layout = 'right';
                    $sidebar = 'tutor_single';
                    $sidebar_width = 9;
                } elseif ( is_active_sidebar('tutor_archive') ) {
                    $layout = 'right';
                    $sidebar = 'tutor_archive';
                    $sidebar_width = 9;
                }
            }

            return [
                $sidebar ?? '',
                $layout ?? 'none',
                $sidebar_width ?? 0
            ];
        }

        public static function render_sidebar(Array $sb_data)
        {
            $class = $sb_data['class'] ?? '';
            $class .= ' rt_col-' . (12 - ((int) $sb_data['column'] ?? 0));

            echo '<div class="sidebar-container', $class, '" ', ($sb_data['style'] ?? ''), '>';

            if (is_active_sidebar($sb_data['id'])) {
                echo '<aside class="sidebar">';
                    do_action('gostudy/theme-helper/render-sidebar/before-widgets');
                    dynamic_sidebar($sb_data['id']);
                echo '</aside>';
            }

            echo '</div>';
        }

        public static function posted_meta_on()
        {
            global $post;

            printf(
                '<span><time class="entry-date published" datetime="%1$s">%2$s</time></span><span>' . esc_html__('Published in', 'gostudy') . ' <a href="%3$s" rel="gallery">%4$s</a></span>',
                esc_attr(get_the_date('c')),
                esc_html(get_the_date()),
                esc_url(get_permalink($post->post_parent)),
                esc_html(get_the_title($post->post_parent))
            );

            printf(
                '<span class="author vcard">%1$s</span>',
                sprintf(
                    '<a class="url fn n" href="%1$s">%2$s</a>',
                    esc_url(get_author_posts_url(get_the_author_meta('ID'))),
                    esc_html(get_the_author())
                )
            );

            $metadata = wp_get_attachment_metadata();

            if ($metadata) {
                printf(
                    '<span class="full-size-link"><span class="screen-reader-text">%1$s </span><a href="%2$s" title="%2$s">%1$s %3$s &times; %4$s</a></span>',
                    esc_html_x('Full size', 'Used before full size attachment link.', 'gostudy'),
                    esc_url(wp_get_attachment_url()),
                    esc_attr(absint($metadata['width'])),
                    esc_attr(absint($metadata['height']))
                );
            }

            $kses_allowed_html = [
                'span' => ['id' => true, 'class' => true, 'style' => true],
                'br' => ['id' => true, 'class' => true, 'style' => true],
                'em' => ['id' => true, 'class' => true, 'style' => true],
                'b' => ['id' => true, 'class' => true, 'style' => true],
                'strong' => ['id' => true, 'class' => true, 'style' => true],
            ];

            edit_post_link(
                /* translators: %s: Name of current post */
                sprintf(
                    wp_kses(__('Edit<span class="screen-reader-text"> "%s"</span>', 'gostudy'), $kses_allowed_html),
                    get_the_title()
                ),
                '<span class="edit-link">',
                '</span>'
            );
        }

        public static function enqueue_css($style)
        {
            ob_start();
            echo self::render_html($style);
            $css = ob_get_clean();
            $css = apply_filters('gostudy/enqueue_shortcode_css', $css, $style);
        }

        public static function render_html_attributes(array $attributes)
        {
            $rendered_attributes = [];

            foreach ($attributes as $attribute_key => $attribute_values) {
                if (is_array($attribute_values)) {
                    $attribute_values = implode(' ', $attribute_values);
                }

                $rendered_attributes[] = sprintf(
                    '%1$s="%2$s"',
                    $attribute_key,
                    esc_attr($attribute_values)
                );
            }

            return implode(' ', $rendered_attributes);
        }


    }

    new Gostudy_Theme_Helper();
}
