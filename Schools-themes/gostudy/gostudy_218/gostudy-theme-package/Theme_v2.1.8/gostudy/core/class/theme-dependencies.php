<?php

defined('ABSPATH') || exit;



if (!class_exists('Gostudy_Theme_Dependencies')) {
    /**
     * Require all the theme necessary files.
     *
     *
     * @category Class
     * @author RaisTheme <help.raistheme@gmail.com>
     * @since 1.0.0
     */
    class Gostudy_Theme_Dependencies
    {
        public function __construct()
        {
            self::include_theme_essential_files();
            self::include_plugins_configurations();
        }

        public static function include_theme_essential_files()
        {

            /** Theme Globals Functions */
            require_once get_theme_file_path('/core/class/theme-global-functions.php');

            /** KSES */
            require_once get_theme_file_path('/core/class/class-kses.php');
            
            /** Animation */
            require_once get_theme_file_path('/core/class/class-animation.php');

            /** Breadcrumb for specific pages */
            require_once get_theme_file_path('/templates/breadcrumb.php');

            /** Theme Helper */
            require_once get_theme_file_path('/core/class/theme-helper.php');

            /** Walker comments */
            require_once get_theme_file_path('/core/class/walker-comment.php');

            /** Walker Mega Menu */
            require_once get_theme_file_path('/core/class/walker-mega-menu.php');

            /** Theme Cats Meta */
            require_once get_theme_file_path('/core/class/theme-cat-meta.php');

            /** Single Post */
            require_once get_theme_file_path('/core/class/single-post.php');

            /** Tinymce Icon */
            require_once get_theme_file_path('/core/class/tinymce-icon.php');

            /** Default Options */
            require_once get_theme_file_path('/core/includes/default-options.php');

            /** Metabox Configuration */
            require_once get_theme_file_path('/core/includes/metabox/metabox-config.php');

            /** Redux Configuration */
            require_once get_theme_file_path('/core/includes/redux/redux-config.php');

            /** Theme Global Variables */
            require_once get_theme_file_path('/core/class/theme-global-variables.php');

            /** Dynamic Styles */
            require_once get_theme_file_path('/core/class/dynamic-styles.php');

            /** Theme Support */
            require_once get_theme_file_path('/core/class/theme-support.php');

            //Admin init
            if (file_exists(get_template_directory() . '/admin/tgm/tgm-init.php')) {
                require_once get_template_directory() . '/admin/tgm/tgm-init.php';
            }

            if (file_exists(get_template_directory() . '/admin/admin-pages/admin.php')) {
                require_once get_template_directory() . '/admin/admin-pages/admin.php';
            }

            if (is_admin()) {
                require get_template_directory() . '/admin/envato_setup/envato_setup.php';
            }

            /**
             * One click demo import.
             */
            require get_theme_file_path('admin/rt-demo-import.php');

        }

        public static function include_plugins_configurations()
        {
            /** Elementor Pro */
            if (class_exists('\ElementorPro\Modules\ThemeBuilder\Module')) {
                require_once get_theme_file_path('/core/class/theme-elementor-pro-support.php');
            }

            if (class_exists('WooCommerce')) {
                require_once get_theme_file_path('/woocommerce/woocommerce-init.php');
            }

            if (class_exists('LearnPress')) {
             require_once get_template_directory() . '/learnpress/learnpress-init.php';
            }

            if (class_exists('SFWD_LMS')) {
               require_once get_template_directory() . '/learndash/learndash-init.php';
            }

            if (function_exists('tutor')) {
                require_once get_template_directory() . '/tutor/tutor-init.php';
            }

            if (class_exists('Zoom_Video_Conferencing_Api')) {
                require_once get_theme_file_path('/video-conferencing-zoom/vczoom-init.php');
            }
        }
    }

    new Gostudy_Theme_Dependencies();
}
