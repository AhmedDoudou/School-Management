<?php

defined('ABSPATH') || exit;

use Gostudy_Theme_Helper as Gostudy;

/**
 * The template for displaying image attachments
 *
 * @package gostudy
 * @author RaisTheme <help.raistheme@gmail.com>
 * @since 1.0.0
 */

get_header();

$sb = Gostudy::get_sidebar_data();
$row_class = $sb['row_class'] ?? '';
$container_class = $sb['container_class'] ?? '';
$column = $sb['column'] ?? '';


echo '<div class="rt-container', apply_filters('gostudy/container/class', esc_attr( $container_class )), '">';
echo '<div class="row', apply_filters('gostudy/row/class', esc_attr( $row_class )), '">';
    echo '<div id="main-content" class="rt_col-', apply_filters('gostudy/column/class', esc_attr( $column )), '">';
        while (have_posts()) :
            the_post();

            /**
            * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
            * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
            */
            $attachments = array_values(get_children([
                'post_parent' => $post->post_parent,
                'post_status' => 'inherit',
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'order' => 'ASC',
                'orderby' => 'menu_order ID',
            ]));

            foreach ($attachments as $k => $attachment) {
                if ($attachment->ID == $post->ID) {
                    break;
                }
            }
            $k++;

            // If there is more than 1 attachment in a gallery
            if (count($attachments) > 1) {
                if (isset($attachments[$k])) {
                    // get the URL of the next image attachment
                    $next_attachment_url = get_attachment_link($attachments[ $k ]->ID);
                } else {
                    // or get the URL of the first image attachment
                    $next_attachment_url = get_attachment_link($attachments[0]->ID);
                }
            } else {
                // or, if there's only 1 image, get the URL of the image
                $next_attachment_url = wp_get_attachment_url();
            }

            echo '<div class="blog-post">';
            echo '<div class="single_meta attachment_media">';
            echo '<div class="blog-post_content">';
                echo '<h4 class="blog-post_title">', esc_html(get_the_title()), '</h4>';

                echo '<div class="meta-data">';
                    Gostudy::posted_meta_on();
                echo '</div>';

                echo '<div class="blog-post_media">',
                    '<a href="', esc_url($next_attachment_url), '" title="', the_title_attribute(), '" rel="attachment">',
                        wp_get_attachment_image(get_the_ID(), [1170, 725]),
                    '</a>',
                '</div>';

                the_content();

                wp_link_pages(Gostudy::pagination_wrapper());

            echo '</div>';
            echo '</div>';
            echo '</div>'; // blog-post

            if (comments_open() || '0' != get_comments_number()) {
                comments_template();
            }
        endwhile;

    echo '</div>'; // #main-content

    if ($sb) {
        Gostudy::render_sidebar($sb);
    }

echo '</div>';
echo '</div>';

get_footer();
