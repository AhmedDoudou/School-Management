<?php
/**
 * Template for displaying single course
 *
 *
 */

get_header();

the_post();

use RTAddons\Templates\RT_Blog;
use Gostudy_Theme_Helper as Gostudy;

   // For ribbon_text 
    global $post; $post_id = $post->ID;
    $course_id = $post_id;
    $user_id   = get_current_user_id();
    $current_id = $post->ID;

    $options = get_option('sfwd_cpt_options');
    $course_options = get_post_meta($post_id, "_sfwd-courses", true);

  // For legacy_short_description
    $legacy_short_description = '';
    if ( $post_type == 'sfwd-courses' ) {
        $legacy_short_description = isset( $course_options['sfwd-courses_course_short_description'] ) ? $course_options['sfwd-courses_course_short_description'] : '';
    }

    if ( ! empty( $cg_short_description ) ) {
        $short_description = $cg_short_description;
    } elseif ( ! empty( $legacy_short_description ) ) {
        $short_description = $legacy_short_description;
    } else {
        $short_description = '';
    }
 ?>

<?php  
    $sticky_sidebar = 'yes';
    if ($sticky_sidebar == 'yes') {
        wp_enqueue_script('theia-sticky-sidebar', get_template_directory_uri() . '/js/theia-sticky-sidebar.min.js');
        $sidebar_class = ' sticky-sidebar';
          $sb_data['class'] = $sidebar_class ?? '';
    }



$sb = Gostudy::get_sidebar_data('single');
$column = $sb['column'] ?? '';
$row_class = $sb['row_class'] ?? '';
$container_class = $sb['container_class'] ?? '';
$layout = $sb['layout'] ?? '';

$single_type = Gostudy::get_mb_option('single_type_layout', 'mb_post_layout_conditional', 'custom') ?: 2;

$row_class .= ' single_type-' . $single_type;

//if ('3' === $single_type) {
    // echo '<div class="post_featured_bg" style="background-color: ', Gostudy::get_option('learndash_single__page_title_bg_image')['background-color'], '">';
    //     get_template_part('templates/post/single/post', $single_type . '_image');
    // echo '</div>';
//}
    $mb_featured_image_replace = rwmb_meta( 'mb_featured_image_replace', array( 'size' => 'full' ) );

    foreach ( $mb_featured_image_replace as $image ) {

        $featured_thumb_replace_url = $image['url'];
    }

    $image = rwmb_meta('mb_page_title_bg');
    $src = $image['image'] ?? '';

    $header_thumb_url = $src;
    $feature_thumb_url = get_the_post_thumbnail_url(); 

    $learndash_single__page_title_bg_image = Gostudy_Theme_Helper::get_mb_option('learndash_single__page_title_bg_image', 'background-image', true);

    $page_title_bg_image = Gostudy_Theme_Helper::get_mb_option('page_title_bg_image', 'background-image', true);


    if (!empty($featured_thumb_replace_url)) {
        $thumbnail_url = $featured_thumb_replace_url;
    }
    elseif (!empty($header_thumb_url)) {
        $thumbnail_url = $header_thumb_url;
    } 
     elseif (!empty($learndash_single__page_title_bg_image['background-image'])) {
         $thumbnail_url = $learndash_single__page_title_bg_image['background-image']; 
     }
    else {
       $thumbnail_url = $page_title_bg_image['background-image']; 
    }

?>
<?php if(class_exists('SFWD_LMS') && is_singular( 'sfwd-courses' )) {   ?>
<div class="gostudy-learndash-header" style="
        background: url(<?php echo esc_url($thumbnail_url); ?> ); background-repeat: no-repeat;
        background-position: bottom;
        background-size: cover;">
    <div class="rt-container">
        <div class="learndash-header-content">

            <h1 class="learndash-course-header-h1"><?php the_title(); ?></h1>
            <div class="header-learndash-course-summery">
                <p><?php echo esc_html( $short_description ); ?></p>
            </div>
            <div class="learndash-single-course-author-meta">

                <div class="learndash-single-course-avatar">
                   <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"> <?php echo get_avatar( get_the_author_meta( 'ID' ), 42 ); ?></a>
                </div>
                <div class="learndash-single-course-author-name"> <span><?php esc_html_e( 'Created by', 'gostudy' ); ?></span>
                    <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php the_author() ?></a>
                </div>
                
                <div class="learndash-last-course-update"> 
                    <span><?php esc_html_e( 'Updated', 'gostudy' ); ?></span>
                   <?php echo esc_html(get_the_modified_date()); ?> 
               </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

    <div class="rt-container">
        <div class="row">
            <div class="rt_col-8 learndash-col-md-100 gostudy-col-space">
                <?php the_content(); ?>
            </div> <!-- .learndash-col-8 -->

            <div class="rt_col-4 <?php echo esc_attr( $sidebar_class ); ?>">
                <div class="learndash-single-course-sidebar">
                    
                  <?php gostudy_learndash_course_meata(); ?>

                <?php if ( is_active_sidebar( 'learndash_single' ) ) : ?>
                    <div class="course-sidebar-secondary">
                        <?php dynamic_sidebar( 'learndash_single' ); ?>
                    </div>
                <?php endif; ?>

                </div>
            </div>
        </div>
    </div>


<?php
get_footer();
