<?php
/**
 * Envato Theme Setup Wizard Class
 *
 * Takes new users through some basic steps to setup their ThemeForest theme.
 *
 * @author      dtbaker
 * @author      vburlak
 * @package     envato_wizard
 * @version     1.3.0
 *
 *
 * 1.2.0 - added custom_logo
 * 1.2.1 - ignore post revisioins
 * 1.2.2 - elementor widget data replace on import
 * 1.2.3 - auto export of content.
 * 1.2.4 - fix category menu links
 * 1.2.5 - post meta un json decode
 * 1.2.6 - post meta un json decode
 * 1.2.7 - elementor generate css on import
 * 1.2.8 - backwards compat with old meta format
 * 1.2.9 - theme setup auth
 * 1.3.0 - ob_start fix
 *
 * Based off the WooThemes installer.
 *
 *
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Envato_Theme_Setup_Wizard' ) ) {
    /**
     * Envato_Theme_Setup_Wizard class
     */
    class Envato_Theme_Setup_Wizard {

        /**
         * The class version number.
         *
         * @since 1.1.1
         * @access private
         *
         * @var string
         */
        protected $version = '1.3.0';

        /** @var string Current theme name, used as namespace in actions. */
        protected $theme_name = '';

        /** @var string Theme author username, used in check for oauth. */
        protected $envato_username = '';

        /** @var string Full url to server-script.php (available from https://gist.github.com/dtbaker ) */
        protected $oauth_script = '';

        /** @var string Current Step */
        protected $step = '';

        /** @var array Steps for the setup wizard */
        protected $steps = array();

        /**
         * Relative plugin path
         *
         * @since 1.1.2
         *
         * @var string
         */
        protected $plugin_path = '';

        /**
         * Relative plugin url for this plugin folder, used when enquing scripts
         *
         * @since 1.1.2
         *
         * @var string
         */
        protected $plugin_url = '';

        /**
         * The slug name to refer to this menu
         *
         * @since 1.1.1
         *
         * @var string
         */
        protected $page_slug;

        /**
         * TGMPA instance storage
         *
         * @var object
         */
        protected $tgmpa_instance;

        /**
         * TGMPA Menu slug
         *
         * @var string
         */
        protected $tgmpa_menu_slug = 'tgmpa-install-plugins';

        /**
         * TGMPA Menu url
         *
         * @var string
         */
        protected $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

        /**
         * The slug name for the parent menu
         *
         * @since 1.1.2
         *
         * @var string
         */
        protected $page_parent;

        /**
         * Complete URL to Setup Wizard
         *
         * @since 1.1.2
         *
         * @var string
         */
        protected $page_url;

        /**
         * @since 1.1.8
         *
         */
        public $site_styles = array();

        /**
         * Holds the current instance of the theme manager
         *
         * @since 1.1.3
         * @var Envato_Theme_Setup_Wizard
         */
        private static $instance = null;

        /**
         * @since 1.1.3
         *
         * @return Envato_Theme_Setup_Wizard
         */
        public static function get_instance() {
            if ( ! self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;
        }


        /**
         * A dummy constructor to prevent this class from being loaded more than once.
         *
         * @see Envato_Theme_Setup_Wizard::instance()
         *
         * @since 1.1.1
         * @access private
         */
        public function __construct() {
            $this->init_globals();
            $this->init_actions();
        }



        /**
         * Get the default style. Can be overriden by theme init scripts.
         *
         * @see Envato_Theme_Setup_Wizard::instance()
         *
         * @since 1.1.9
         * @access public
         */
        public function get_header_logo_width() {
            return 'auto';
        }


        /**
         * Get the default style. Can be overriden by theme init scripts.
         *
         * @see Envato_Theme_Setup_Wizard::instance()
         *
         * @since 1.1.9
         * @access public
         */
        public function get_logo_image() {
            $image_url = get_theme_mod( 'logo_header_image', get_template_directory_uri() . '/img/logo.png' );

            return apply_filters( 'envato_setup_logo_image', $image_url );
        }

        /**
         * Setup the class globals.
         *
         * @since 1.1.1
         * @access public
         */
        public function init_globals() {
            $current_theme         = wp_get_theme();
            $this->theme_name      = strtolower( preg_replace( '#[^a-zA-Z]#', '', $current_theme->get( 'Name' ) ) );
            $this->envato_username = apply_filters( $this->theme_name . '_theme_setup_wizard_username', 'raistheme' );
            $this->oauth_script    = apply_filters( $this->theme_name . '_theme_setup_wizard_oauth_script', '//www.raistheme.com/envato/api/server-script.php' );
            $this->page_slug       = apply_filters( $this->theme_name . '_theme_setup_wizard_page_slug', $this->theme_name . '-setup' );
            $this->parent_slug     = apply_filters( $this->theme_name . '_theme_setup_wizard_parent_slug', '' );

            //If we have parent slug - set correct url
            if ( $this->parent_slug !== '' ) {
                $this->page_url = 'admin.php?page=' . $this->page_slug;
            } else {
                $this->page_url = 'admin.php?page=' . $this->page_slug;
            }
            $this->page_url = apply_filters( $this->theme_name . '_theme_setup_wizard_page_url', $this->page_url );

            //set relative plugin path url
            $this->plugin_path = trailingslashit( $this->cleanFilePath( dirname( __FILE__ ) ) );
            $relative_url      = str_replace( $this->cleanFilePath( get_template_directory() ), '', $this->plugin_path );
            $this->plugin_url  = trailingslashit( get_template_directory_uri() . $relative_url );
        }

        /**
         * Setup the hooks, actions and filters.
         *
         * @uses add_action() To add actions.
         * @uses add_filter() To add filters.
         *
         * @since 1.1.1
         * @access public
         */
        public function init_actions() {
            if ( apply_filters( $this->theme_name . '_enable_setup_wizard', true ) && current_user_can( 'manage_options' ) ) {
                add_action( 'after_switch_theme', array( $this, 'switch_theme' ) );

                if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
                    add_action( 'init', array( $this, 'get_tgmpa_instanse' ), 30 );
                    add_action( 'init', array( $this, 'set_tgmpa_url' ), 40 );
                }

                add_action( 'admin_menu', array( $this, 'admin_menus' ) );
                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                add_action( 'admin_init', array( $this, 'admin_redirects' ), 30 );
                add_action( 'admin_init', array( $this, 'init_wizard_steps' ), 30 );
                add_action( 'admin_init', array( $this, 'setup_wizard' ), 30 );
                add_filter( 'tgmpa_load', array( $this, 'tgmpa_load' ), 10, 1 );
                add_action( 'wp_ajax_envato_setup_plugins', array( $this, 'ajax_plugins' ) );
                add_action( 'wp_ajax_envato_setup_content', array( $this, 'ajax_content' ) );
            }
            if ( function_exists( 'envato_market' ) ) {
                add_action( 'admin_init', array( $this, 'envato_market_admin_init' ), 20 );
                add_filter( 'http_request_args', array( $this, 'envato_market_http_request_args' ), 10, 2 );
                add_action( 'wp_ajax_dtbwp_update_notice_handler', array($this,'ajax_notice_handler') );
                add_action( 'admin_notices', array($this,'admin_theme_auth_notice') );
            }
            add_action( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 2 );
        }

        /**
         * After a theme update we clear the setup_complete option. This prompts the user to visit the update page again.
         *
         * @since 1.1.8
         * @access public
         */
        public function upgrader_post_install( $return, $theme ) {
            if ( is_wp_error( $return ) ) {
                return $return;
            }
            if ( $theme != get_stylesheet() ) {
                return $return;
            }
            update_option( 'envato_setup_complete', false );

            return $return;
        }

        /**
         * We determine if the user already has theme content installed. This can happen if swapping from a previous theme or updated the current theme. We change the UI a bit when updating / swapping to a new theme.
         *
         * @since 1.1.8
         * @access public
         */
        public function is_possible_upgrade() {
            return false;
        }

        public function enqueue_scripts() {
        }

        public function tgmpa_load( $status ) {
            return is_admin() || current_user_can( 'install_themes' );
        }

        public function switch_theme() {
            set_transient( '_' . $this->theme_name . '_activation_redirect', 1 );
        }

        public function admin_redirects() {
            if ( ! get_transient( '_' . $this->theme_name . '_activation_redirect' ) || get_option( 'envato_setup_complete', false ) ) {
                return;
            }
            delete_transient( '_' . $this->theme_name . '_activation_redirect' );
            wp_safe_redirect( admin_url( $this->page_url ) );
            exit;
        }

        /**
         * Get configured TGMPA instance
         *
         * @access public
         * @since 1.1.2
         */
        public function get_tgmpa_instanse() {
            $this->tgmpa_instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
        }

        /**
         * Update $tgmpa_menu_slug and $tgmpa_parent_slug from TGMPA instance
         *
         * @access public
         * @since 1.1.2
         */
        public function set_tgmpa_url() {

            $this->tgmpa_menu_slug = ( property_exists( $this->tgmpa_instance, 'menu' ) ) ? $this->tgmpa_instance->menu : $this->tgmpa_menu_slug;
            $this->tgmpa_menu_slug = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_menu_slug', $this->tgmpa_menu_slug );

            $tgmpa_parent_slug = ( property_exists( $this->tgmpa_instance, 'parent_slug' ) && $this->tgmpa_instance->parent_slug !== 'themes.php' ) ? 'admin.php' : 'themes.php';

            $this->tgmpa_url = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_url', $tgmpa_parent_slug . '?page=' . $this->tgmpa_menu_slug );

        }

        /**
         * Add admin menus/screens.
         */
        public function admin_menus() {
         add_submenu_page('gostudy-admin-menu', esc_html__( 'Setup Wizard','gostudy' ), esc_html__( 'Setup Wizard','gostudy' ), 'manage_options', $this->page_slug,  array( $this, $this->page_slug) );
     }


        /**
         * Setup steps.
         *
         * @since 1.1.1
         * @access public
         * @return array
         */
        public function init_wizard_steps() {

            $this->steps = array(
                'introduction' => array(
                    'name'    => esc_html__( 'Introduction','gostudy' ),
                    'view'    => array( $this, 'envato_setup_introduction' ),
                    'handler' => array( $this, 'envato_setup_introduction_save' ),
                ),
            );
            if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
                $this->steps['default_plugins'] = array(
                    'name'    => esc_html__( 'Plugins','gostudy' ),
                    'view'    => array( $this, 'envato_setup_default_plugins' ),
                    'handler' => '',
                );
            }
            $this->steps['updates']         = array(
                'name'    => esc_html__( 'Updates','gostudy' ),
                'view'    => array( $this, 'envato_setup_updates' ),
                'handler' => array( $this, 'envato_setup_updates_save' ),
            );

            $this->steps['customize']       = array(
                'name'    => esc_html__( 'Customize','gostudy' ),
                'view'    => array( $this, 'envato_setup_customize' ),
                'handler' => '',
            );
            $this->steps['help_support']    = array(
                'name'    => esc_html__( 'Support','gostudy' ),
                'view'    => array( $this, 'envato_setup_help_support' ),
                'handler' => '',
            );
            $this->steps['next_steps']      = array(
                'name'    => esc_html__( 'Ready!','gostudy' ),
                'view'    => array( $this, 'envato_setup_ready' ),
                'handler' => '',
            );

            $this->steps = apply_filters( $this->theme_name . '_theme_setup_wizard_steps', $this->steps );

        }

        /**
         * Show the setup wizard
         */
        public function setup_wizard() {
            if ( empty( $_GET['page'] ) || $this->page_slug !== $_GET['page'] ) {
                return;
            }
            if (ob_get_length() > 0 ) {
                ob_end_clean();
            }

            $this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

            wp_register_script( 'jquery-blockui', $this->plugin_url . 'js/jquery.blockUI.js', array( 'jquery' ), '2.70', true );
            wp_register_script( 'envato-setup', $this->plugin_url . 'js/envato-setup.js', array(
                'jquery',
                'jquery-blockui',
            ), $this->version );
            wp_localize_script( 'envato-setup', 'envato_setup_params', array(
                'tgm_plugin_nonce' => array(
                    'update'  => wp_create_nonce( 'tgmpa-update' ),
                    'install' => wp_create_nonce( 'tgmpa-install' ),
                ),
                'tgm_bulk_url'     => admin_url( $this->tgmpa_url ),
                'ajaxurl'          => admin_url( 'admin-ajax.php' ),
                'wpnonce'          => wp_create_nonce( 'envato_setup_nonce' ),
                'verify_text'      => esc_html__( '...verifying','gostudy' ),
            ) );
            
            wp_enqueue_style( 'envato-setup', $this->plugin_url . 'css/envato-setup.css', array(
                'wp-admin',
                'dashicons',
                'install',
            ), $this->version );

            //enqueue style for admin notices
            wp_enqueue_style( 'wp-admin' );

            wp_enqueue_media();
            wp_enqueue_script( 'media' );

            ob_start();
            $this->setup_wizard_header();
            $this->setup_wizard_steps();
            $show_content = true;
            echo '<div class="envato-setup-content">';
            if ( ! empty( $_REQUEST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
                $show_content = call_user_func( $this->steps[ $this->step ]['handler'] );
            }
            if ( $show_content ) {
                $this->setup_wizard_content();
            }
            echo '</div>';
            $this->setup_wizard_footer();
            exit;
        }

        public function get_step_link( $step ) {
            return add_query_arg( 'step', $step, admin_url( 'admin.php?page=' . $this->page_slug ) );
        }

        public function get_next_step_link() {
            $keys = array_keys( $this->steps );

            return add_query_arg( 'step', $keys[ array_search( $this->step, array_keys( $this->steps ) ) + 1 ], remove_query_arg( 'translation_updated' ) );
        }

        /**
         * Setup Wizard Header
         */
        public function setup_wizard_header() {
            ?>
            <!DOCTYPE html>
            <html xmlns="https://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
            <head>
                <meta name="viewport" content="width=device-width"/>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <?php
            // avoid theme check issues.
                echo '<t';
                echo 'itle>' . __( 'Theme &rsaquo; Setup Wizard','gostudy' ) . '</ti' . 'tle>'; ?>
                <?php wp_print_scripts( 'envato-setup' ); ?>
                <?php do_action( 'admin_print_styles' ); ?>
                <?php add_action( 'admin_head', array( $this, 'dismiss' ) ); ?>
            </head>
            <body class="envato-setup wp-core-ui">
                <h1 id="wc-logo">
                    <?php
                    $image_url = $this->get_logo_image();
                    if ( $image_url ) {
                        $image = '<img class="site-logo" src="%s" alt="%s" style="width:%s; height:auto" />';
                        printf(
                            $image,
                            $image_url,
                            get_bloginfo( 'name' ),
                            $this->get_header_logo_width()
                        );
                    } else { ?>
                        <img src="<?php echo esc_url( $this->plugin_url . 'images/logo.png' ); ?>" alt="<?php esc_attr_e( 'Envato install wizard', 'gostudy' );?>" /><?php
                    } ?>
                </h1>
                <?php
            }

        /**
         * Setup Wizard Footer
         */
        public function setup_wizard_footer() {
            ?>
            <?php if ( 'next_steps' === $this->step ) : ?>
                <a class="wc-return-to-dashboard"
                href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Return to the WordPress Dashboard','gostudy' ); ?></a>
            <?php endif; ?>
        </body>
        <?php
        @do_action( 'admin_footer' ); // this was spitting out some errors in some admin templates. quick @ fix until I have time to find out what's causing errors.
        do_action( 'admin_print_footer_scripts' );
        ?>
        </html>
        <?php
    }

        /**
         * Output the steps
         */
        public function setup_wizard_steps() {
            $ouput_steps = $this->steps;
            array_shift( $ouput_steps );
            ?>
            <ol class="envato-setup-steps">
                <?php foreach ( $ouput_steps as $step_key => $step ) : ?>
                    <li class="<?php
                    $show_link = false;
                    if ( $step_key === $this->step ) {
                        echo 'active';
                    } elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
                        echo 'done';
                        $show_link = true;
                    }
                    ?>"><?php
                    if ( $show_link ) {
                        ?>
                        <a href="<?php echo esc_url( $this->get_step_link( $step_key ) ); ?>"><?php echo esc_html( $step['name'] ); ?></a>
                        <?php
                    } else {
                        echo esc_html( $step['name'] );
                    }
                    ?></li>
                <?php endforeach; ?>
            </ol>
            <?php
        }

        /**
         * Output the content for the current step
         */
        public function setup_wizard_content() {
            isset( $this->steps[ $this->step ] ) ? call_user_func( $this->steps[ $this->step ]['view'] ) : false;
        }

        /**
         * Introduction step
         */
        public function envato_setup_introduction() {

            if ( false && isset( $_REQUEST['debug'] ) ) {
                echo '<pre>';
                // debug inserting a particular post so we can see what's going on
                $post_type = 'nav_menu_item';
                $post_id   = 239; // debug this particular import post id.
                $all_data  = $this->_get_json( 'default.json' );
                if ( ! $post_type || ! isset( $all_data[ $post_type ] ) ) {
                    echo "Post type $post_type not found.";
                } else {
                    echo "Looking for post id $post_id \n";
                    foreach ( $all_data[ $post_type ] as $post_data ) {

                        if ( $post_data['post_id'] == $post_id ) {
                            //print_r( $post_data );
                            $this->_process_post_data( $post_type, $post_data, 0, true );
                        }
                    }
                }
                $this->_handle_delayed_posts();
                print_r( $this->logs );

                echo '</pre>';
            } else if ( isset( $_REQUEST['export'] ) ) {



            } else if ( $this->is_possible_upgrade() ) {
                ?>
                <h1><?php printf( __( 'Welcome to %s.','gostudy' ), wp_get_theme() ); ?></h1>
                <p><?php esc_html_e( 'It looks like you may have recently upgraded to this theme. Great! This setup wizard will help ensure all the default settings are correct. It will also show some information about your new website and support options.','gostudy' ); ?></p>
                <p class="envato-setup-actions step">
                    <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                     class="button-primary button button-large button-next"><?php esc_html_e( 'Let\'s Go!','gostudy' ); ?></a>
                     <a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>"
                         class="button button-large"><?php esc_html_e( 'Not right now','gostudy' ); ?></a>
                     </p>
                     <?php
                 } else if ( get_option( 'envato_setup_complete', false ) ) {
                    ?>
                    <h1><?php printf( __( 'Welcome to %s.','gostudy' ), wp_get_theme() ); ?></h1>
                    <p><?php esc_html_e( 'It looks like you have already run the setup wizard. Below are some options: ','gostudy' ); ?></p>
                    <ul>
                        <li>
                            <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                             class="button-primary button button-next button-large"><?php esc_html_e( 'Run Setup Wizard Again','gostudy' ); ?></a>
                         </li>
                     </ul>
                     <p class="envato-setup-actions step">
                        <a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>"
                         class="button button-large"><?php esc_html_e( 'Cancel','gostudy' ); ?></a>
                     </p>
                     <?php
                 } else { ?>


                    <h1><?php printf( __( 'Welcome to %s.','gostudy' ), wp_get_theme() ); ?></h1>
     
                    <?php
                    $theme = wp_get_theme();

                    $allowed_html = [
                        'a' => [
                            'href' => true,
                            'target' => true,
                        ],
                    ];
                                      ?>

                            <div class="rt-welcome_content">
                                <div class="step-subtitle">
                                    <?php
                                        echo sprintf(esc_html__('Just complete the steps below and you will be able to use all functionalities of %s theme by RaisTheme:', 'gostudy'), esc_html(wp_get_theme()->get('Name')));
                                    ?>
                                </div>
                                <ul>

                                  <li>
                                      <span class="step">
                                          <?php esc_html_e('Step 1', 'gostudy');?>
                                      </span>
                                      <?php
                                    echo sprintf( wp_kses( __( 'Check <a target="_blank" href="%s">requirements</a> to avoid errors with your WordPress.', 'gostudy' ), $allowed_html), esc_url( admin_url( 'admin.php?page=gostudy-requirements' ) ) );

                                      ?>
                                  </li>
                                  <li>
                                      <span class="step">
                                          <?php esc_html_e('Step 2', 'gostudy');?>
                                      </span>
                                      <?php esc_html_e('Install Required and recommended plugins.', 'gostudy');?>
                                  </li>
                                  <li>
                                      <span class="step">
                                          <?php esc_html_e('Step 3', 'gostudy');?>
                                      </span>
                                      <?php esc_html_e('Import demo content', 'gostudy');?>
                                  </li>
                                </ul>
                            </div>

                    <p class="envato-setup-actions step">
                        <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                         class="button-primary button button-large button-next"><?php esc_html_e( 'Let\'s Go!','gostudy' ); ?></a>
                         <a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>"
                             class="button button-large"><?php esc_html_e( 'Not right now','gostudy' ); ?></a>
                         </p>
                         <?php
                     }
                 }

                 public function filter_options( $options ) {
                    return $options;
                }

        /**
         *
         * Handles save button from welcome page. This is to perform tasks when the setup wizard has already been run. E.g. reset defaults
         *
         * @since 1.2.5
         */
        public function envato_setup_introduction_save() {

            check_admin_referer( 'envato-setup' );

            if ( ! empty( $_POST['reset-font-defaults'] ) && $_POST['reset-font-defaults'] == 'yes' ) {

                // clear font options
                update_option( 'tt_font_theme_options', array() );

                // do other reset options here.

                // reset site color
                remove_theme_mod( 'dtbwp_site_color' );

                if ( class_exists( 'dtbwp_customize_save_hook' ) ) {
                    $site_color_defaults = new dtbwp_customize_save_hook();
                    $site_color_defaults->save_color_options();
                }

                $file_name = get_template_directory() . '/style.custom.css';
                if ( file_exists( $file_name ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                    WP_Filesystem();
                    global $wp_filesystem;
                    $wp_filesystem->put_contents( $file_name, '' );
                }
                ?>
                <p>
                    <strong><?php esc_html_e( 'Options have been reset. Please go to Appearance > Customize in the WordPress backend.','gostudy' ); ?></strong>
                </p>
                <?php
                return true;
            }

            return false;
        }


        private function _get_plugins() {
            $instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
            $plugins  = array(
                'all'      => array(), // Meaning: all plugins which still have open actions.
                'install'  => array(),
                'update'   => array(),
                'activate' => array(),
            );

            foreach ( $instance->plugins as $slug => $plugin ) {

                if ( !$instance->can_plugin_activate( $slug ) && false === $instance->does_plugin_have_update( $slug ) ) {
                    // No need to display plugins if they are installed, up-to-date and active.
                    continue;
                } else {
                    $plugins['all'][ $slug ] = $plugin;

                    if ( ! $instance->is_plugin_installed( $slug ) ) {
                        $plugins['install'][ $slug ] = $plugin;
                    } else {
                        if ( false !== $instance->does_plugin_have_update( $slug ) ) {
                            $plugins['update'][ $slug ] = $plugin;
                        }

                        if ( $instance->can_plugin_activate( $slug ) ) {
                            $plugins['activate'][ $slug ] = $plugin;
                        }
                    }
                }
            }

            return $plugins;
        }

        /**
         * Page setup
         */
        public function envato_setup_default_plugins() {

            tgmpa_load_bulk_installer();
            // install plugins with TGM.
            if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
                die( 'Failed to find TGM' );
            }
            $url     = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'envato-setup' );
            $plugins = $this->_get_plugins();

            // copied from TGM

            $method = ''; // Leave blank so WP_Filesystem can populate it as necessary.
            $fields = array_keys( $_POST ); // Extra fields to pass to WP_Filesystem.

            if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
                return true; // Stop the normal page form from displaying, credential request form will be shown.
            }

            // Now we have some credentials, setup WP_Filesystem.
            if ( ! WP_Filesystem( $creds ) ) {
                // Our credentials were no good, ask the user for them again.
                request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );

                return true;
            }

            /* If we arrive here, we have the filesystem */

            ?>
            <h1><?php esc_html_e( 'Default Plugins','gostudy' ); ?></h1>
            <form method="post">

                <?php
                $plugins = $this->_get_plugins();
                if ( count( $plugins['all'] ) ) {
                    ?>
                    <p><?php esc_html_e( 'Your website needs a few essential plugins. The following plugins will be installed or updated:','gostudy' ); ?></p>
                    <ul class="envato-wizard-plugins">
                        <?php foreach ( $plugins['all'] as $slug => $plugin ) { ?>
                            <li data-slug="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $plugin['name'] ); ?>
                            <span>
                                <?php
                                $keys = array();
                                if ( isset( $plugins['install'][ $slug ] ) ) {
                                    $keys[] = 'Installation';
                                }
                                if ( isset( $plugins['update'][ $slug ] ) ) {
                                    $keys[] = 'Update';
                                }
                                if ( isset( $plugins['activate'][ $slug ] ) ) {
                                    $keys[] = 'Activation';
                                }
                                echo implode( ' and ', $keys ) . ' required';
                                ?>
                            </span>
                            <div class="spinner"></div>
                        </li>
                    <?php } ?>
                </ul>
                <?php
            } else {
                echo '<p><strong>' . esc_html_e( 'Good news! All plugins are already installed and up to date. Please continue.','gostudy' ) . '</strong></p>';
            } ?>

            <p><?php esc_html_e( 'You can add and remove plugins later on from within WordPress.','gostudy' ); ?></p>

            <p class="envato-setup-actions step">
                <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                 class="button-primary button button-large button-next"
                 data-callback="install_plugins"><?php esc_html_e( 'Continue','gostudy' ); ?></a>
                 <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                     class="button button-large button-next"><?php esc_html_e( 'Skip this step','gostudy' ); ?></a>
                     <?php wp_nonce_field( 'envato-setup' ); ?>
                 </p>
             </form>
             <?php
         }


         public function ajax_plugins() {
            if ( ! check_ajax_referer( 'envato_setup_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
                wp_send_json_error( array( 'error' => 1, 'message' => __( 'No Slug Found','gostudy' ) ) );
            }
            $json = array();
            // send back some json we use to hit up TGM
            $plugins = $this->_get_plugins();
            // what are we doing with this plugin?
            foreach ( $plugins['activate'] as $slug => $plugin ) {
                if ( $_POST['slug'] == $slug ) {
                    $json = array(
                        'url'           => admin_url( $this->tgmpa_url ),
                        'plugin'        => array( $slug ),
                        'tgmpa-page'    => $this->tgmpa_menu_slug,
                        'plugin_status' => 'all',
                        '_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
                        'action'        => 'tgmpa-bulk-activate',
                        'action2'       => - 1,
                        'message'       => esc_html__( 'Activating Plugin','gostudy' ),
                    );
                    break;
                }
            }
            foreach ( $plugins['update'] as $slug => $plugin ) {
                if ( $_POST['slug'] == $slug ) {
                    $json = array(
                        'url'           => admin_url( $this->tgmpa_url ),
                        'plugin'        => array( $slug ),
                        'tgmpa-page'    => $this->tgmpa_menu_slug,
                        'plugin_status' => 'all',
                        '_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
                        'action'        => 'tgmpa-bulk-update',
                        'action2'       => - 1,
                        'message'       => esc_html__( 'Updating Plugin','gostudy' ),
                    );
                    break;
                }
            }
            foreach ( $plugins['install'] as $slug => $plugin ) {
                if ( $_POST['slug'] == $slug ) {
                    $json = array(
                        'url'           => admin_url( $this->tgmpa_url ),
                        'plugin'        => array( $slug ),
                        'tgmpa-page'    => $this->tgmpa_menu_slug,
                        'plugin_status' => 'all',
                        '_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
                        'action'        => 'tgmpa-bulk-install',
                        'action2'       => - 1,
                        'message'       => esc_html__( 'Installing Plugin','gostudy' ),
                    );
                    break;
                }
            }

            if ( $json ) {
                $json['hash'] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
                wp_send_json( $json );
            } else {
                wp_send_json( array( 'done' => 1, 'message' => esc_html__( 'Success','gostudy' ) ) );
            }
            exit;

        }


        private function _content_default_get() {

            $content = array();

            // find out what content is in our default json file.
            $available_content = $this->_get_json( 'default.json' );
            foreach ( $available_content as $post_type => $post_data ) {
                if ( count( $post_data ) ) {
                    $first           = current( $post_data );
                    $post_type_title = ! empty( $first['type_title'] ) ? $first['type_title'] : ucwords( $post_type ) . 's';
                    if ( $post_type_title == 'Navigation Menu Items' ) {
                        $post_type_title = 'Navigation';
                    }
                    $content[ $post_type ] = array(
                        'title'            => $post_type_title,
                        'description'      => sprintf( __( 'This will create default %s as seen in the demo.','gostudy' ), $post_type_title ),
                        'pending'          => esc_html__( 'Pending.','gostudy' ),
                        'installing'       => esc_html__( 'Installing.','gostudy' ),
                        'success'          => esc_html__( 'Success.' ,'gostudy'),
                        'install_callback' => array( $this, '_content_install_type' ),
                        'checked'          => $this->is_possible_upgrade() ? 0 : 1,
                        // dont check if already have content installed.
                    );
                }
            }

            $content['widgets'] = array(
                'title'            => esc_html__( 'Widgets','gostudy' ),
                'description'      => esc_html__( 'Insert default sidebar widgets as seen in the demo.' ,'gostudy'),
                'pending'          => esc_html__( 'Pending.' ,'gostudy'),
                'installing'       => esc_html__( 'Installing Default Widgets.','gostudy' ),
                'success'          => esc_html__( 'Success.','gostudy' ),
                'install_callback' => array( $this, '_content_install_widgets' ),
                'checked'          => $this->is_possible_upgrade() ? 0 : 1,
                // dont check if already have content installed.
            );
            $content['settings'] = array(
                'title'            => esc_html__( 'Settings' ,'gostudy'),
                'description'      => esc_html__( 'Configure default settings.' ,'gostudy'),
                'pending'          => esc_html__( 'Pending.','gostudy' ),
                'installing'       => esc_html__( 'Installing Default Settings.' ,'gostudy'),
                'success'          => esc_html__( 'Success.','gostudy' ),
                'install_callback' => array( $this, '_content_install_settings' ),
                'checked'          => $this->is_possible_upgrade() ? 0 : 1,
                // dont check if already have content installed.
            );

            $content = apply_filters( $this->theme_name . '_theme_setup_wizard_content', $content );

            return $content;

        }

        /**
         * Page setup
         */
        public function envato_setup_default_content() {
            ?>
            <h1><?php esc_html_e( 'Default Content','gostudy' ); ?></h1>
            <form method="post">
                <?php if ( $this->is_possible_upgrade() ) { ?>
                    <p><?php esc_html_e( 'It looks like you already have content installed on this website. If you would like to install the default demo content as well you can select it below. Otherwise just choose the upgrade option to ensure everything is up to date.','gostudy' ); ?></p>
                <?php } else { ?>
                    <p><?php printf( __( 'It\'s time to insert some default content for your new WordPress website. Choose what you would like inserted below and click Continue. It is recommended to leave everything selected. Once inserted, this content can be managed from the WordPress admin dashboard. ','gostudy' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=page' ) ) . '" target="_blank">', '</a>' ); ?></p>
                <?php } ?>
                <table class="envato-setup-pages" cellspacing="0">
                    <thead>
                        <tr>
                            <td class="check"></td>
                            <th class="item"><?php esc_html_e( 'Item','gostudy' ); ?></th>
                            <th class="description"><?php esc_html_e( 'Description','gostudy' ); ?></th>
                            <th class="status"><?php esc_html_e( 'Status','gostudy' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $this->_content_default_get() as $slug => $default ) { ?>
                            <tr class="envato_default_content" data-content="<?php echo esc_attr( $slug ); ?>">
                                <td>
                                    <input type="checkbox" name="default_content[<?php echo esc_attr( $slug ); ?>]"
                                    class="envato_default_content"
                                    id="default_content_<?php echo esc_attr( $slug ); ?>"
                                    value="1" <?php echo ( ! isset( $default['checked'] ) || $default['checked'] ) ? ' checked' : ''; ?>>
                                </td>
                                <td><label
                                    for="default_content_<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $default['title'] ); ?></label>
                                </td>
                                <td class="description"><?php echo esc_html( $default['description'] ); ?></td>
                                <td class="status"><span><?php echo esc_html( $default['pending'] ); ?></span>
                                    <div class="spinner"></div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <p class="envato-setup-actions step">
                    <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                     class="button-primary button button-large button-next"
                     data-callback="install_content"><?php esc_html_e( 'Continue','gostudy' ); ?></a>
                     <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                         class="button button-large button-next"><?php esc_html_e( 'Skip this step','gostudy' ); ?></a>
                         <?php wp_nonce_field( 'envato-setup' ); ?>
                     </p>
                 </form>
                 <?php
             }


             public function ajax_content() {
                $content = $this->_content_default_get();
                if ( ! check_ajax_referer( 'envato_setup_nonce', 'wpnonce' ) || empty( $_POST['content'] ) && isset( $content[ $_POST['content'] ] ) ) {
                    wp_send_json_error( array( 'error' => 1, 'message' => esc_html__( 'No content Found' ,'gostudy') ) );
                }

                $json         = false;
                $this_content = $content[ $_POST['content'] ];

                if ( isset( $_POST['proceed'] ) ) {
                // install the content!

                    $this->log( ' -!! STARTING SECTION for ' . $_POST['content'] );

                // init delayed posts from transient.
                    $this->delay_posts = get_transient( 'delayed_posts' );
                    if ( ! is_array( $this->delay_posts ) ) {
                        $this->delay_posts = array();
                    }

                    if ( ! empty( $this_content['install_callback'] ) ) {
                        if ( $result = call_user_func( $this_content['install_callback'] ) ) {

                            $this->log( ' -- FINISH. Writing ' . count( $this->delay_posts, COUNT_RECURSIVE ) . ' delayed posts to transient ' );
                            set_transient( 'delayed_posts', $this->delay_posts, 60 * 60 * 24 );

                            if ( is_array( $result ) && isset( $result['retry'] ) ) {
                            // we split the stuff up again.
                                $json = array(
                                    'url'         => admin_url( 'admin-ajax.php' ),
                                    'action'      => 'envato_setup_content',
                                    'proceed'     => 'true',
                                    'retry'       => time(),
                                    'retry_count' => $result['retry_count'],
                                    'content'     => $_POST['content'],
                                    '_wpnonce'    => wp_create_nonce( 'envato_setup_nonce' ),
                                    'message'     => $this_content['installing'],
                                    'logs'        => $this->logs,
                                    'errors'      => $this->errors,
                                );
                            } else {
                                $json = array(
                                    'done'    => 1,
                                    'message' => $this_content['success'],
                                    'debug'   => $result,
                                    'logs'    => $this->logs,
                                    'errors'  => $this->errors,
                                );
                            }
                        }
                    }
                } else {

                    $json = array(
                        'url'      => admin_url( 'admin-ajax.php' ),
                        'action'   => 'envato_setup_content',
                        'proceed'  => 'true',
                        'content'  => $_POST['content'],
                        '_wpnonce' => wp_create_nonce( 'envato_setup_nonce' ),
                        'message'  => $this_content['installing'],
                        'logs'     => $this->logs,
                        'errors'   => $this->errors,
                    );
                }

                if ( $json ) {
                $json['hash'] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
                wp_send_json( $json );
            } else {
                wp_send_json( array(
                    'error'   => 1,
                    'message' => esc_html__( 'Error','gostudy' ),
                    'logs'    => $this->logs,
                    'errors'  => $this->errors,
                ) );
            }

            exit;

        }


        private function _imported_term_id( $original_term_id, $new_term_id = false ) {
            $terms = get_transient( 'importtermids' );
            if ( ! is_array( $terms ) ) {
                $terms = array();
            }
            if ( $new_term_id ) {
                if ( ! isset( $terms[ $original_term_id ] ) ) {
                    $this->log( 'Insert old TERM ID ' . $original_term_id . ' as new TERM ID: ' . $new_term_id );
                } else if ( $terms[ $original_term_id ] != $new_term_id ) {
                    $this->error( 'Replacement OLD TERM ID ' . $original_term_id . ' overwritten by new TERM ID: ' . $new_term_id );
                }
                $terms[ $original_term_id ] = $new_term_id;
                set_transient( 'importtermids', $terms, 60 * 60 * 24 );
            } else if ( $original_term_id && isset( $terms[ $original_term_id ] ) ) {
                return $terms[ $original_term_id ];
            }

            return false;
        }


        public function vc_post( $post_id = false ) {

            $vc_post_ids = get_transient( 'import_vc_posts' );
            if ( ! is_array( $vc_post_ids ) ) {
                $vc_post_ids = array();
            }
            if ( $post_id ) {
                $vc_post_ids[ $post_id ] = $post_id;
                set_transient( 'import_vc_posts', $vc_post_ids, 60 * 60 * 24 );
            } else {

                $this->log( 'Processing vc pages 2: ' );

                return;
                if ( class_exists( 'Vc_Manager' ) && class_exists( 'Vc_Post_Admin' ) ) {
                    $this->log( $vc_post_ids );
                    $vc_manager = Vc_Manager::getInstance();
                    $vc_base    = $vc_manager->vc();
                    $post_admin = new Vc_Post_Admin();
                    foreach ( $vc_post_ids as $vc_post_id ) {
                        $this->log( 'Save ' . $vc_post_id );
                        $vc_base->buildShortcodesCustomCss( $vc_post_id );
                        $post_admin->save( $vc_post_id );
                        $post_admin->setSettings( $vc_post_id );
                        //twice? bug?
                        $vc_base->buildShortcodesCustomCss( $vc_post_id );
                        $post_admin->save( $vc_post_id );
                        $post_admin->setSettings( $vc_post_id );
                    }
                }
            }

        }


        public function elementor_post( $post_id = false ) {

            // regenrate the CSS for this Elementor post
            if( class_exists( 'Elementor\Post_CSS_File' ) ) {
                $post_css = new Elementor\Post_CSS_File($post_id);
                $post_css->update();
            }

        }



        private function _imported_post_id( $original_id = false, $new_id = false ) {
            if ( is_array( $original_id ) || is_object( $original_id ) ) {
                return false;
            }
            $post_ids = get_transient( 'importpostids' );
            if ( ! is_array( $post_ids ) ) {
                $post_ids = array();
            }
            if ( $new_id ) {
                if ( ! isset( $post_ids[ $original_id ] ) ) {
                    $this->log( 'Insert old ID ' . $original_id . ' as new ID: ' . $new_id );
                } else if ( $post_ids[ $original_id ] != $new_id ) {
                    $this->error( 'Replacement OLD ID ' . $original_id . ' overwritten by new ID: ' . $new_id );
                }
                $post_ids[ $original_id ] = $new_id;
                set_transient( 'importpostids', $post_ids, 60 * 60 * 24 );
            } else if ( $original_id && isset( $post_ids[ $original_id ] ) ) {
                return $post_ids[ $original_id ];
            } else if ( $original_id === false ) {
                return $post_ids;
            }

            return false;
        }

        private function _post_orphans( $original_id = false, $missing_parent_id = false ) {
            $post_ids = get_transient( 'postorphans' );
            if ( ! is_array( $post_ids ) ) {
                $post_ids = array();
            }
            if ( $missing_parent_id ) {
                $post_ids[ $original_id ] = $missing_parent_id;
                set_transient( 'postorphans', $post_ids, 60 * 60 * 24 );
            } else if ( $original_id && isset( $post_ids[ $original_id ] ) ) {
                return $post_ids[ $original_id ];
            } else if ( $original_id === false ) {
                return $post_ids;
            }

            return false;
        }

        private function _cleanup_imported_ids() {
            // loop over all attachments and assign the correct post ids to those attachments.

        }

        private $delay_posts = array();

        private function _delay_post_process( $post_type, $post_data ) {
            if ( ! isset( $this->delay_posts[ $post_type ] ) ) {
                $this->delay_posts[ $post_type ] = array();
            }
            $this->delay_posts[ $post_type ][ $post_data['post_id'] ] = $post_data;

        }


        // return the difference in length between two strings
        public function cmpr_strlen( $a, $b ) {
            return strlen( $b ) - strlen( $a );
        }

        private function _process_post_data( $post_type, $post_data, $delayed = 0, $debug = false ) {

            $this->log( " Processing $post_type " . $post_data['post_id'] );
            $original_post_data = $post_data;

            if ( $debug ) {
                echo "HERE\n";
            }
            if ( ! post_type_exists( $post_type ) ) {
                return false;
            }
            if ( ! $debug && $this->_imported_post_id( $post_data['post_id'] ) ) {
                return true; // already done :)
            }

            if ( empty( $post_data['post_title'] ) && empty( $post_data['post_name'] ) ) {
                // this is menu items
                $post_data['post_name'] = $post_data['post_id'];
            }

            $post_data['post_type'] = $post_type;

            $post_parent = (int) $post_data['post_parent'];
            if ( $post_parent ) {
                // if we already know the parent, map it to the new local ID
                if ( $this->_imported_post_id( $post_parent ) ) {
                    $post_data['post_parent'] = $this->_imported_post_id( $post_parent );
                    // otherwise record the parent for later
                } else {
                    $this->_post_orphans( intval( $post_data['post_id'] ), $post_parent );
                    $post_data['post_parent'] = 0;
                }
            }

            // check if already exists
            if ( ! $debug ) {
                if ( empty( $post_data['post_title'] ) && ! empty( $post_data['post_name'] ) ) {
                    global $wpdb;
                    $sql     = "
                    SELECT ID, post_name, post_parent, post_type
                    FROM $wpdb->posts
                    WHERE post_name = %s
                    AND post_type = %s
                    ";
                    $pages   = $wpdb->get_results( $wpdb->prepare( $sql, array(
                        $post_data['post_name'],
                        $post_type,
                    ) ), OBJECT_K );
                    $foundid = 0;
                    foreach ( (array) $pages as $page ) {
                        if ( $page->post_name == $post_data['post_name'] && empty( $page->post_title ) ) {
                            $foundid = $page->ID;
                        }
                    }
                    if ( $foundid ) {
                        $this->_imported_post_id( $post_data['post_id'], $foundid );

                        return true;
                    }
                }
                // dont use post_exists because it will dupe up on media with same name but different slug
                if ( ! empty( $post_data['post_title'] ) && ! empty( $post_data['post_name'] ) ) {
                    global $wpdb;
                    $sql     = "
                    SELECT ID, post_name, post_parent, post_type
                    FROM $wpdb->posts
                    WHERE post_name = %s
                    AND post_title = %s
                    AND post_type = %s
                    ";
                    $pages   = $wpdb->get_results( $wpdb->prepare( $sql, array(
                        $post_data['post_name'],
                        $post_data['post_title'],
                        $post_type,
                    ) ), OBJECT_K );
                    $foundid = 0;
                    foreach ( (array) $pages as $page ) {
                        if ( $page->post_name == $post_data['post_name'] ) {
                            $foundid = $page->ID;
                        }
                    }
                    if ( $foundid ) {
                        $this->_imported_post_id( $post_data['post_id'], $foundid );

                        return true;
                    }
                }
            }

            // backwards compat with old import format.
            if ( isset( $post_data['meta'] ) ) {
                foreach ( $post_data['meta'] as $key => $meta ) {
                    if(is_array($meta) && count($meta) == 1){
                        $single_meta = current($meta);
                        if(!is_array($single_meta)){
                            $post_data['meta'][$key] = $single_meta;
                        }
                    }
                }
            }

            switch ( $post_type ) {
                case 'attachment':
                    // import media via url
                if ( ! empty( $post_data['guid'] ) ) {

                        // check if this has already been imported.
                    $old_guid = $post_data['guid'];
                    if ( $this->_imported_post_id( $old_guid ) ) {
                            return true; // alrady done;
                        }
                        // ignore post parent, we haven't imported those yet.
                        // $file_data = wp_remote_get($post_data['guid']);
                        $remote_url = $post_data['guid'];

                        $post_data['upload_date'] = date( 'Y/m', strtotime( $post_data['post_date_gmt'] ) );
                        if ( isset( $post_data['meta'] ) ) {
                            foreach ( $post_data['meta'] as $key => $meta ) {
                                if ( $key == '_wp_attached_file' ) {
                                    foreach ( (array) $meta as $meta_val ) {
                                        if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta_val, $matches ) ) {
                                            $post_data['upload_date'] = $matches[0];
                                        }
                                    }
                                }
                            }
                        }

                        $upload = $this->_fetch_remote_file( $remote_url, $post_data );

                        if ( ! is_array( $upload ) || is_wp_error( $upload ) ) {
                            // todo: error
                            return false;
                        }

                        if ( $info = wp_check_filetype( $upload['file'] ) ) {
                            $post['post_mime_type'] = $info['type'];
                        } else {
                            return false;
                        }

                        $post_data['guid'] = $upload['url'];

                        // as per wp-admin/includes/upload.php
                        $post_id = wp_insert_attachment( $post_data, $upload['file'] );
                        if($post_id) {

                            if ( ! empty( $post_data['meta'] ) ) {
                                foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
                                    if($meta_key != '_wp_attached_file' && !empty($meta_val)) {
                                        update_post_meta( $post_id, $meta_key, $meta_val );
                                    }
                                }
                            }

                            wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

                            // remap resized image URLs, works by stripping the extension and remapping the URL stub.
                            if ( preg_match( '!^image/!', $info['type'] ) ) {
                                $parts = pathinfo( $remote_url );
                                $name  = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

                                $parts_new = pathinfo( $upload['url'] );
                                $name_new  = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

                                $this->_imported_post_id( $parts['dirname'] . '/' . $name, $parts_new['dirname'] . '/' . $name_new );
                            }
                            $this->_imported_post_id( $post_data['post_id'], $post_id );
                        }

                    }
                    break;
                    default:
                    // work out if we have to delay this post insertion

                    $replace_meta_vals = array(
                    );

                    if ( ! empty( $post_data['meta'] ) && is_array( $post_data['meta'] ) ) {

                        // replace any elementor post data:
                        // fix for double json encoded stuff:
                        foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
                            if ( is_string( $meta_val ) && strlen( $meta_val ) && $meta_val[0] == '[' ) {
                                $test_json = @json_decode( $meta_val, true );
                                if ( is_array( $test_json ) ) {
                                    $post_data['meta'][ $meta_key ] = $test_json;
                                }
                            }
                        }

                        array_walk_recursive( $post_data['meta'], array( $this, '_elementor_id_import' ) );

                        // replace menu data:
                        // work out what we're replacing. a tax, page, term etc..

                        if(!empty($post_data['meta']['_menu_item_menu_item_parent'])) {
                            $new_parent_id = $this->_imported_post_id( $post_data['meta']['_menu_item_menu_item_parent'] );
                            if(!$new_parent_id) {
                                if ( $delayed ) {
                                    // already delayed, unable to find this meta value, skip inserting it
                                    $this->error( 'Unable to find replacement. Continue anyway.... content will most likely break..' );
                                } else {
                                    $this->error( 'Unable to find replacement. Delaying.... ' );
                                    $this->_delay_post_process( $post_type, $original_post_data );
                                    return false;
                                }
                            }
                            $post_data['meta']['_menu_item_menu_item_parent'] = $new_parent_id;
                        }
                        switch($post_data['meta'][ '_menu_item_type' ]){
                            case 'post_type':
                            if(!empty($post_data['meta']['_menu_item_object_id'])) {
                                $new_parent_id = $this->_imported_post_id( $post_data['meta']['_menu_item_object_id'] );
                                if(!$new_parent_id) {
                                    if ( $delayed ) {
                                            // already delayed, unable to find this meta value, skip inserting it
                                        $this->error( 'Unable to find replacement. Continue anyway.... content will most likely break..' );
                                    } else {
                                        $this->error( 'Unable to find replacement. Delaying.... ' );
                                        $this->_delay_post_process( $post_type, $original_post_data );
                                        return false;
                                    }
                                }
                                $post_data['meta']['_menu_item_object_id'] = $new_parent_id;
                            }
                            break;
                            case 'taxonomy':
                            if(!empty($post_data['meta']['_menu_item_object_id'])) {
                                $new_parent_id = $this->_imported_term_id( $post_data['meta']['_menu_item_object_id'] );
                                if(!$new_parent_id) {
                                    if ( $delayed ) {
                                            // already delayed, unable to find this meta value, skip inserting it
                                        $this->error( 'Unable to find replacement. Continue anyway.... content will most likely break..' );
                                    } else {
                                        $this->error( 'Unable to find replacement. Delaying.... ' );
                                        $this->_delay_post_process( $post_type, $original_post_data );
                                        return false;
                                    }
                                }
                                $post_data['meta']['_menu_item_object_id'] = $new_parent_id;
                            }
                            break;
                        }

                        // please ignore this horrible loop below:
                        // it was an attempt to automate different visual composer meta key replacements
                        // but I'm not using visual composer any more, so ignoring it.
                        foreach ( $replace_meta_vals as $meta_key_to_replace => $meta_values_to_replace ) {

                            $meta_keys_to_replace   = explode( '|', $meta_key_to_replace );
                            $success                = false;
                            $trying_to_find_replace = false;
                            foreach ( $meta_keys_to_replace as $meta_key ) {

                                if ( ! empty( $post_data['meta'][ $meta_key ] ) ) {

                                    $meta_val = $post_data['meta'][ $meta_key ];

                                    if ( $debug ) {
                                        echo "Meta key: $meta_key \n";
                                        print_r( $meta_val );
                                    }

                                    // if we're replacing a single post/tax value.
                                    if ( isset( $meta_values_to_replace['post'] ) && $meta_values_to_replace['post'] && (int) $meta_val > 0 ) {
                                        $trying_to_find_replace = true;
                                        $new_meta_val           = $this->_imported_post_id( $meta_val );
                                        if ( $new_meta_val ) {
                                            $post_data['meta'][ $meta_key ] = $new_meta_val;
                                            $success                        = true;
                                        } else {
                                            $success = false;
                                            break;
                                        }
                                    }
                                    if ( isset( $meta_values_to_replace['taxonomy'] ) && $meta_values_to_replace['taxonomy'] && (int) $meta_val > 0 ) {
                                        $trying_to_find_replace = true;
                                        $new_meta_val           = $this->_imported_term_id( $meta_val );
                                        if ( $new_meta_val ) {
                                            $post_data['meta'][ $meta_key ] = $new_meta_val;
                                            $success                        = true;
                                        } else {
                                            $success = false;
                                            break;
                                        }
                                    }
                                    if ( is_array( $meta_val ) && isset( $meta_values_to_replace['posts'] ) ) {

                                        foreach ( $meta_values_to_replace['posts'] as $post_array_key ) {

                                            $this->log( 'Trying to find/replace "' . $post_array_key . '"" in the ' . $meta_key . ' sub array:' );
                                            $this_success = false;
                                            array_walk_recursive( $meta_val, function ( &$item, $key ) use ( &$trying_to_find_replace, $post_array_key, &$success, &$this_success, $post_type, $original_post_data, $meta_key, $delayed ) {
                                                if ( $key == $post_array_key && (int) $item > 0 ) {
                                                    $trying_to_find_replace = true;
                                                    $new_insert_id          = $this->_imported_post_id( $item );
                                                    if ( $new_insert_id ) {
                                                        $success      = true;
                                                        $this_success = true;
                                                        $this->log( 'Found' . $meta_key . ' -> ' . $post_array_key . ' replacement POST ID insert for ' . $item . ' ( as ' . $new_insert_id . ' ) ' );
                                                        $item = $new_insert_id;
                                                    } else {
                                                        $this->error( 'Unable to find ' . $meta_key . ' -> ' . $post_array_key . ' POST ID insert for ' . $item . ' ' );
                                                    }
                                                }
                                            } );
                                            if ( $this_success ) {
                                                $post_data['meta'][ $meta_key ] = $meta_val;
                                            }
                                        }
                                        foreach ( $meta_values_to_replace['taxonomies'] as $post_array_key ) {

                                            $this->log( 'Trying to find/replace "' . $post_array_key . '"" TAXONOMY in the ' . $meta_key . ' sub array:' );
                                            //$this->log(var_export($meta_val,true));

                                            $this_success = false;
                                            array_walk_recursive( $meta_val, function ( &$item, $key ) use ( &$trying_to_find_replace, $post_array_key, &$success, &$this_success, $post_type, $original_post_data, $meta_key, $delayed ) {
                                                if ( $key == $post_array_key && (int) $item > 0 ) {
                                                    $trying_to_find_replace = true;
                                                    $new_insert_id          = $this->_imported_term_id( $item );
                                                    if ( $new_insert_id ) {
                                                        $success      = true;
                                                        $this_success = true;
                                                        $this->log( 'Found' . $meta_key . ' -> ' . $post_array_key . ' replacement TAX ID insert for ' . $item . ' ( as ' . $new_insert_id . ' ) ' );
                                                        $item = $new_insert_id;
                                                    } else {
                                                        $this->error( 'Unable to find ' . $meta_key . ' -> ' . $post_array_key . ' TAX ID insert for ' . $item . ' ' );
                                                    }
                                                }
                                            } );

                                            if ( $this_success ) {
                                                $post_data['meta'][ $meta_key ] = $meta_val;
                                            }
                                        }
                                    }

                                    if ( $success ) {
                                        if ( $debug ) {
                                            echo "Meta key AFTER REPLACE: $meta_key \n";
                                            print_r( $post_data['meta'] );
                                        }
                                    }
                                }
                            }
                            if ( $trying_to_find_replace ) {
                                $this->log( 'Trying to find/replace postmeta "' . $meta_key_to_replace . '" ' );
                                if ( ! $success ) {
                                    // failed to find a replacement.
                                    if ( $delayed ) {
                                        // already delayed, unable to find this meta value, skip inserting it
                                        $this->error( 'Unable to find replacement. Continue anyway.... content will most likely break..' );
                                    } else {
                                        $this->error( 'Unable to find replacement. Delaying.... ' );
                                        $this->_delay_post_process( $post_type, $original_post_data );

                                        return false;
                                    }
                                } else {
                                    $this->log( 'SUCCESSSS ' );
                                }
                            }
                        }
                    }

                    $post_data['post_content'] = $this->_parse_gallery_shortcode_content($post_data['post_content']);

                    // we have to fix up all the visual composer inserted image ids
                    $replace_post_id_keys = array(
                        'parallax_image',
                        'dtbwp_row_image_top',
                        'dtbwp_row_image_bottom',
                        'image',
                        'item', // vc grid
                        'post_id',
                    );
                    foreach ( $replace_post_id_keys as $replace_key ) {
                        if ( preg_match_all( '# ' . $replace_key . '="(\d+)"#', $post_data['post_content'], $matches ) ) {
                            foreach ( $matches[0] as $match_id => $string ) {
                                $new_id = $this->_imported_post_id( $matches[1][ $match_id ] );
                                if ( $new_id ) {
                                    $post_data['post_content'] = str_replace( $string, ' ' . $replace_key . '="' . $new_id . '"', $post_data['post_content'] );
                                } else {
                                    $this->error( 'Unable to find POST replacement for ' . $replace_key . '="' . $matches[1][ $match_id ] . '" in content.' );
                                    if ( $delayed ) {
                                        // already delayed, unable to find this meta value, insert it anyway.

                                    } else {

                                        $this->error( 'Adding ' . $post_data['post_id'] . ' to delay listing.' );
                                        // echo "Delaying post id ".$post_data['post_id']."... \n\n";
                                        $this->_delay_post_process( $post_type, $original_post_data );

                                        return false;
                                    }
                                }
                            }
                        }
                    }
                    $replace_tax_id_keys = array(
                        'taxonomies',
                    );
                    foreach ( $replace_tax_id_keys as $replace_key ) {
                        if ( preg_match_all( '# ' . $replace_key . '="(\d+)"#', $post_data['post_content'], $matches ) ) {
                            foreach ( $matches[0] as $match_id => $string ) {
                                $new_id = $this->_imported_term_id( $matches[1][ $match_id ] );
                                if ( $new_id ) {
                                    $post_data['post_content'] = str_replace( $string, ' ' . $replace_key . '="' . $new_id . '"', $post_data['post_content'] );
                                } else {
                                    $this->error( 'Unable to find TAXONOMY replacement for ' . $replace_key . '="' . $matches[1][ $match_id ] . '" in content.' );
                                    if ( $delayed ) {
                                        // already delayed, unable to find this meta value, insert it anyway.
                                    } else {
                                        // echo "Delaying post id ".$post_data['post_id']."... \n\n";
                                        $this->_delay_post_process( $post_type, $original_post_data );

                                        return false;
                                    }
                                }
                            }
                        }
                    }

                    $post_id = wp_insert_post( $post_data, true );
                    if ( ! is_wp_error( $post_id ) ) {
                        $this->_imported_post_id( $post_data['post_id'], $post_id );
                        // add/update post meta
                        if ( ! empty( $post_data['meta'] ) ) {
                            foreach ( $post_data['meta'] as $meta_key => $meta_val ) {

                                // if the post has a featured image, take note of this in case of remap
                                if ( '_thumbnail_id' == $meta_key ) {
                                    /// find this inserted id and use that instead.
                                    $inserted_id = $this->_imported_post_id( intval( $meta_val ) );
                                    if ( $inserted_id ) {
                                        $meta_val = $inserted_id;
                                    }
                                }
                                // echo "Post meta $meta_key was $meta_val \n\n";

                                update_post_meta( $post_id, $meta_key, $meta_val );

                            }
                        }
                        if ( ! empty( $post_data['terms'] ) ) {
                            $terms_to_set = array();
                            foreach ( $post_data['terms'] as $term_slug => $terms ) {
                                foreach ( $terms as $term ) {
                                    $taxonomy = $term['taxonomy'];
                                    if ( taxonomy_exists( $taxonomy ) ) {
                                        $term_exists = term_exists( $term['slug'], $taxonomy );
                                        $term_id     = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
                                        if ( ! $term_id ) {
                                            if ( ! empty( $term['parent'] ) ) {
                                                // see if we have imported this yet?
                                                $term['parent'] = $this->_imported_term_id( $term['parent'] );
                                            }
                                            $t = wp_insert_term( $term['name'], $taxonomy, $term );
                                            if ( ! is_wp_error( $t ) ) {
                                                $term_id = $t['term_id'];
                                            } else {
                                                // todo - error
                                                continue;
                                            }
                                        }
                                        $this->_imported_term_id( $term['term_id'], $term_id );
                                        // add the term meta.
                                        if($term_id && !empty($term['meta']) && is_array($term['meta'])){
                                            foreach($term['meta'] as $meta_key => $meta_val){
                                                // we have to replace certain meta_key/meta_val
                                                // e.g. thumbnail id from woocommerce product categories.
                                                switch($meta_key){
                                                    case 'thumbnail_id':
                                                    if( $new_meta_val = $this->_imported_post_id($meta_val) ){
                                                            // use this new id.
                                                        $meta_val = $new_meta_val;
                                                    }
                                                    break;
                                                }
                                                update_term_meta( $term_id, $meta_key, $meta_val );
                                            }
                                        }
                                        $terms_to_set[ $taxonomy ][] = intval( $term_id );
                                    }
                                }
                            }
                            foreach ( $terms_to_set as $tax => $ids ) {
                                wp_set_post_terms( $post_id, $ids, $tax );
                            }
                        }

                        // procses visual composer just to be sure.
                        if ( strpos( $post_data['post_content'], '[vc_' ) !== false ) {
                            $this->vc_post( $post_id );
                        }
                        if ( !empty($post_data['meta']['_elementor_data']) || !!empty($post_data['meta']['_elementor_css']) ) {
                            $this->elementor_post( $post_id );
                        }
                    }

                    break;
                }

                return true;
            }

            private function _parse_gallery_shortcode_content($content){
            // we have to format the post content. rewriting images and gallery stuff
                $replace      = $this->_imported_post_id();
                $urls_replace = array();
                foreach ( $replace as $key => $val ) {
                    if ( $key && $val && ! is_numeric( $key ) && ! is_numeric( $val ) ) {
                        $urls_replace[ $key ] = $val;
                    }
                }
                if ( $urls_replace ) {
                    uksort( $urls_replace, array( &$this, 'cmpr_strlen' ) );
                    foreach ( $urls_replace as $from_url => $to_url ) {
                        $content = str_replace( $from_url, $to_url, $content );
                    }
                }
                if ( preg_match_all( '#\[gallery[^\]]*\]#', $content, $matches ) ) {
                    foreach ( $matches[0] as $match_id => $string ) {
                        if ( preg_match( '#ids="([^"]+)"#', $string, $ids_matches ) ) {
                            $ids = explode( ',', $ids_matches[1] );
                            foreach ( $ids as $key => $val ) {
                                $new_id = $val ? $this->_imported_post_id( $val ) : false;
                                if ( ! $new_id ) {
                                    unset( $ids[ $key ] );
                                } else {
                                    $ids[ $key ] = $new_id;
                                }
                            }
                            $new_ids                   = implode( ',', $ids );
                            $content = str_replace( $ids_matches[0], 'ids="' . $new_ids . '"', $content );
                        }
                    }
                }
            // contact form 7 id fixes.
                if ( preg_match_all( '#\[contact-form-7[^\]]*\]#', $content, $matches ) ) {
                    foreach ( $matches[0] as $match_id => $string ) {
                        if ( preg_match( '#id="(\d+)"#', $string, $id_match ) ) {
                            $new_id = $this->_imported_post_id( $id_match[1] );
                            if ( $new_id ) {
                                $content = str_replace( $id_match[0], 'id="' . $new_id . '"', $content );
                            } else {
                            // no imported ID found. remove this entry.
                                $content = str_replace( $matches[0], '(insert contact form here)', $content );
                            }
                        }
                    }
                }
                return $content;
            }

            private function _elementor_id_import( &$item, $key ) {
                if ( $key == 'id' && ! empty( $item ) && is_numeric( $item ) ) {
                // check if this has been imported before
                    $new_meta_val = $this->_imported_post_id( $item );
                    if ( $new_meta_val ) {
                        $item = $new_meta_val;
                    }
                }
                if ( $key == 'page' && ! empty( $item ) ) {

                    if ( false !== strpos( $item, 'p.' ) ) {
                        $new_id = str_replace('p.', '', $item);
                    // check if this has been imported before
                        $new_meta_val = $this->_imported_post_id( $new_id );
                        if ( $new_meta_val ) {
                            $item = 'p.' . $new_meta_val;
                        }
                    }else if(is_numeric($item)){
                    // check if this has been imported before
                        $new_meta_val = $this->_imported_post_id( $item );
                        if ( $new_meta_val ) {
                            $item = $new_meta_val;
                        }
                    }
                }
                if ( $key == 'post_id' && ! empty( $item ) && is_numeric( $item ) ) {
                // check if this has been imported before
                    $new_meta_val = $this->_imported_post_id( $item );
                    if ( $new_meta_val ) {
                        $item = $new_meta_val;
                    }
                }
                if ( $key == 'url' && ! empty( $item ) && strstr( $item, 'ocalhost' ) ) {
                // check if this has been imported before
                    $new_meta_val = $this->_imported_post_id( $item );
                    if ( $new_meta_val ) {
                        $item = $new_meta_val;
                    }
                }
                if ( ($key == 'shortcode' || $key == 'editor') && ! empty( $item ) ) {
                // we have to fix the [contact-form-7 id=133] shortcode issue.
                    $item = $this->_parse_gallery_shortcode_content($item);

                }
            }

            private function _content_install_type() {
                $post_type = ! empty( $_POST['content'] ) ? $_POST['content'] : false;
                $all_data  = $this->_get_json( 'default.json' );
                if ( ! $post_type || ! isset( $all_data[ $post_type ] ) ) {
                    return false;
                }
                $limit = 10 + ( isset( $_REQUEST['retry_count'] ) ? (int) $_REQUEST['retry_count'] : 0 );
                $x     = 0;
                foreach ( $all_data[ $post_type ] as $post_data ) {

                    $this->_process_post_data( $post_type, $post_data );

                    if ( $x ++ > $limit ) {
                        return array( 'retry' => 1, 'retry_count' => $limit );
                    }
                }

                $this->_handle_delayed_posts();

                $this->_handle_post_orphans();

            // now we have to handle any custom SQL queries. This is needed for the events manager to store location and event details.
                $sql = $this->_get_sql( basename( $post_type ) . '.sql' );
                if ( $sql ) {
                    global $wpdb;
                // do a find-replace with certain keys.
                    if ( preg_match_all( '#__POSTID_(\d+)__#', $sql, $matches ) ) {
                        foreach ( $matches[0] as $match_id => $match ) {
                            $new_id = $this->_imported_post_id( $matches[1][ $match_id ] );
                            if ( ! $new_id ) {
                                $new_id = 0;
                            }
                            $sql = str_replace( $match, $new_id, $sql );
                        }
                    }
                    $sql  = str_replace( '__DBPREFIX__', $wpdb->prefix, $sql );
                    $bits = preg_split( "/;(\s*\n|$)/", $sql );
                    foreach ( $bits as $bit ) {
                        $bit = trim( $bit );
                        if ( $bit ) {
                            $wpdb->query( $bit );
                        }
                    }
                }

                return true;

            }

            private function _handle_post_orphans() {
                $orphans = $this->_post_orphans();
                foreach ( $orphans as $original_post_id => $original_post_parent_id ) {
                    if ( $original_post_parent_id ) {
                        if ( $this->_imported_post_id( $original_post_id ) && $this->_imported_post_id( $original_post_parent_id ) ) {
                            $post_data                = array();
                            $post_data['ID']          = $this->_imported_post_id( $original_post_id );
                            $post_data['post_parent'] = $this->_imported_post_id( $original_post_parent_id );
                            wp_update_post( $post_data );
                        $this->_post_orphans( $original_post_id, 0 ); // ignore future
                    }
                }
            }
        }

        private function _handle_delayed_posts( $last_delay = false ) {

            $this->log( ' ---- Processing ' . count( $this->delay_posts, COUNT_RECURSIVE ) . ' delayed posts' );
            for ( $x = 1; $x < 4; $x ++ ) {
                foreach ( $this->delay_posts as $delayed_post_type => $delayed_post_datas ) {
                    foreach ( $delayed_post_datas as $delayed_post_id => $delayed_post_data ) {
                        if ( $this->_imported_post_id( $delayed_post_data['post_id'] ) ) {
                            $this->log( $x . ' - Successfully processed ' . $delayed_post_type . ' ID ' . $delayed_post_data['post_id'] . ' previously.' );
                            unset( $this->delay_posts[ $delayed_post_type ][ $delayed_post_id ] );
                            $this->log( ' ( ' . count( $this->delay_posts, COUNT_RECURSIVE ) . ' delayed posts remain ) ' );
                        } else if ( $this->_process_post_data( $delayed_post_type, $delayed_post_data, $last_delay ) ) {
                            $this->log( $x . ' - Successfully found delayed replacement for ' . $delayed_post_type . ' ID ' . $delayed_post_data['post_id'] . '.' );
                            // successfully inserted! don't try again.
                            unset( $this->delay_posts[ $delayed_post_type ][ $delayed_post_id ] );
                            $this->log( ' ( ' . count( $this->delay_posts, COUNT_RECURSIVE ) . ' delayed posts remain ) ' );
                        }
                    }
                }
            }
        }

        private function _fetch_remote_file( $url, $post ) {
            // extract the file name and extension from the url
            $file_name  = basename( $url );
            $local_file = trailingslashit( get_template_directory() ) . 'images/stock/' . $file_name;
            $upload     = false;
            if ( is_file( $local_file ) && filesize( $local_file ) > 0 ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                WP_Filesystem();
                global $wp_filesystem;
                $file_data = $wp_filesystem->get_contents( $local_file );
                $upload    = wp_upload_bits( $file_name, 0, $file_data, $post['upload_date'] );
                if ( $upload['error'] ) {
                    return new WP_Error( 'upload_dir_error', $upload['error'] );
                }
            }

            if ( ! $upload || $upload['error'] ) {
                // get placeholder file in the upload dir with a unique, sanitized filename
                $upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
                if ( $upload['error'] ) {
                    return new WP_Error( 'upload_dir_error', $upload['error'] );
                }

                // fetch the remote url and write it to the placeholder file
                //$headers = wp_get_http( $url, $upload['file'] );

                $max_size = (int) apply_filters( 'import_attachment_size_limit', 0 );

                // we check if this file is uploaded locally in the source folder.
                $response = wp_remote_get( $url );
                if ( is_array( $response ) && ! empty( $response['body'] ) && $response['response']['code'] == '200' ) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                    $headers = $response['headers'];
                    WP_Filesystem();
                    global $wp_filesystem;
                    $wp_filesystem->put_contents( $upload['file'], $response['body'] );
                    //
                } else {
                    // required to download file failed.
                    @unlink( $upload['file'] );

                    return new WP_Error( 'import_file_error', esc_html__( 'Remote server did not respond','gostudy' ) );
                }

                $filesize = filesize( $upload['file'] );

                if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
                    @unlink( $upload['file'] );

                    return new WP_Error( 'import_file_error', esc_html__( 'Remote file is incorrect size' ,'gostudy') );
                }

                if ( 0 == $filesize ) {
                    @unlink( $upload['file'] );

                    return new WP_Error( 'import_file_error', esc_html__( 'Zero size file downloaded','gostudy' ) );
                }

                if ( ! empty( $max_size ) && $filesize > $max_size ) {
                    @unlink( $upload['file'] );

                    return new WP_Error( 'import_file_error', sprintf( __( 'Remote file is too large, limit is %s','gostudy' ), size_format( $max_size ) ) );
                }
            }

            // keep track of the old and new urls so we can substitute them later
            $this->_imported_post_id( $url, $upload['url'] );
            $this->_imported_post_id( $post['guid'], $upload['url'] );
            // keep track of the destination if the remote url is redirected somewhere else
            if ( isset( $headers['x-final-location'] ) && $headers['x-final-location'] != $url ) {
                $this->_imported_post_id( $headers['x-final-location'], $upload['url'] );
            }

            return $upload;
        }
        

        /**
         * Payments Step
         */
        public function envato_setup_updates() {
            ?>
            <h1><?php esc_html_e( 'Theme Updates','gostudy' ); ?></h1>
            <?php if ( function_exists( 'envato_market' ) ) { ?>
                <form method="post">
                    <?php
                    $option = envato_market()->get_options();

                    $my_items = array();
                    if ( $option && ! empty( $option['items'] ) ) {
                        foreach ( $option['items'] as $item ) {
                            if ( ! empty( $item['oauth'] ) && ! empty( $item['token_data']['expires'] ) && $item['oauth'] == $this->envato_username && $item['token_data']['expires'] >= time() ) {
                                // token exists and is active
                                $my_items[] = $item;
                            }
                        }
                    }
                    if ( count( $my_items ) ) {
                        ?>
                        <p><?php esc_html_e( 'Thanks! Theme updates have been enabled for the following items:','gostudy' ); ?></p>
                        <ul>
                            <?php foreach ( $my_items as $item ) { ?>
                                <li><?php echo esc_html( $item['name'] ); ?></li>
                            <?php } ?>
                        </ul>
                        <p><?php esc_html_e( 'When an update becomes available it will show in the Dashboard with an option to install.','gostudy' ); ?></p>
                        <p><?php esc_html_e( "Change settings from the 'Envato Market' menu in the WordPress Dashboard.",'gostudy' ); ?></p>

                        <p class="envato-setup-actions step">
                            <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                             class="button button-large button-next button-primary"><?php esc_html_e( 'Continue','gostudy' ); ?></a>
                         </p>
                         <?php
                     } else {
                        ?>
                        <p><?php esc_html_e( 'ThemeForest account to enable Theme Updates. We update themes when a new feature is added or a bug is fixed. It is highly recommended to enable Theme Updates.','gostudy' ); ?></p>
                        <p><?php esc_html_e( "When an update becomes available it will show in the Dashboard with an option to install.",'gostudy' ); ?></p>
                        <p>
                            <em><?php esc_html_e( 'If you have any questions please ','gostudy' ); ?><a
                            href="<?php echo esc_url('https://raistheme.com/support/'); ?>" target="_blank"><?php esc_html_e( 'Contact us.','gostudy' ); ?></a></em>
                        </p>
                        <p class="envato-setup-actions step">

                            <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                             class="button button-primary button-large button-next"><?php esc_html_e( 'Continue','gostudy' ); ?></a>
                             
<!--                             <input type="submit" class="button-primary button button-large button-next"
                            value="<?php ///esc_attr_e( 'Login with Envato','gostudy' ); ?>" name="save_step"/> -->

                             <?php wp_nonce_field( 'envato-setup' ); ?>
                         </p>
                     <?php } ?>
                 </form>
             <?php } else { ?>
                <?php esc_html_e( 'Please ensure the Envato Market plugin has been installed correctly.','gostudy' ); ?> <a
                href="<?php echo esc_url( $this->get_step_link( 'default_plugins' ) ); ?>"><?php esc_html_e( 'Return to Required
                Plugins installer.','gostudy' ); ?></a>
            <?php } ?>
            <?php
        }

        /**
         * Payments Step save
         */
        public function envato_setup_updates_save() {
            check_admin_referer( 'envato-setup' );

            // redirect to our custom login URL to get a copy of this token.
            $url = $this->get_oauth_login_url( $this->get_step_link( 'updates' ) );

            wp_redirect( esc_url_raw( $url ) );
            exit;
        }


        public function envato_setup_customize() {
            ?>

            <h1><?php esc_html_e( 'Theme Customization','gostudy' ); ?></h1>
            <p><?php esc_html_e( 'Most changes to the website can be made through the Gostudy > Theme Options menu from the WordPress
            dashboard. These include Whole site global settings, typography, colors, styles, etc.','gostudy' ); ?> 
        </p>
        
    <p><?php esc_html_e( 'To change the Sidebars go to Appearance > Widgets. Here widgets can be "drag &amp; drop" into sidebars. More details in documentation.','gostudy' ); ?></p>
    <p>
        <em><?php esc_html_e( 'Advanced Users: If you are going to make changes to the theme source code please use a','gostudy' ); ?> <a
            href="//developer.wordpress.org/themes/advanced-topics/child-themes/" target="_blank"><?php esc_html_e( 'Child Theme','gostudy' ); ?></a> <?php esc_html_e( 'rather than
                modifying the main theme HTML/CSS/PHP code. This allows the parent theme to receive updates without
                overwriting your source code changes.','gostudy' ); ?> <br/> <?php esc_html_e( 'See','gostudy' ); ?> <code><?php esc_html_e( 'gostudy-child.zip','gostudy' ); ?></code> <?php esc_html_e( 'in the main theme folder.','gostudy' ); ?></em>
            </p>

            <p class="envato-setup-actions step">
                <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                 class="button button-primary button-large button-next"><?php esc_html_e( 'Continue','gostudy' ); ?></a>
             </p>

             <?php
         }

         public function envato_setup_help_support() {
            ?>
            <h1><?php esc_html_e( 'Help and Support','gostudy' ); ?></h1>
            <p><?php esc_html_e( 'This theme comes with 6 months item support from purchase date (with the option to extend this period).
                This license allows you to use this theme on a single website. Please purchase an additional license to
                use this theme on another website.','gostudy' ); ?></p>

                <p class="greendata"><?php esc_html_e( 'Item Support can be accessed from','gostudy' ); ?> <a href="<?php echo esc_url('https://raistheme.com/support'); ?>" target="_blank"><?php esc_html_e( 'raistheme Help','gostudy' ); ?></a>
                 <?php esc_html_e( 'and includes:','gostudy' ); ?></p>
                 <ul>
                    <li><?php esc_html_e( 'Availability of the author to answer questions','gostudy' ); ?></li>
                    <li><?php esc_html_e( 'Answering technical questions about item features','gostudy' ); ?></li>
                    <li><?php esc_html_e( 'Assistance with reported bugs and issues','gostudy' ); ?></li>
                    <li><?php esc_html_e( 'Help with bundled 3rd party plugins','gostudy' ); ?></li>
                </ul>

                <p class="reddata"><?php esc_html_e( 'Item Support DOES NOT Include:', 'gostudy' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'Customization services', 'gostudy' ); ?>
                </li>
            </li>
        </ul>
        <p><?php esc_html_e( 'More details about item support can be found in the ThemeForest', 'gostudy' ); ?> <a
            href="https://themeforest.net/page/item_support_policy" target="_blank"><?php esc_html_e( 'Item Support Polity.', 'gostudy' ); ?></a> </p>
            <p class="envato-setup-actions step">
                <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                 class="button button-primary button-large button-next"><?php esc_html_e( 'Continue','gostudy' ); ?></a>
                 <?php wp_nonce_field( 'envato-setup' ); ?>
             </p>
             <?php
         }

        /**
         * Final step
         */
        public function envato_setup_ready() {

            update_option( 'envato_setup_complete', time() );
            update_option( 'dtbwp_update_notice', strtotime('-4 days') );
            ?>
            <a href="https//twitter.com/share" class="twitter-share-button"
            data-url="https://themeforest.net/user/raistheme/portfolio"
            data-text="<?php echo esc_attr( 'I just installed the ' . wp_get_theme() . ' #WordPress theme from #ThemeForest' ); ?>"
            data-via="EnvatoMarket" data-size="large">Tweet</a>
            <script>!function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (!d.getElementById(id)) {
                    js = d.createElement(s);
                    js.id = id;
                    js.src = "//platform.twitter.com/widgets.js";
                    fjs.parentNode.insertBefore(js, fjs);
                }
            }(document, "script", "twitter-wjs");</script>

            <h1><?php esc_html_e( 'Your website is almost ready!','gostudy' ); ?></h1>

            <p class="greendata"><?php esc_html_e( 'Congratulations! The theme and the required plugins have been activated. Click the "Import Dummy Data" button for demo import.','gostudy' ); ?></p>
            <p><?php esc_html_e( 'Please come back and','gostudy'); ?> <a href="<?php echo esc_url( __( 'https://themeforest.net/downloads', 'gostudy' ) ); ?>" target="_blank"><?php esc_html_e( 'leave a 5-star rating.','gostudy'); ?></a>
                <?php esc_html_e( 'if you are happy with this theme','gostudy'); ?> <br/><?php esc_html_e( 'Follow','gostudy'); ?> 
                <a href="<?php echo esc_url( __( 'https://web.facebook.com/raistheme/', 'gostudy' ) ); ?>" target="_blank"><?php esc_html_e( '@RaisTheme','gostudy'); ?></a>
                <?php esc_html_e( 'on Facebook to see updates. Thanks!','gostudy'); ?> </p>

                <div class="envato-setup-next-steps">
                    <div class="envato-setup-next-steps-first">
                        <h2><?php esc_html_e( 'Next Steps','gostudy' ); ?></h2>
                        <ul>
                            <?php $url = admin_url().'themes.php?page=pt-one-click-demo-import'; ?>
                            <li class="setup-product"><a class="button button-primary button-large"
                               href="<?php echo esc_url($url); ?>"><?php esc_html_e( 'Import Dummy Data', 'gostudy' ); ?></a>
                           </li>
                           <li class="setup-product"><a class="button button-next button-large"
                               href="<?php echo esc_url( home_url('/') ); ?>"><?php esc_html_e( 'View your new website!','gostudy' ); ?></a>
                           </li>
                       </ul>
                   </div>
                   <div class="envato-setup-next-steps-last">
                    <h2><?php esc_html_e( 'More Resources','gostudy' ); ?></h2>
                    <ul>
                        <li class="documentation"><a href="<?php echo esc_url( 'https://raistheme.com/docs/gostudy/' ); ?>"
                           target="_blank"><?php esc_html_e( 'Read the Theme Documentation','gostudy'); ?></a>
                       </li>
                       <li class="howto"><a href="<?php echo esc_url('https://wordpress.org/support/' ); ?>"
                           target="_blank"><?php esc_html_e( 'Learn how to use WordPress','gostudy' ); ?></a>
                       </li>
                       <li class="rating"><a href="<?php echo esc_url('https://themeforest.net/downloads' ); ?>"
                          target="_blank"><?php esc_html_e( 'Leave an Item Rating','gostudy' ); ?></a></li>
                          <li class="support"><a href="<?php echo esc_url( 'https://raistheme.com/support/' ); ?>"
                             target="_blank"><?php esc_html_e( 'Get Help and Support','gostudy' ); ?></a></li>
                         </ul>
                     </div>
                 </div>
                 <?php
             }

             public function envato_market_admin_init() {

                if ( ! function_exists( 'envato_market' ) ) {
                    return;
                }

                global $wp_settings_sections;
                if ( ! isset( $wp_settings_sections[ envato_market()->get_slug() ] ) ) {
                // means we're running the admin_init hook before envato market gets to setup settings area.
                // good - this means our oauth prompt will appear first in the list of settings blocks
                    register_setting( envato_market()->get_slug(), envato_market()->get_option_name() );
                }

            // pull our custom options across to envato.
                $option         = get_option( 'envato_setup_wizard', array() );
                $envato_options = envato_market()->get_options();
                $envato_options = $this->_array_merge_recursive_distinct( $envato_options, $option );
                if(!empty($envato_options['items'])) {
                    foreach($envato_options['items'] as $key => $item) {
                        if(!empty($item['id']) && is_string($item['id'])) {
                            $envato_options['items'][$key]['id'] = (int)$item['id'];
                        }
                    }
                }
                update_option( envato_market()->get_option_name(), $envato_options );

            //add_thickbox();

                if ( ! empty( $_POST['oauth_session'] ) && ! empty( $_POST['bounce_nonce'] ) && wp_verify_nonce( $_POST['bounce_nonce'], 'envato_oauth_bounce_' . $this->envato_username ) ) {
                // request the token from our bounce url.
                    $my_theme    = wp_get_theme();
                    $oauth_nonce = get_option( 'envato_oauth_' . $this->envato_username );
                    if ( ! $oauth_nonce ) {
                    // this is our 'private key' that is used to request a token from our api bounce server.
                    // only hosts with this key are allowed to request a token and a refresh token
                    // the first time this key is used, it is set and locked on the server.
                        $oauth_nonce = wp_create_nonce( 'envato_oauth_nonce_' . $this->envato_username );
                        update_option( 'envato_oauth_' . $this->envato_username, $oauth_nonce );
                    }
                    $response = wp_remote_post( $this->oauth_script, array(
                        'method'      => 'POST',
                        'timeout'     => 15,
                        'redirection' => 1,
                        'httpversion' => '1.0',
                        'blocking'    => true,
                        'headers'     => array(),
                        'body'        => array(
                            'oauth_session' => $_POST['oauth_session'],
                            'oauth_nonce'   => $oauth_nonce,
                            'get_token'     => 'yes',
                            'url'           => home_url('/'),
                            'theme'         => $my_theme->get( 'Name' ),
                            'version'       => $my_theme->get( 'Version' ),
                        ),
                        'cookies'     => array(),
                    )
                );
                    if ( is_wp_error( $response ) ) {
                        $error_message = $response->get_error_message();
                        $class         = 'error';
                        echo "<div class=\"$class\"><p>" . sprintf( __( 'Something went wrong while trying to retrieve oauth token: %s','gostudy' ), $error_message ) . '</p></div>';
                    } else {
                        $token  = @json_decode( wp_remote_retrieve_body( $response ), true );
                        $result = false;
                        if ( is_array( $token ) && ! empty( $token['access_token'] ) ) {
                            $token['oauth_session'] = $_POST['oauth_session'];
                            $result                 = $this->_manage_oauth_token( $token );
                        }
                        if ( $result !== true ) {
                            echo 'Failed to get oAuth token. Please go back and try again';
                            exit;
                        }
                    }
                }

                add_settings_section(
                    envato_market()->get_option_name() . '_' . $this->envato_username . '_oauth_login',
                    sprintf( __( 'Login for %s updates','gostudy' ), $this->envato_username ),
                    array( $this, 'render_oauth_login_description_callback' ),
                    envato_market()->get_slug()
                );
            // Items setting.
                add_settings_field(
                    $this->envato_username . 'oauth_keys',
                    __( 'oAuth Login','gostudy' ),
                    array( $this, 'render_oauth_login_fields_callback' ),
                    envato_market()->get_slug(),
                    envato_market()->get_option_name() . '_' . $this->envato_username . '_oauth_login'
                );
            }

            private static $_current_manage_token = false;

            private function _manage_oauth_token( $token ) {
                if ( is_array( $token ) && ! empty( $token['access_token'] ) ) {
                    if ( self::$_current_manage_token == $token['access_token'] ) {
                    return false; // stop loops when refresh auth fails.
                }
                self::$_current_manage_token = $token['access_token'];
                // yes! we have an access token. store this in our options so we can get a list of items using it.
                $option = get_option( 'envato_setup_wizard', array() );
                if ( ! is_array( $option ) ) {
                    $option = array();
                }
                if ( empty( $option['items'] ) ) {
                    $option['items'] = array();
                }
                // check if token is expired.
                if ( empty( $token['expires'] ) ) {
                    $token['expires'] = time() + 3600;
                }
                if ( $token['expires'] < time() + 120 && ! empty( $token['oauth_session'] ) ) {
                    // time to renew this token!
                    $my_theme    = wp_get_theme();
                    $oauth_nonce = get_option( 'envato_oauth_' . $this->envato_username );
                    $response    = wp_remote_post( $this->oauth_script, array(
                        'method'      => 'POST',
                        'timeout'     => 10,
                        'redirection' => 1,
                        'httpversion' => '1.0',
                        'blocking'    => true,
                        'headers'     => array(),
                        'body'        => array(
                            'oauth_session' => $token['oauth_session'],
                            'oauth_nonce'   => $oauth_nonce,
                            'refresh_token' => 'yes',
                            'url'           => home_url('/'),
                            'theme'         => $my_theme->get( 'Name' ),
                            'version'       => $my_theme->get( 'Version' ),
                        ),
                        'cookies'     => array(),
                    )
                );
                    if ( is_wp_error( $response ) ) {
                        $error_message = $response->get_error_message();
                        // we clear any stored tokens which prompts the user to re-auth with the update server.
                        $this->_clear_oauth();
                    } else {
                        $new_token = @json_decode( wp_remote_retrieve_body( $response ), true );
                        $result    = false;
                        if ( is_array( $new_token ) && ! empty( $new_token['new_token'] ) ) {
                            $token['access_token'] = $new_token['new_token'];
                            $token['expires']      = time() + 3600;
                        }else {
                            //refresh failed, we clear any stored tokens which prompts the user to re-register.
                            $this->_clear_oauth();
                        }
                    }
                }
                // use this token to get a list of purchased items
                // add this to our items array.
                $response                    = envato_market()->api()->request( '//api.envato.com/v3/market/buyer/purchases', array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $token['access_token'],
                    ),
                ) );
                self::$_current_manage_token = false;
                if ( is_array( $response ) && is_array( $response['purchases'] ) ) {
                    // up to here, add to items array
                    foreach ( $response['purchases'] as $purchase ) {
                        // check if this item already exists in the items array.
                        $exists = false;
                        foreach ( $option['items'] as $id => $item ) {
                            if ( ! empty( $item['id'] ) && $item['id'] == $purchase['item']['id'] ) {
                                $exists = true;
                                // update token.
                                $option['items'][ $id ]['token']      = $token['access_token'];
                                $option['items'][ $id ]['token_data'] = $token;
                                $option['items'][ $id ]['oauth']      = $this->envato_username;
                                if ( ! empty( $purchase['code'] ) ) {
                                    $option['items'][ $id ]['purchase_code'] = $purchase['code'];
                                }
                            }
                        }
                        if ( ! $exists ) {
                            $option['items'][] = array(
                                'id'            => '' . $purchase['item']['id'],
                                // item id needs to be a string for market download to work correctly.
                                'name'          => $purchase['item']['name'],
                                'token'         => $token['access_token'],
                                'token_data'    => $token,
                                'oauth'         => $this->envato_username,
                                'type'          => ! empty( $purchase['item']['wordpress_theme_metadata'] ) ? 'theme' : 'plugin',
                                'purchase_code' => ! empty( $purchase['code'] ) ? $purchase['code'] : '',
                            );
                        }
                    }
                } else {
                    return false;
                }
                if ( ! isset( $option['oauth'] ) ) {
                    $option['oauth'] = array();
                }
                // store our 1 hour long token here. we can refresh this token when it comes time to use it again (i.e. during an update)
                $option['oauth'][ $this->envato_username ] = $token;
                update_option( 'envato_setup_wizard', $option );

                $envato_options = envato_market()->get_options();
                $envato_options = $this->_array_merge_recursive_distinct( $envato_options, $option );
                update_option( envato_market()->get_option_name(), $envato_options );
                envato_market()->items()->set_themes( true );
                envato_market()->items()->set_plugins( true );

                return true;
            } else {
                return false;
            }
        }

        public function _clear_oauth() {
            $envato_options = envato_market()->get_options();
            unset( $envato_options['oauth'] );
            update_option( envato_market()->get_option_name(), $envato_options );
        }



        public function ajax_notice_handler() {
            check_ajax_referer( 'dtnwp-ajax-nonce', 'security' );
            // Store it in the options table
            update_option( 'dtbwp_update_notice', time() );
        }

        public function admin_theme_auth_notice() {


            if(function_exists('envato_market')) {
                $option = envato_market()->get_options();

                $envato_items = get_option( 'envato_setup_wizard', array() );

                if ( !$option || empty($option['oauth']) || empty( $option['oauth'][ $this->envato_username ] ) || empty($envato_items) || empty($envato_items['items']) || !envato_market()->items()->themes( 'purchased' )) {

                    // we show an admin notice if it hasn't been dismissed
                    $dissmissed_time = get_option('dtbwp_update_notice', false );

                    if ( ! $dissmissed_time || $dissmissed_time < strtotime('-7 days') ) {
                        // Added the class "notice-my-class" so jQuery pick it up and pass via AJAX,
                        // and added "data-notice" attribute in order to track multiple / different notices
                        // multiple dismissible notice states ?>
                        
                        <div class="notice notice-warning notice-dtbwp-themeupdates is-dismissible">
    
                            <p>
                            
                
                                <?php printf( __( 'Configure the Envato Market plugin to get the theme update notification.','gostudy' ),  esc_url('#') ); ?> 
                 
                                <?php printf( __( 'If you need any help, please do not hesitate to contact our support desk. <a target="_blank" class="" href="%s">https://raistheme.com/support/ </a>','gostudy' ),  esc_url('https://raistheme.com/support/') ); ?> 
                            </p>      
                        </div>
                        <script type="text/javascript">
                            jQuery(function($) {
                                $( document ).on( 'click', '.notice-dtbwp-themeupdates .notice-dismiss', function () {
                                    $.ajax( ajaxurl,
                                    {
                                        type: 'POST',
                                        data: {
                                            action: 'dtbwp_update_notice_handler',
                                            security: '<?php echo wp_create_nonce( "dtnwp-ajax-nonce" ); ?>'
                                        }
                                    } );
                                } );
                            });
                        </script>
                    <?php }

                }
            }
        }
        /**
         * @param $array1
         * @param $array2
         *
         * @return mixed
         *
         *
         * @since    1.1.4
         */
        private function _array_merge_recursive_distinct( $array1, $array2 ) {
            $merged = $array1;
            foreach ( $array2 as $key => &$value ) {
                if ( is_array( $value ) && isset( $merged [ $key ] ) && is_array( $merged [ $key ] ) ) {
                    $merged [ $key ] = $this->_array_merge_recursive_distinct( $merged [ $key ], $value );
                } else {
                    $merged [ $key ] = $value;
                }
            }

            return $merged;
        }

        /**
         * @param $args
         * @param $url
         *
         * @return mixed
         *
         * Filter the WordPress HTTP call args.
         * We do this to find any queries that are using an expired token from an oAuth bounce login.
         * Since these oAuth tokens only last 1 hour we have to hit up our server again for a refresh of that token before using it on the Envato API.
         * Hacky, but only way to do it.
         */
        public function envato_market_http_request_args( $args, $url ) {
            if ( strpos( $url, 'api.envato.com' ) && function_exists( 'envato_market' ) ) {
                // we have an API request.
                // check if it's using an expired token.
                if ( ! empty( $args['headers']['Authorization'] ) ) {
                    $token = str_replace( 'Bearer ', '', $args['headers']['Authorization'] );
                    if ( $token ) {
                        // check our options for a list of active oauth tokens and see if one matches, for this envato username.
                        $option = envato_market()->get_options();
                        if ( $option && ! empty( $option['oauth'][ $this->envato_username ] ) && $option['oauth'][ $this->envato_username ]['access_token'] == $token && $option['oauth'][ $this->envato_username ]['expires'] < time() + 120 ) {
                            // we've found an expired token for this oauth user!
                            // time to hit up our bounce server for a refresh of this token and update associated data.
                            $this->_manage_oauth_token( $option['oauth'][ $this->envato_username ] );
                            $updated_option = envato_market()->get_options();
                            if ( $updated_option && ! empty( $updated_option['oauth'][ $this->envato_username ]['access_token'] ) ) {
                                // hopefully this means we have an updated access token to deal with.
                                $args['headers']['Authorization'] = 'Bearer ' . $updated_option['oauth'][ $this->envato_username ]['access_token'];
                            }
                        }
                    }
                }
            }

            return $args;
        }

        public function render_oauth_login_description_callback() {
            echo 'If you have purchased items from ' . esc_html( $this->envato_username ) . ' on ThemeForest or CodeCanyon please login here for quick and easy updates.';

        }

        public function render_oauth_login_fields_callback() {
            $option = envato_market()->get_options();
            ?>
            <div class="oauth-login" data-username="<?php echo esc_attr( $this->envato_username ); ?>">
                <a href="<?php echo esc_url( $this->get_oauth_login_url( admin_url( 'admin.php?page=' . envato_market()->get_slug() . '#settings' ) ) ); ?>"
                 class="oauth-login-button button button-primary">Login with Envato to activate updates</a>
             </div>
             <?php
         }

        /// a better filter would be on the post-option get filter for the items array.
        // we can update the token there.

         public function get_oauth_login_url( $return ) {
            return $this->oauth_script . '?bounce_nonce=' . wp_create_nonce( 'envato_oauth_bounce_' . $this->envato_username ) . '&wp_return=' . urlencode( $return );
        }

        /**
         * Helper function
         * Take a path and return it clean
         *
         * @param string $path
         *
         * @since    1.1.2
         */
        public static function cleanFilePath( $path ) {
            $path = str_replace( '', '', str_replace( array( '\\', '\\\\', '//' ), '/', $path ) );
            if ( $path[ strlen( $path ) - 1 ] === '/' ) {
                $path = rtrim( $path, '/' );
            }

            return $path;
        }

        public function is_submenu_page() {
            return ( $this->parent_slug == '' ) ? false : true;
        }
    }

}// if !class_exists

/**
 * Loads the main instance of Envato_Theme_Setup_Wizard to have
 * ability extend class functionality
 *
 * @since 1.1.1
 * @return object Envato_Theme_Setup_Wizard
 */
add_action( 'after_setup_theme', 'envato_theme_setup_wizard', 10 );
if ( ! function_exists( 'envato_theme_setup_wizard' ) ) :
    function envato_theme_setup_wizard() {
        Envato_Theme_Setup_Wizard::get_instance();
    }
endif;
