<?php

defined('ABSPATH') || exit;

use Gostudy_Theme_Helper as Gostudy;

/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package gostudy
 * @since 1.0.0
 */

get_header();
the_post();

$sb = Gostudy::get_sidebar_data();
$row_class = $sb['row_class'] ?? '';
$column = $sb['column'] ?? '';
$container_class = $sb['container_class'] ?? '';

// Render
echo '<div class="rt-container', apply_filters('gostudy/container/class', esc_attr( $container_class )), '">';
echo '<div class="row ', apply_filters('gostudy/row/class', esc_attr( $row_class )), '">';

    echo '<div id="main-content" class="rt_col-', apply_filters('gostudy/column/class', esc_attr( $column )), '">';

        the_content(esc_html__('Read more!', 'gostudy'));

        // Pagination
        wp_link_pages(Gostudy::pagination_wrapper());

        // Comments
        if (comments_open() || get_comments_number()) {
            comments_template();
        }

    echo '</div>';

    if ($sb) {
        Gostudy::render_sidebar($sb);
    }

echo '</div>';
echo '</div>';

get_footer();
