<?php

use Gostudy_Theme_Helper as Gostudy;
use RTAddons\Includes\RT_Elementor_Helper;

global $rt_blog_atts;

// Default settings for blog item
$trim = true;
if (!$rt_blog_atts) {
    global $wp_query;

    $trim = false;

    $rt_blog_atts = [
        'query' => $wp_query,
        // General
        'blog_layout' => 'grid',
        // Content
        'blog_columns' => Gostudy::get_option('blog_list_columns') ?: '12',
        'hide_media' => Gostudy::get_option('blog_list_hide_media'),
        'hide_content' => Gostudy::get_option('blog_list_hide_content'),
        'hide_blog_title' => Gostudy::get_option('blog_list_hide_title'),
        'hide_all_meta' => Gostudy::get_option('blog_list_meta'),
        'meta_author' => Gostudy::get_option('blog_list_meta_author'),
        'meta_comments' => Gostudy::get_option('blog_list_meta_comments'),
        'meta_categories' => Gostudy::get_option('blog_list_meta_categories'),
        'meta_date' => Gostudy::get_option('blog_list_meta_date'),
        'hide_likes' => !Gostudy::get_option('blog_list_likes'),
        'hide_share' => !Gostudy::get_option('blog_list_share'),
        'hide_views' => !Gostudy::get_option('blog_list_views'),
        'read_more_hide' => Gostudy::get_option('blog_list_read_more'),
        'content_letter_count' => Gostudy::get_option('blog_list_letter_count') ?: '85',
        'heading_tag' => 'h3',
        'read_more_text' => esc_html__('READ MORE', 'gostudy'),
        'items_load' => 4,
    ];
}

// Retrieve arrived|default variables
extract($rt_blog_atts);

global $rt_query_vars;
if (!empty($rt_query_vars)) {
    $query = $rt_query_vars;
}

$kses_allowed_html = [
    'a' => [
        'href' => true, 'title' => true,
        'class' => true, 'style' => true,
        'rel' => true, 'target' => true,
    ],
    'br' => ['class' => true, 'style' => true],
    'b' => ['class' => true, 'style' => true],
    'em' => ['class' => true, 'style' => true],
    'strong' => ['class' => true, 'style' => true],
    'span' => ['class' => true, 'style' => true],
];

// Variables validation
$img_size = $img_size ?? 'full';
$img_aspect_ratio = $img_aspect_ratio ?? '';
$hide_share = $hide_share && function_exists('rt_theme_helper');
$media_link = $media_link ?? false;
$hide_views = $hide_views ?? false;

// Meta
$meta_date = $meta_cats = [];
if (!$hide_all_meta) {
	$meta_date['date'] = !$meta_date;
    $meta_cats['category'] = !$meta_categories;
	$meta_cats['author'] = !$meta_author;
	$meta_cats['comments'] = !$meta_comments;
    $use_likes = !$hide_likes;
    $use_views = !$hide_views;
    $use_shares = !$hide_share;
}

// Loop through query
while ($query->have_posts()) :
    $query->the_post();

    $post_img_size = class_exists('RTAddons\Includes\RT_Elementor_Helper')
        ? RT_Elementor_Helper::get_image_dimensions($img_size, $img_aspect_ratio)
        : 'full';

    $single = Gostudy_Single_Post::getInstance();
    $single->set_post_data();
    $single->set_image_data($media_link = true, $post_img_size);

    $has_media = $single->meta_info_render;

    $blog_post_classes = ' format-' . $single->get_pf();
    $blog_post_classes .= $hide_media ? ' hide_media' : '';
    $blog_post_classes .= is_sticky() ? ' sticky-post' : '';
    $blog_post_classes .= !$has_media ? ' format-no_featured' : '';

    // Render
    echo '<div class="rt_col-', esc_attr($blog_columns), ' item">';
    echo '<div class="blog-post', esc_attr($blog_post_classes), '">';
    echo '<div class="blog-post_wrapper">';

	// Date
	if (!$hide_all_meta) {
		$single->render_post_meta($meta_date);
	}

    // Media
    if (!$hide_media && $has_media) {
        $single->render_featured([
            'media_link' => $media_link,
            'image_size' => $post_img_size,
            'hide_all_meta' => true,
            'meta_cats' => $meta_cats
        ]);
    } ?>
    <div <?php if (class_exists('WooCommerce')) { wc_product_class("blog-post_content"); } else { echo 'class="'.esc_attr('blog-post_content').'"'; } ?>><?php

    // Media alt (link, quote, audio...)
    if (!$hide_media && !$has_media) {
        $single->render_featured();
    }

    if (!$hide_all_meta) {?>
        <div class="post_meta-wrap"><?php

        // Cats
        $single->render_post_meta($meta_cats);

        // Likes, Views
        if ($use_views || $use_likes) { ?>
            <div class="meta-data"><?php

	        // Socials
            if ( !$hide_all_meta || !$hide_share ) {?>
                <div class="share_post-container">
                    <i class="flaticon flaticon-share"></i><?php
                    rt_theme_helper()->render_post_share(); ?>
                </div><?php
            }

            // Views
            echo ( (bool)$use_views ? $single->get_post_views(get_the_ID(), true) : '' );

            // Likes
            if ($use_likes) {
                rt_simple_likes()->likes_button(get_the_ID(), 0);
            } ?>
            </div><?php
        }?>
        </div><?php
	}

    // Title
    if (
        !$hide_blog_title
        && !empty($title = get_the_title())
    ) {
        printf(
            '<%1$s class="blog-post_title"><a href="%2$s">%3$s</a></%1$s>',
            esc_html($heading_tag),
            esc_url(get_permalink()),
            wp_kses($title, $kses_allowed_html)
        );
    }

    // Excerpt|Content
    if (!$hide_content) {
        $single->render_excerpt($content_letter_count, $trim);
    }

    // Read more
    if (!$read_more_hide && !empty($read_more_text)) { ?>
        <div class="read-more-wrap">
            <a href="<?php echo esc_url(get_permalink()); ?>" class="button-read-more">
            <span><?php
            echo esc_html($read_more_text); ?>
            </span>
            </a>
        </div><?php
    }

    // Pagination
    wp_link_pages(Gostudy::pagination_wrapper()); ?>
    </div>
    </div>
    </div>
    </div><?php

endwhile;
wp_reset_postdata();
