<?php


$postID = get_the_ID();
$single = Gostudy_Single_Post::getInstance();
$single->set_post_data();
$post_format = $single->get_pf();
$single->set_image_data();
$single->set_post_views($postID);

$hide_featured = Gostudy_Theme_Helper::get_mb_option('post_hide_featured_image', 'mb_post_hide_featured_image', true);

$use_author_info = Gostudy_Theme_Helper::get_option('single_author_info');
$use_tags = Gostudy_Theme_Helper::get_option('single_meta_tags') && has_tag();
$use_shares = Gostudy_Theme_Helper::get_option('single_share') && function_exists('rt_theme_helper');
$hide_all_meta = Gostudy_Theme_Helper::get_option('single_meta');
$use_likes = Gostudy_Theme_Helper::get_option('single_likes') && function_exists('rt_simple_likes');
$use_views = Gostudy_Theme_Helper::get_option('single_views');

$video_style = 'video' === $post_format && function_exists('rwmb_meta') ? rwmb_meta('post_format_video_style') : '';
$has_media = $single->meta_info_render;

$meta_date = $meta_data = [];
if (!$hide_all_meta) {
    $meta_date['date'] = !Gostudy_Theme_Helper::get_option('single_meta_date');
    $meta_data['category'] = !Gostudy_Theme_Helper::get_option('single_meta_categories');
    $meta_data['author'] = !Gostudy_Theme_Helper::get_option('single_meta_author');
    $meta_data['comments'] = !Gostudy_Theme_Helper::get_option('single_meta_comments');
}

// Render ?>

<article class="blog-post blog-post-single-item format-<?php echo esc_attr( $single->get_pf() ); ?>">
    <div <?php post_class( 'single_meta' ); ?>>
        <div class="item_wrapper">
            <div class="blog-post_content">

                <?php
                // Media
                if ($post_format == 'video') {
                   $single->render_featured();
                } else { ?>
                        <div class="blog-post_media">
                            <div class="blog-post_media_part">
                            <?php 
                                if ( has_post_thumbnail() ) {
                                    the_post_thumbnail();
                                } 
                            ?>
                            </div>
                        </div>
              <?php  }
                

                // Date
                if ( !$hide_all_meta ) $single->render_post_meta_2($meta_date);

                // Meta-wrap ?>
                <div class="post_meta-wrap"><?php

                    // Cats, Author, Comments
                    if ( !$hide_all_meta ) $single->render_post_meta_2($meta_data);

                    // Likes, Views
                    if ( $use_views || $use_likes ) { ?>
                        <div class="meta-data"><?php
                        // Views
                        echo ( (bool)$use_views ? $single->get_post_views($postID) : '' );
                        // Likes
                        if ($use_likes) {
                            rt_simple_likes()->likes_button($postID, 0);
                        } ?>
                        </div><?php
                    } ?>

                </div><?php // meta-wrap

                // Content
                the_content();

                // Pagination
                wp_link_pages(Gostudy_Theme_Helper::pagination_wrapper());

                if ( $use_tags || $use_shares ) { ?>
                    <div class="single_post_info"><?php

                    // Tags
                    if ($use_tags) {
                        the_tags('<div class="tagcloud-wrapper"><div class="tagcloud">', ' ', '</div></div>');

                    // Socials
                    if ($use_shares) {
                        rt_theme_helper()->render_post_share();
                    }
                    }?>

                    </div><?php
                }

                // Author Info
                if ($use_author_info) {
                    $single->render_author_info();
                }?>
                <div class="post_info-divider"></div>
                <div class="clear"></div>
            </div><!--blog-post_content-->
        </div><!--item_wrapper-->
    </div>
</article>
