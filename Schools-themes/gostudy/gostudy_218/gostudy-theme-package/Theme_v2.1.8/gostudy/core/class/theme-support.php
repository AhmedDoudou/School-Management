<?php

defined('ABSPATH') || exit;

/**
* Gostudy Theme Support
*
*
* @class        Gostudy_Theme_Support
* @version      1.0
* @category     Class
* @author       RaisTheme
*/

if (! class_exists('Gostudy_Theme_Support')) {
    class Gostudy_Theme_Support
    {

        private static $instance = null;

        public static function get_instance()
        {
            if (null == self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct()
        {
            if (function_exists('add_theme_support')) {
                add_theme_support('post-thumbnails');
                add_theme_support('automatic-feed-links');
                add_theme_support('revisions');
                add_theme_support('post-formats', ['gallery', 'video', 'quote', 'audio', 'link']);
            }

            // Register Nav Menu
            add_action('init', [$this, 'register_my_menus'] );
            // Add translation file
            add_action('init', [$this, 'enqueue_translation_files'] );
            // Add widget support
            add_action('widgets_init', [$this, 'sidebar_register'] );
        }

        public function register_my_menus()
        {
            register_nav_menus( [
                'main_menu' => esc_html__('Main menu', 'gostudy')
            ] );
        }

        public function enqueue_translation_files()
        {
            load_theme_textdomain('gostudy', get_template_directory() . '/languages/');
        }

        public function sidebar_register()
        {
            // Get List of registered sidebar
            $custom_sidebars = Gostudy_Theme_Helper::get_option('sidebars');

            // Default wrapper for widget and title
            $wrapper_before = '<div id="%1$s" class="widget gostudy_widget %2$s">';
            $wrapper_after = '</div>';
            $title_before = '<div class="title-wrapper"><span class="title">';
            $title_after = '</span></div>';

            // Register custom sidebars
            if (!empty($custom_sidebars)) {
                foreach ($custom_sidebars as $single) {
                    register_sidebar([
                        'name' => esc_attr($single),
                        'id' => "sidebar_".esc_attr(strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $single)))),
                        'description' => esc_html__('Add widget here to appear it in custom sidebar.', 'gostudy'),
                        'before_widget' => $wrapper_before,
                        'after_widget' => $wrapper_after,
                        'before_title' => $title_before,
                        'after_title' => $title_after,
                    ]);
                }
            }

            // Register Footer Sidebar
            $footer_columns = [
                [
                    'name' => esc_html__('Footer Column 1', 'gostudy'),
                    'id' => 'footer_column_1'
                ], [
                    'name' => esc_html__('Footer Column 2', 'gostudy'),
                    'id' => 'footer_column_2'
                ], [
                    'name' => esc_html__('Footer Column 3', 'gostudy'),
                    'id' => 'footer_column_3'
                ], [
                    'name' => esc_html__('Footer Column 4', 'gostudy'),
                    'id' => 'footer_column_4'
                ],
            ];

            foreach ($footer_columns as $footer_column) {
                register_sidebar( [
                    'name' => $footer_column['name'],
                    'id' => $footer_column['id'],
                    'description' => esc_html__('This area will display in footer like a column. Add widget here to appear it in footer column.', 'gostudy'),
                    'before_widget' => $wrapper_before,
                    'after_widget' => $wrapper_after,
                    'before_title' => $title_before,
                    'after_title' => $title_after,
                ] );
            }
            if (class_exists('WooCommerce')) {
                $shop_sidebars = [
                    [
                        'name' => esc_html__('Shop Products', 'gostudy'),
                        'id' => 'shop_products'
                    ], [
                        'name' => esc_html__('Shop Single', 'gostudy'),
                        'id' => 'shop_single'
                    ], [
                        'name' => esc_html__('Shop Filter', 'gostudy'),
                        'id' => 'shop_filter'
                    ]
                ];
                foreach ($shop_sidebars as $shop_sidebar) {
                    register_sidebar( [
                        'name' => $shop_sidebar['name'],
                        'id' => $shop_sidebar['id'],
                        'description' => esc_html__('This sidebar will display in WooCommerce Pages.', 'gostudy'),
                        'before_widget' => $wrapper_before,
                        'after_widget' => $wrapper_after,
                        'before_title' => $title_before,
                        'after_title' => $title_after,
                    ] );
                }
            }

            if ( class_exists('LearnPress') ) {
				$learnpress_sidebars = [
					[
						'name' => esc_html__( 'LearnPress Single', 'gostudy' ),
						'id' => 'learnpress_single'
					], [
						'name' => esc_html__( 'LearnPress Archive', 'gostudy' ),
						'id' => 'learnpress_archive'
					],
				];
				foreach ($learnpress_sidebars as $key => $sidebar) {
					register_sidebar( [
						'name'          => $sidebar['name'],
						'id'            => $sidebar['id'],
						'description'   => esc_html__( 'Sidebar to be shown on LearnPress Pages.', 'gostudy'),
						'before_widget' => $wrapper_before,
						'after_widget'  => $wrapper_after,
						'before_title'  => $title_before,
						'after_title'   => $title_after,
					]);
				}
			}

            if ( class_exists('SFWD_LMS') ) {
                $learndash_sidebars = [
                    [
                        'name' => esc_html__( 'LearnDash Single', 'gostudy' ),
                        'id' => 'learndash_single'
                    ], [
                        'name' => esc_html__( 'LearnDash Archive', 'gostudy' ),
                        'id' => 'learndash_archive'
                    ],
                ];
                foreach ($learndash_sidebars as $key => $sidebar) {
                    register_sidebar( [
                        'name'          => $sidebar['name'],
                        'id'            => $sidebar['id'],
                        'description'   => esc_html__( 'Sidebar to be shown on LearnDash Pages.', 'gostudy'),
                        'before_widget' => $wrapper_before,
                        'after_widget'  => $wrapper_after,
                        'before_title'  => $title_before,
                        'after_title'   => $title_after,
                    ]);
                }
            }

            if ( function_exists('tutor') ) {
                $tutor_sidebars = [
                    [
                        'name' => esc_html__( 'Tutor Single', 'gostudy' ),
                        'id' => 'tutor_single'
                    ], [
                        'name' => esc_html__( 'Tutor Archive', 'gostudy' ),
                        'id' => 'tutor_archive'
                    ],
                ];
                foreach ($tutor_sidebars as $key => $sidebar) {
                    register_sidebar( [
                        'name'          => $sidebar['name'],
                        'id'            => $sidebar['id'],
                        'description'   => esc_html__( 'Sidebar to be shown on Tutor Pages.', 'gostudy'),
                        'before_widget' => $wrapper_before,
                        'after_widget'  => $wrapper_after,
                        'before_title'  => $title_before,
                        'after_title'   => $title_after,
                    ]);
                }
            }

            register_sidebar( [
                'name' => esc_html__('Side Panel', 'gostudy'),
                'id' => 'side_panel',
                'before_widget' => $wrapper_before,
                'after_widget' => $wrapper_after,
                'before_title' => $title_before,
                'after_title' => $title_after,
            ] );
        }
    }

    new Gostudy_Theme_Support();
}
