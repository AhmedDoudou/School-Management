<?php

defined('ABSPATH') || exit;

/**
 * Template for displaying related courses.
 *
 * @author RaisTheme
 * @package gostudy\templates\learnpress
 * @since 1.0.0
 */

use RTAddons\Templates\WglCourses;

if (
    !Gostudy_Theme_Helper::get_option('learnpress_related_posts')
    && !class_exists('Gostudy_Core')
    && !class_exists('\Elementor\Plugin')
) {
    return;
}

$related_columns = Gostudy_Theme_Helper::get_option('learnpress_column_r') ?: 3;
$related_items = Gostudy_Theme_Helper::get_option('learnpress_number_r') ?: 3;

$taxonomies = [];
$count = 0;

if ($tags = get_the_terms($post->ID, 'course_tag')) {
    foreach ($tags as $term) {
        $taxonomies[] = $term->taxonomy . ':' . $term->slug;
        if ($count < $related_items) {
            $count += get_term_by('slug', $term->slug, $term->taxonomy)->count;
        }
    }
}

if (!$count && $cats = get_the_terms($post->ID, 'course_category')) {
    foreach ($cats as $term) {
        $taxonomies[] = $term->taxonomy . ':' . $term->slug;
        if ($count < $related_items) $count += get_term_by('slug', $term->slug, $term->taxonomy)->count;
    }
}

if ($count < 2) {
    return; // abort if no any related courses
}

$atts = [
    // General
    'course_layout' => 'carousel',
    'grid_columns' => $related_columns,
    'img_size_string' => '840x540',
    'img_size_array' => '',
    'img_aspect_ratio' => '',
    'isotope_filter' => false,
    'pagination' => false,
    'single_link_image' => true,
    'single_link_title' => true,
    // Appearance
    'hide_media' => false,
    'hide_tax' => false,
    'hide_price' => false,
    'hide_title' => false,
    'hide_instructor' => false,
    'hide_students' => false,
    'hide_lessons' => false,
    'hide_reviews' => true,
    'hide_wishlist' => true,
    'hide_excerpt' => true,
    'excerpt_chars' => '',
    // Carousel
    'autoplay' => false,
    'autoplay_speed' => false,
    'infinite_loop' => false,
    'slide_single' => false,
    'use_pagination' => false,
    'pag_type' => false,
    'pag_offset' => false,
    'custom_pag_color' => false,
    'pag_color' => false,
    'use_navigation' => false,
    'custom_resp' => true,
    'resp_medium' => '',
    'custom_pag_color' => '',
    'resp_tablets_slides' => '',
    'resp_tablets' => '',
    'resp_medium_slides' => '',
    'resp_mobile' => '600',
    'resp_mobile_slides'=> '1',
    // Query
    'number_of_posts' => $related_items,
    'exclude_any' => 'true',
    'order_by' => 'rand',
    // Extra
    'caller' => 'related',
];

// Render
echo '<div class="related-courses">',
    '<div class="gostudy_module_title">',
        '<h4>',
            esc_html__('Related Courses', 'gostudy'),
        '</h4>',
    '</div>',
    (new WglCourses())->render($atts),
'</div>';
