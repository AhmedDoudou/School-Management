<?php
global $rt_blog_atts;

extract($rt_blog_atts);
$image_size['width'] = 420;
$image_size['heoght'] = 300;

global $rt_query_vars;
if(!empty($rt_query_vars)){
    $query = $rt_query_vars;
}

while ($query->have_posts()) : $query->the_post();

    echo '<div class="span item">';

    $single = Gostudy_Single_Post::getInstance();
    $single->set_post_data();

    $title = get_the_title();

    $blog_item_classes = ' format-'.$single->get_pf();
    ?>

    <div class="blog-post <?php echo esc_attr($blog_item_classes); ?>">
        <div class="blog-post_wrapper">
            <?php

            // Media
            $single->render_featured([
                'media_link' => true,
                'image_size' => $image_size
            ]);
            ?>
            <div class="blog-post_content">
            <?php
                // Blog Title
                echo '<h6 class="blog-post_title"><a href="'.esc_url(get_permalink()).'">'.esc_html($title).'</a></h6>';

                // Blog Meta
                $meta_to_show = [
                    'date' => true,
                    'author' => false,
                    'comments' => false,
                    'category' => false
                ];
                $single->render_post_meta($meta_to_show);
                ?>
            </div>
        </div>
    </div>
    <?php

    echo '</div>';

endwhile;
wp_reset_postdata();
