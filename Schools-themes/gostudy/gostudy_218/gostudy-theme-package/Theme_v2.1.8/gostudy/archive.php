<?php

defined('ABSPATH') || exit;

use Gostudy_Theme_Helper as Gostudy;

/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package gostudy
 * @author RaisTheme <help.raistheme@gmail.com>
 * @since 1.0.0
 */

// Taxonomies
$tax_obj = get_queried_object();
$term_id = $tax_obj->term_id ?? '';
$tax_description = false;
 if ($term_id) {
    $taxonomies[] = $tax_obj->taxonomy . ': ' . $tax_obj->slug;
    $tax_description = $tax_obj->description;
}

// Sidebar parameters
$sb = Gostudy::get_sidebar_data('blog_list');
$container_class = $sb['container_class'] ?? '';
$row_class = $sb['row_class'] ?? '';
$column = $sb['column'] ?? '';

// Render
get_header();

echo '<div class="rt-container', apply_filters('gostudy/container/class', esc_attr( $container_class )), '">';
echo '<div class="row', apply_filters('gostudy/row/class', $row_class), '">';
    echo '<div id="main-content" class="rt_col-', apply_filters('gostudy/column/class', esc_attr( $column )), '">';

        if ($term_id) { ?>
            <div class="archive__heading">
                <h4 class="archive__tax_title"><?php
                    echo get_the_archive_title(); ?>
                </h4>
                <?php
                if (!empty($tax_description)) {
                    echo '<div class="archive__tax_description">' . esc_html($tax_description) . '</div>';
                }
                ?>
            </div><?php
        }

        // Blog Archive Template
        get_template_part('templates/post/posts-list');

        echo Gostudy::pagination();

    echo '</div>';

    $sb && Gostudy::render_sidebar($sb);

echo '</div>';
echo '</div>';

get_footer();
