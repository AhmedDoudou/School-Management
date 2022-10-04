<?php

use Gostudy_Theme_Helper as Gostudy;

$single = Gostudy_Single_Post::getInstance();
$single->set_post_data();
$single->set_image_data();
$single->set_post_views(get_the_ID());

$hide_all_meta = Gostudy_Theme_Helper::get_option('single_meta');

$has_media = $single->meta_info_render;

$meta_date = $meta_data = [];
if (!$hide_all_meta) {
	$meta_date['date'] = !Gostudy_Theme_Helper::get_option('single_meta_date');
	$meta_data['category'] = !Gostudy_Theme_Helper::get_option('single_meta_categories');
    $meta_data['author'] = !Gostudy_Theme_Helper::get_option('single_meta_author');
    $meta_data['comments'] = !Gostudy_Theme_Helper::get_option('single_meta_comments');
}

$use_likes = Gostudy_Theme_Helper::get_option('single_likes') && function_exists('rt_simple_likes');
$use_views = Gostudy_Theme_Helper::get_option('single_views');

$page_title_padding = Gostudy_Theme_Helper::get_mb_option('single_padding_layout_3', 'mb_post_layout_conditional', 'custom');
$page_title_padding_top = !empty($page_title_padding['padding-top']) ? (int)$page_title_padding['padding-top'] : '';
$page_title_padding_bottom = !empty($page_title_padding['padding-bottom']) ? (int)$page_title_padding['padding-bottom'] : '';
$page_title_styles = !empty($page_title_padding_top) ? 'padding-top: '.esc_attr((int) $page_title_padding_top).'px;' : '';
$page_title_styles .= !empty($page_title_padding_bottom) ? 'padding-bottom: '.esc_attr((int) $page_title_padding_bottom).'px;' : '';
$page_title_styles = $page_title_styles ? ' style="' . esc_attr($page_title_styles) . '"' : '';
$page_title_top = $page_title_padding_top ?: 200;

$apply_animation = Gostudy_Theme_Helper::get_mb_option('single_apply_animation', 'mb_post_layout_conditional', 'custom');
$data_attr_image = $data_attr_content = $post_class = '';

if ($apply_animation) {
    wp_enqueue_script('skrollr', get_template_directory_uri() . '/js/skrollr.min.js', [], false, false);

    $data_attr_image = ' data-center="background-position: 50% 0px;" data-top-bottom="background-position: 50% -100px;" data-anchor-target=".blog-post-single-item"';
	$data_attr_content = ' data-center="opacity: 1" data-'.esc_attr($page_title_top).'-top="opacity: 1" data-'.esc_attr($page_title_top)*0.4 .'-top="opacity: 1" data--100-top="opacity: 0" data-anchor-target=".blog-post-single-item .blog-post_content"';
	
	$post_class = ' blog_skrollr_init';
}

// Render
echo '<div class="blog-post', $post_class, ' blog-post-single-item format-', esc_attr($single->get_pf()), '"', $page_title_styles, '>'; ?>
<div <?php post_class('single_meta'); ?>>
	<div class="item_wrapper">
		<div class="blog-post_content"><?php

		    $single->render_featured_image_as_background_2( false, 'full', $data_attr_image); ?>

		    <div class="rt-container">
			    <div class="row">
				    <div class="content-container rt_col-12"<?php echo Gostudy::render_html($data_attr_content); ?>><?php

				        // Date
				        if (!$hide_all_meta) $single->render_post_meta($meta_date);

				        // Title ?>
				        <h1 class="blog-post_title"><?php echo get_the_title(); ?></h1><?php

				         // Breadcrumb ?>
				        <?php gostudy_breadcrumb_trail(); ?>

				    </div>
			    </div>
		    </div>
		</div>
	</div>
</div>
</div><?php
