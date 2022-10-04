<?php
defined('ABSPATH') || exit;

if (!class_exists('RT_Theme_Panel')) {
    /**
     * RT Theme Panel
     *
     *
     * @category Class
     * @package gostudy\core\class
     * @author RaisTheme <help.raistheme@gmail.com>
     * @since 1.0.0
     */
    class RT_Theme_Panel
    {
        /**
         * @access      private
         * @var         \RT_Theme_Panel $instance
         * @since       3.0.0
         */
        private static $instance;

        /**
         * Get active instance
         *
         * @access      public
         * @since       3.1.3
         * @return      self::$instance
         */
        public static function instance()
        {
            if ( ! self::$instance ) {
                self::$instance = new self;
                self::$instance->hooks();
            }

            return self::$instance;
        }

        // Shim since we changed the function name. Deprecated.
        public static function get_instance()
        {
            if ( ! self::$instance ) {
                self::$instance = new self;
                self::$instance->hooks();
            }

            return self::$instance;
        }

        private function hooks()
        {
            /* ----------------------------------------------------------------------------- */
            /* Add Menu Page */
            /* ----------------------------------------------------------------------------- */
            add_action( 'admin_menu', [ $this, 'theme_panel_admin_menu' ]);
            add_action( 'admin_init', [ $this, 'theme_redirect' ] );
        }

        public function theme_panel_admin_menu()
        {
            add_menu_page (
                esc_html__('Gostudy', 'gostudy'),
                esc_html__('Gostudy', 'gostudy'),
                'manage_options', // capability
                'rt-dashboard-panel',  // menu-slug
                [ $this, 'theme_panel_welcome_render' ], // function that will render its output
                get_template_directory_uri() . '/core/admin/img/dashboard/logo.png', // link to the icon that will be displayed in the sidebar
                2 // position of the menu option
            );
            $submenu = [];
            $submenu[] = [
                esc_html__('Welcome', 'gostudy'), // page_title
                esc_html__('Welcome', 'gostudy'), // menu_title
                'manage_options', // capability
                'rt-dashboard-panel', // menu_slug
                [ $this, 'theme_panel_welcome_render' ], // function that will render its output
            ];

            if (current_user_can( 'activate_plugins' )):
                $submenu[] = [
                    esc_html__('Theme Plugins', 'gostudy'), // page_title
                    esc_html__('Theme Plugins', 'gostudy'), // menu_title
                    'edit_posts', // capability
                    'rt-plugins-panel', // menu_slug
                    [ $this, 'theme_plugins' ], // function that will render its output
                ];
            endif;


            $submenu[] = [
                esc_html__('Requirements', 'gostudy'), // page_title
                esc_html__('Requirements', 'gostudy'), // menu_title
                'edit_posts', // capability
                'rt-status-panel', // menu_slug
                [ $this, 'theme_status' ], // function that will render its output
            ];


            $submenu[] = [
                esc_html__('Activate Theme', 'gostudy'), // page_title
                esc_html__('Activate Theme', 'gostudy'), // menu_title
                'edit_posts', // capability
                'rt-activate-theme-panel', // menu_slug
                [ $this, 'theme_activate' ], // function that will render its output
            ];

            $submenu[] = [
                esc_html__('Help Center', 'gostudy'), // page_title
                esc_html__('Help Center', 'gostudy'), // menu_title
                'edit_posts', // capability
                'rt-theme-helper-panel', // menu_slug
                [ $this, 'theme_helper' ], // function that will render its output
            ];
            if ( class_exists( 'Gostudy_Core' ) ) {
                $submenu[] = [
                    esc_html__('Theme Options', 'gostudy'), // page_title
                    esc_html__('Theme Options', 'gostudy'), // menu_title
                    'edit_posts', // capability
                    'rt-theme-options-panel', // menu_slug
                    [ $this, 'theme_options' ], // function that will render its output
                ];
            }

            $submenu = apply_filters('rt_panel_submenu', [ $submenu ] );

            foreach ($submenu[0] as $key => $value) {
                add_submenu_page(
                    'rt-dashboard-panel', // parent menu slug
                    $value[0], // page_title
                    $value[1], // menu_title
                    $value[2], // capability
                    $value[3], // menu_slug
                    $value[4] // function that will render its output
                );
            }
        }

        public function theme_dashboard_heading()
        {
            global $submenu;

            $menu_items = '';

            if (isset($submenu['rt-dashboard-panel'])):
              $menu_items = $submenu['rt-dashboard-panel'];
            endif;

            if (!empty($menu_items)) :
            ?>
              <div class="wrap rt-wrapper-notify">
                <div class="nav-tab-wrapper">
                  <?php foreach ($menu_items as $item):
                    $class = isset($_GET['page']) && $_GET['page'] == $item[2] ? ' nav-tab-active' : '';
                    ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page='.$item[2].''));?>"
                        class="nav-tab<?php echo esc_attr($class);?>"
                    >
                        <?php echo esc_html($item[0]); ?>

                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif;
        }

        public function theme_panel_welcome_render()
        {

            $this->theme_dashboard_heading();

            /**
             * Template View Welcome
             */
            require_once get_theme_file_path('/core/dashboard/tpl-view-weclome.php');
        }

        public function theme_plugins()
        {

            $this->theme_dashboard_heading();

            /**
             * Template View Plugin
             */
            require_once get_theme_file_path('/core/dashboard/tpl-view-plugins.php');
        }

        public function theme_status()
        {

            $this->theme_dashboard_heading();

            /**
             * Template View Plugin
             */
            require_once get_theme_file_path('/core/dashboard/tpl-view-status.php');

        }

        public function theme_activate()
        {
            $this->theme_dashboard_heading();

            /**
             * Template View Plugin
             */
            require_once get_theme_file_path('/core/dashboard/tpl-view-activate-theme.php');
        }

        public function theme_helper()
        {
            $this->theme_dashboard_heading();

            /**
             * Template View Plugin
             */
            require_once get_theme_file_path('/core/dashboard/tpl-view-theme-helper.php');
        }

        public function theme_options() {}

        public function theme_redirect()
        {
            global $pagenow;
            if ( is_admin() && isset( $_GET['activated'] ) && 'themes.php' === $pagenow ) {
                wp_safe_redirect( esc_url(admin_url( 'admin.php?page=rt-dashboard-panel' )) );
                exit;
            }
        }

    }
}

RT_Theme_Panel::get_instance();


?>