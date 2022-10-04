<?php
global $rt_blog_atts;

// Default settings for blog item
$trim = true;
if (!$rt_blog_atts) {
    $opt_likes = Gostudy_Theme_Helper::get_option('blog_list_likes');
    $opt_share = Gostudy_Theme_Helper::get_option('blog_list_share');
    $opt_meta_author = Gostudy_Theme_Helper::get_option('blog_list_meta_author');
    $opt_meta_comments = Gostudy_Theme_Helper::get_option('blog_list_meta_comments');
    $opt_meta_categories = Gostudy_Theme_Helper::get_option('blog_list_meta_categories');
    $opt_meta_date = Gostudy_Theme_Helper::get_option('blog_list_meta_date');
    $opt_read_more = Gostudy_Theme_Helper::get_option('blog_list_read_more');

    global $wp_query;
    $rt_blog_atts = [
        'query' => $wp_query,
        // General
        'blog_layout' => 'grid',
        // Content
        'blog_columns' => Gostudy_Theme_Helper::get_option('blog_list_columns') ?: '12',
        'hide_media' => Gostudy_Theme_Helper::get_option('blog_list_hide_media'),
        'hide_content' => Gostudy_Theme_Helper::get_option('blog_list_hide_content'),
        'hide_blog_title' => Gostudy_Theme_Helper::get_option('blog_list_hide_title'),
        'hide_all_meta' => Gostudy_Theme_Helper::get_option('blog_list_meta'),
        'meta_author' => $opt_meta_author,
        'meta_comments' => $opt_meta_comments,
        'meta_categories' => $opt_meta_categories,
        'meta_date' => $opt_meta_date,
        'hide_likes' => !$opt_likes,
        'hide_share' => !$opt_share,
        'hide_views' => true,
        'read_more_hide' => $opt_read_more,
        'content_letter_count' => Gostudy_Theme_Helper::get_option('blog_list_letter_count') ?: '85',
        'crop_square_img' => true,
        'heading_tag' => 'h3',
        'read_more_text' => esc_html__('READ MORE', 'gostudy'),
        'items_load'  => 4,
    ];
    $trim = false;
}

extract($rt_blog_atts);

if ($crop_square_img) {
    $image_size = 'gostudy-740-830';
} else {
    $image_size = 'full';
}

global $rt_query_vars;
if (!empty($rt_query_vars)){
    $query = $rt_query_vars;
}

// Allowed HTML render
$allowed_html = [
    'a' => [
        'href' => true,
        'title' => true,
    ],
    'br' => [],
    'b' => [],
    'em' => [],
    'strong' => []
];

$blog_styles = '';

$blog_attr = !empty($blog_styles) ? ' style="'.esc_attr($blog_styles).'"' : '';

while ($query->have_posts()) : $query->the_post();

    echo '<div class="rt_col-'.esc_attr($blog_columns).' item">';

    $single = Gostudy_Single_Post::getInstance();
    $single->set_post_data();

    $title = get_the_title();

    $blog_item_classes = ' format-'.$single->get_pf();
    $blog_item_classes .= (bool)$hide_media ? ' hide_media' : '';
    $blog_item_classes .= is_sticky() ? ' sticky-post' : '';

    $has_media = $single->render_bg_image;

    if ((bool)$hide_media){
        $has_media = false;
    }

    $blog_item_classes .= !(bool) $has_media ? ' format-no_featured' : '';

    $meta_to_show = array(
        'comments' => !(bool)$meta_comments,
        'author' => !(bool)$meta_author,
        'date' => !(bool)$meta_date,
    );
    $meta_to_show_cats = array(
        'category' => !(bool)$meta_categories,
    );

    ?>
    <div class="blog-post <?php echo esc_attr($blog_item_classes); ?>"<?php echo Gostudy_Theme_Helper::render_html($blog_attr);?>>
        <div class="blog-post-hero_wrapper">

            <?php
            // Media blog post

            $media_link = true;
            ?>

            <div class="blog-post-hero-content_front">

                <div class="blog-post-hero_content">
                <?php

                    //Post Meta render cats
                    if (!$hide_all_meta && !empty($meta_to_show_cats) ) {
                        echo '<div class="blog-post_cats">';
                            $single->render_post_meta($meta_to_show_cats);
                        echo '</div>';
                    }

                    // Blog Title
                    if (!(bool)$hide_blog_title && !empty($title) ) :
                        printf(
                            '<%1$s class="blog-post_title"><a href="%2$s">%3$s</a></%1$s>',
                            esc_html($heading_tag),
                            esc_url(get_permalink()),
                            wp_kses($title, $allowed_html)
                        );
                    endif;
                    //Post Meta render comments,author
                    if (!$hide_all_meta ) {
                        $single->render_post_meta($meta_to_show);
                    }
                    ?>

                    <div class='blog-post-hero_meta-desc'>
                        <?php
                            if (!(bool)$hide_share || !(bool)$hide_likes) {
                                echo '<div class="meta-data">';
                            }

                            echo "<div class='divider_post_info'></div>";

                            // Likes in blog
                            if (!(bool)$hide_share || !(bool)$hide_likes) echo '<div class="blog-post_info-wrap">';

                            // Render shares
                            if (!(bool)$hide_share && function_exists('rt_theme_helper') ) :
                                echo rt_theme_helper()->render_post_list_share();
                            endif;

                            if (!(bool)$hide_likes && function_exists('rt_simple_likes')) {
                                rt_simple_likes()->likes_button( get_the_ID(), 0 );
                            }

                            if (!(bool)$hide_share || !(bool)$hide_likes ): ?>
                                </div>
                                </div>
                            <?php
                            endif;

                        ?>
                    </div>

                    <?php
                    wp_link_pages(Gostudy_Theme_Helper::pagination_wrapper());
                    ?>
                </div>
            </div>

            <div class="blog-post-hero-content_back">
                <div class="blog-post-hero_content">
                    <?php

                    // Post Meta render cats
                    if (!$hide_all_meta && !empty($meta_to_show_cats) ) {
                        echo '<div class="blog-post_cats">';
                            $single->render_post_meta($meta_to_show_cats);
                        echo '</div>';
                    }

                    $heading_tag_back = 'p';
                    // Blog Title
                    if (!(bool)$hide_blog_title && !empty($title)) :
                        printf(
                            '<%1$s class="blog-post_title"><a href="%2$s">%3$s</a></%1$s>',
                            esc_html($heading_tag_back),
                            esc_url(get_permalink()),
                            wp_kses($title, $allowed_html)
                        );
                    endif;

                    // Post Meta render comments,author
                    if (!$hide_all_meta ) {
                        $single->render_post_meta($meta_to_show);
                    }
                    // Content Blog
                    if (!(bool)$hide_content ) $single->render_excerpt($content_letter_count, $trim, !(bool)$read_more_hide, $read_more_text);

                    ?>
                    <div class='blog-post-hero_meta-desc'>
                        <?php
                            if (!(bool)$hide_share || !(bool)$hide_likes) {
                                echo '<div class="meta-data">';
                            }

                            echo "<div class='divider_post_info'></div>";
                            // Read more link
                            if (!(bool)$read_more_hide ) :
                                ?>
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="button-read-more standard_post"><?php echo esc_html($read_more_text); ?></a>
                                <?php
                            endif;


                            // Likes in blog
                            if (!(bool)$hide_share || !(bool)$hide_likes) echo '<div class="blog-post_info-wrap">';

                            // Render shares
                            if (!(bool)$hide_share && function_exists('rt_theme_helper') ) :
                                echo rt_theme_helper()->render_post_list_share();
                            endif;

                            if (!(bool)$hide_likes && function_exists('rt_simple_likes')) {
                                rt_simple_likes()->likes_button( get_the_ID(), 0 );
                            }

                            if (!(bool)$hide_share || !(bool)$hide_likes): ?>
                                </div>
                                </div>
                            <?php
                            endif;

                        ?>
                    </div>

                    <?php
                    wp_link_pages(Gostudy_Theme_Helper::pagination_wrapper());
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php

    echo '</div>';

endwhile;
wp_reset_postdata();
