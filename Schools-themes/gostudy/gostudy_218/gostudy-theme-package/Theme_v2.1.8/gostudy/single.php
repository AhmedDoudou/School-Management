<?php

defined('ABSPATH') || exit;

use RTAddons\Templates\RT_Blog;
use Gostudy_Theme_Helper as Gostudy;

/**
 * The dedault template for single posts rendering
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package gostudy
 * @author RaisTheme <help.raistheme@gmail.com>
 * @since 1.0.0
 */

get_header();
the_post();

$sb = Gostudy::get_sidebar_data('single');
$column = $sb['column'] ?? '';
$row_class = $sb['row_class'] ?? '';
$container_class = $sb['container_class'] ?? '';
$layout = $sb['layout'] ?? '';

$single_type = Gostudy::get_mb_option('single_type_layout', 'mb_post_layout_conditional', 'custom') ?: 2;

$row_class .= ' single_type-' . $single_type;

if ('3' === $single_type) {
    echo '<div class="post_featured_bg" style="background-color: ', Gostudy::get_option('post_single_layout_3_bg_image')['background-color'], '">';
        get_template_part('templates/post/single/post', $single_type . '_image');
    echo '</div>';
}

//* Render
echo '<div class="rt-container', apply_filters('gostudy/container/class', esc_attr( $container_class )), '">';
echo '<div class="row', apply_filters('gostudy/row/class', esc_attr( $row_class )), '">';
    echo '<div id="main-content" class="rt_col-', apply_filters('gostudy/column/class', esc_attr( $column )), '">';

        get_template_part('templates/post/single/post', $single_type);

        //* Navigation
        get_template_part('templates/post/post-navigation');

        //* ↓ Related Posts
        $show_post_related = Gostudy::get_option('single_related_posts');

        if (
            class_exists('RWMB_Loader')
            && !empty($mb_blog_show_r = rwmb_meta('mb_blog_show_r'))
            && $mb_blog_show_r != 'default'
        ) {
            $show_post_related = $mb_blog_show_r === 'off' ? null : $mb_blog_show_r;
        }

        if (
            $show_post_related
            && class_exists('Gostudy_Core')
            && class_exists('\Elementor\Plugin')
        ) {
            global $rt_related_posts;
            $rt_related_posts = true;

            $mb_blog_carousel_r = Gostudy::get_mb_option('blog_carousel_r', 'mb_blog_show_r', 'custom');
            $mb_blog_title_r = Gostudy::get_mb_option('blog_title_r', 'mb_blog_show_r', 'custom');

            $mb_blog_cat_r = [];

            $cats = Gostudy::get_option('blog_cat_r');
            if (!empty($cats)) {
                $mb_blog_cat_r[] = implode( (array)',', $cats);
            }

            if (
                class_exists('RWMB_Loader')
                && get_queried_object_id() !== 0
                && $mb_blog_show_r == 'custom'
            ) {
                $mb_blog_cat_r = get_post_meta(get_the_id(), 'mb_blog_cat_r');
            }

            $mb_blog_column_r = Gostudy::get_mb_option('blog_column_r', 'mb_blog_show_r', 'custom');
            $mb_blog_number_r = Gostudy::get_mb_option('blog_number_r', 'mb_blog_show_r', 'custom');

            //* Render ?>
            <section class="single related_posts"><?php
                //* Get Cats_Slug
                if ($categories = get_the_category()) {
                    $post_categ = $post_category_compile = '';
                    foreach ($categories as $category) {
                        $post_categ = $post_categ . $category->slug . ',';
                    }
                    $post_category_compile .= '' . trim($post_categ, ',') . '';

                    if (!empty($mb_blog_cat_r[0])) {
                        $categories = get_categories(['include' => $mb_blog_cat_r[0]]);
                        $post_categ = $post_category_compile = '';
                        foreach ($categories as $category) {
                            $post_categ = $post_categ . $category->slug . ',';
                        }
                        $post_category_compile .= trim($post_categ, ',');
                    }

                    $mb_blog_cat_r = $post_category_compile;
                } ?>
                <div class="gostudy_module_title">
	                <h4><?php
                        echo !empty($mb_blog_title_r) ? esc_html($mb_blog_title_r) : esc_html__('Related Posts', 'gostudy'); ?>
                    </h4>
                </div><?php

                $related_posts_atts = [
                    'blog_layout' => !empty($mb_blog_carousel_r) ? 'carousel' : 'grid',
                    'navigation_type' => 'none',
                    'use_navigation' => null,
                    'hide_content' => true,
                    'hide_share' => true,
                    'hide_likes' => true,
                    'hide_views' => true,
                    'meta_author' => false,
                    'meta_comments' => '',
                    'read_more_hide' => false,
                    'read_more_text' => esc_html__('READ MORE', 'gostudy'),
                    'heading_tag' => 'h4',
                    'content_letter_count' => 90,
                    'img_size_string' => '840x600',
                    'img_size_array' => '',
                    'img_aspect_ratio' => '',
                    'items_load' => 4,
                    'load_more_text' => esc_html__('Load More', 'gostudy'),
                    'blog_columns' => $mb_blog_column_r ?? (($layout == 'none') ? '4' : '6'),
                    //* Carousel
                    'autoplay' => '',
                    'autoplay_speed' => 3000,
                    'slides_to_scroll' => '',
                    'infinite_loop' => false,
                    'use_pagination' => '',
                    'pag_type' => 'circle',
                    'pag_offset' => '',
                    'custom_resp' => true,
                    'resp_medium' => '',
                    'pag_color' => '',
                    'custom_pag_color' => '',
                    'resp_tablets_slides' => '',
                    'resp_tablets' => '',
                    'resp_medium_slides' => '',
                    'resp_mobile' => '767',
                    'resp_mobile_slides' => '1',
                    //* Query
                    'number_of_posts' => (int) $mb_blog_number_r,
                    'categories' => $mb_blog_cat_r,
                    'order_by' => 'rand',
                    'exclude_any' => 'yes',
                    'by_posts' => [$post->post_name => $post->post_title] //* exclude current post
                ];

                (new RT_Blog())->render($related_posts_atts);

            echo '</section>';

            unset($rt_related_posts); //* destroy globar var
        }
        //* ↑ related posts

        //* Comments
        if (comments_open() || get_comments_number()) { ?>
            <div class="row">
            <div class="rt_col-12"><?php
                comments_template(); ?>
            </div>
            </div><?php
        }

    echo '</div>'; //* #main-content

    $sb && Gostudy::render_sidebar($sb);

echo '</div>';
echo '</div>';

get_footer();
