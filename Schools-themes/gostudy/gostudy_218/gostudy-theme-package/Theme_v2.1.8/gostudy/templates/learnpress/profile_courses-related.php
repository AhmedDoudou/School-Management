<?php

defined('ABSPATH') || exit;

use RTAddons\Templates\WglCourses;

/**
 * Template for displaying author courses.
 *
 * @package gostudy\templates\learnpress
 * @author RaisTheme
 * @since 1.0.0
 */

global $this_profile; // fetch global var

$user = $this_profile->get_user();
$role = $user->get_role();
$id = $user->get_id();
$q_id[0] = $id;

if (
    $user->is_guest()
    || $this_profile->is_current_user()
    || !in_array($role, ['admin', 'instructor'])
    || (count_user_posts($user->get_id(), 'lp_course') < 1)
) {
    return;
}

$name = $user->get_display_name();

$atts = [
    // General
    'course_layout' => 'grid',
    'grid_columns' => '3',
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
    // Query
    'author' => $q_id,
    'order' => 'DSC',
];

$search_all = $id ? '/?s=&ref=course&order=DSC&author=' . $id : '';

// Render
echo '<div class="related-courses lp-user-profile">',
    '<div class="gostudy_module_title">',
        '<h4>',
            '<a href="', esc_url($search_all), '">',
                $name
                    ? esc_html__('Courses by ', 'gostudy') . esc_html($name)
                    : esc_html__('Related Courses', 'gostudy'),
            '</a>',
        '</h4>',
    '</div>',
    (new WglCourses())->render($atts),
'</div>';
