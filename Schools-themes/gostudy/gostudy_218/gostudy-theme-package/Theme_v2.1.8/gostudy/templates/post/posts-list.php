<?php

$opt_blog_columns = Gostudy_Theme_Helper::get_option('blog_list_columns') ?: '12';

$rt_blog_atts = [
    // General
    'blog_layout' => 'grid',
    'blog_columns' => $opt_blog_columns,
    'hide_likes' => true,
    'hide_share' => true,
    'navigation_type' => 'pagination',
];

extract($rt_blog_atts);

// Row classes
if (in_array($blog_layout, ['grid', 'masonry'])) {
    switch ($blog_columns) {
        default:
        case '12':
            $row_class = ' blog_columns-1';
            break;
        case '6':
            $row_class = ' blog_columns-2';
            break;
        case '4':
            $row_class = ' blog_columns-3';
            break;
        case '3':
            $row_class = ' blog_columns-4';
            break;
    }
}
$row_class .= ' blog-style-standard';

// Render
if (have_posts()) :
    echo '<div class="blog-posts blog-posts-list">';
    echo '<div class="container-grid row ', esc_attr($row_class), '">';

    get_template_part('templates/post/post-standard');

    echo '</div>';
    echo '</div>'; // blog-posts
endif;
