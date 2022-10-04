<?php
/**
 * Template for displaying single course
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

get_header();
?>

<?php do_action('tutor_course/single/before/wrap'); ?>

<?php  
    $sticky_sidebar = 'yes';
    if ($sticky_sidebar == 'yes') {
        wp_enqueue_script('theia-sticky-sidebar', get_template_directory_uri() . '/js/theia-sticky-sidebar.min.js');
        $sidebar_class = ' sticky-sidebar';
          $sb_data['class'] = $sidebar_class ?? '';
    }
?>
<div <?php tutor_post_class('tutor-full-width-course-top tutor-course-top-info tutor-page-wrap'); ?>>
    <div class="rt-container rt-tutor-container">
        <div class="row">
            <div class="rt_col-8 tutor-col-md-100 gostudy-col-space">
	            <?php do_action('tutor_course/single/before/inner-wrap'); ?>
	            <?php tutor_course_content(); ?>
	            <?php tutor_course_benefits_html(); ?>
	            <?php tutor_course_topics(); ?>
                <?php tutor_course_instructors_html(); ?>
                <?php tutor_course_target_reviews_html(); ?>
	            <?php do_action('tutor_course/single/after/inner-wrap'); ?>
            </div> <!-- .tutor-col-8 -->

            <div class="rt_col-4 <?php echo esc_attr( $sidebar_class ); ?>">
                <div class="tutor-single-course-sidebar">
                    <?php do_action('tutor_course/single/before/sidebar'); ?>
                    <div class="tutor-price-preview-box">
                        <?php tutor_load_template('single.course.course-entry-box'); ?>
                        <?php tutor_course_requirements_html(); ?>
                        <?php tutor_course_tags_html(); ?>
                        <?php tutor_course_target_audience_html(); ?>
                    </div>
                </div>
                <?php do_action('tutor_course/single/after/sidebar'); ?>
            </div>
        </div>
    </div>
</div>

<?php do_action('tutor_course/single/after/wrap'); ?>

<?php
get_footer();
