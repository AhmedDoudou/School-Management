<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Gostudy_Animation' ) ) {
    class Gostudy_Animation {

        protected static $instance = null;

        public static function instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function initialize() {

        /**
         * Edit Controls.
         */
        // Add custom Motion Effect - Entrance Animation.
        add_filter( 'elementor/controls/animations/additional_animations', [
            $this,
            'add_custom_entrance_animations',
        ] );

        }

        public function add_custom_entrance_animations( $animations ) {
            $animations['By Gostudy'] = [
                'gostudyFadeInUp' => 'Gostudy - Fade In Up',
            ];

            return $animations;
        }


    }

    Gostudy_Animation::instance()->initialize();
}

