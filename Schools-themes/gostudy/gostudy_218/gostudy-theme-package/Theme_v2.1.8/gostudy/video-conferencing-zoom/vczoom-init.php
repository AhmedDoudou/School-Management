<?php
defined('ABSPATH') || exit;

if (!class_exists('Gostudy_Vczoom')) {
    /**
     * Video Conferencing with Zoom plugin configuration for Gostudy theme

     * @package gostudy\video-conferencing-zoom
     * @author RaisTheme <help.raistheme@gmail.com>
     * @since 1.0.0
     */
    class Gostudy_Vczoom
    {
        public function __construct()
        {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';

            $this->vczoom_plugin_configuration();
        }

        public function vczoom_plugin_configuration ()
        {
            $this->single_page_configuration();
        }

        protected function single_page_configuration()
        {
            remove_action( 'vczoom_before_main_content', 'video_conference_zoom_output_content_start', 10 );
            remove_action( 'vczoom_after_main_content', 'video_conference_zoom_output_content_end', 10 );

            // Single
            add_action('vczoom_before_content', [$this, 'single_before_content']);
            add_action('vczoom_after_content', [$this, 'single_after_content']);

        }

        public function single_before_content()
        {
            echo '<div class="rt-container">',
                '<div class="row">';
        }

        public function single_after_content()
        {
            echo '</div>',
                '</div>';
        }

    }

    new Gostudy_Vczoom();

}