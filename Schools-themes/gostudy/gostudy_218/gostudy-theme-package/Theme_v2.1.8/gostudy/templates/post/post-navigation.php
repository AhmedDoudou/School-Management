<?php
/**
 * Navigation section template.
 *
 *
 * @package gostudy\templates
 * @author RaisTheme <help.raistheme@gmail.com>
 * @since 1.0.0
 */

$prevPost = get_adjacent_post(false, '', true);
$nextPost  = get_adjacent_post(false, '', false);

// KSES Allowed HTML
$allowed_html = [
    'a' => [
        'href' => true, 'title' => true,
        'class' => true, 'style' => true,
        'rel' => true, 'target' => true
    ],
    'br' => ['class' => true, 'style' => true],
    'b' => ['class' => true, 'style' => true],
    'em' => ['class' => true, 'style' => true],
    'strong' => ['class' => true, 'style' => true],
];

if ($nextPost || $prevPost) :

    echo '<section class="gostudy-post-navigation">';

    if (is_a($prevPost, 'WP_Post')) :
        $image_prev_url = wp_get_attachment_image_url(get_post_thumbnail_id($prevPost->ID), 'thumbnail');

        $class_image_prev = $image_prev_url ? ' image_exist' : ' no_image';
        $img_prev_html = "<span class='image_prev" . esc_attr($class_image_prev)."'>";
            if ($image_prev_url) {
                $img_prev_html .= "<img src='". esc_url($image_prev_url) ."' alt='". esc_attr($prevPost->post_title) ."'/>";
            } else {
                $img_prev_html .= '<span class="no_image_post"></span>';
            }
        $img_prev_html .= '</span>';

        echo '<div class="prev-link_wrapper">',
            '<div class="info_wrapper">',
            '<a href="', esc_url(get_permalink($prevPost->ID)), '" title="', esc_attr($prevPost->post_title), '">',
                $img_prev_html,
                '<div class="prev-link-info_wrapper">',
                '<h4 class="prev_title">',
                    wp_kses($prevPost->post_title, $allowed_html),
                '</h4>',
                '<span class="meta-data">',
                    esc_html(get_the_time(get_option('date_format'), $prevPost->ID)),
                '</span>',
                '</div>', // prev-link-info_wrapper
            '</a>',
            '</div>', // info_wrapper
        '</div>';
    endif;

    if (is_a($nextPost, 'WP_Post') ) :
        $image_next_url = wp_get_attachment_image_url(get_post_thumbnail_id($nextPost->ID), 'thumbnail');
        $class_image_next = $image_next_url ? ' image_exist' : ' no_image';

        $img_next_html = '<span class="image_next' . esc_attr($class_image_next) . '">';
            if ($image_next_url) {
                $img_next_html .= "<img src='" . esc_url($image_next_url) . "' alt='". esc_attr($nextPost->post_title) ."'/>";
            } else {
                $img_next_html .= '<span class="no_image_post"></span>';
            }
        $img_next_html .= '</span>';
        echo '<div class="next-link_wrapper">',
            '<div class="info_wrapper">',
            '<a href="', esc_url(get_permalink($nextPost->ID)), '" title="', esc_attr($nextPost->post_title), '">',
                '<div class="next-link-info_wrapper">',
                '<h4 class="next_title">',
                    wp_kses($nextPost->post_title, $allowed_html),
                '</h4>',
                '<span class="meta-data">',
                    esc_html(get_the_time(get_option('date_format'), $nextPost->ID)),
                '</span>',
                '</div>', // next-link-info_wrapper
                $img_next_html,
            '</a>',
            '</div>', // info_wrapper
        '</div>';
    endif;

    if (is_a($prevPost, 'WP_Post')
        || is_a($nextPost, 'WP_Post')
    ) {
        echo '<a class="back-nav_page"',
            ' title="', esc_attr__('Back to previous page', 'gostudy'), '"',
            ' onclick="location.href = document.referrer; return false;"',
            '>',
            '<span></span><span></span>',
            '<span></span><span></span>',
        '</a>';
    }

    echo '</section>'; // gostudy-post-navigation

endif;