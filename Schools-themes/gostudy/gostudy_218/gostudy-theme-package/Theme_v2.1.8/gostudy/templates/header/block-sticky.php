<?php

defined('ABSPATH') || exit;

if (!class_exists('Gostudy_Header_Sticky')) {
    class Gostudy_Header_Sticky extends Gostudy_Get_Header
    {
        public function __construct()
        {
            $this->header_vars();
            $this->html_render = 'sticky';

            if (Gostudy_Theme_Helper::get_mb_option('header_sticky', 'mb_customize_header_layout', 'custom')) {
                $header_sticky_style = Gostudy_Theme_Helper::get_option('header_sticky_style');

                echo "<div class='rt-sticky-header rt-sticky-element", ($this->header_type === 'default' ? ' header_sticky_shadow' : ''), "'", (!empty($header_sticky_style) ? ' data-style="' . esc_attr($header_sticky_style) . '"' : ''), ">";

                echo '<div class="container-wrapper">';

                    $this->build_header_layout('sticky');

                echo '</div>';

                echo '</div>';
            }
        }
    }

    new Gostudy_Header_Sticky();
}
