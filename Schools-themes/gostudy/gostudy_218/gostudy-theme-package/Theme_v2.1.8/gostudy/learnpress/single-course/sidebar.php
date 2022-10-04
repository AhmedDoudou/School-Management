<?php
/**
 * Template for displaying course sidebar.
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hide sidebar if there is no content
 */
if ( ! is_active_sidebar( 'learnpress_single' ) && ! LP()->template( 'course' )->has_sidebar() ) {
	return;
}
?>
<?php  
    $sticky_sidebar = 'yes';
    if ($sticky_sidebar == 'yes') {
        wp_enqueue_script('theia-sticky-sidebar', get_template_directory_uri() . '/js/theia-sticky-sidebar.min.js');
        $sidebar_class = ' sticky-sidebar';
          $sb_data['class'] = $sidebar_class ?? '';
    }
?>
<div class="gostudy-single-sidebar-top sticky-sidebar">
	<div class="theiaStickySidebar course-summary-sidebar__inner">
		<div class="course-sidebar-top">
			<?php
			/**
			 * LP Hook
			 *
			 * @since 4.0.0
			 */
			do_action( 'learn-press/before-course-summary-sidebar' );

			/**
			 * LP Hook
			 *
			 * @since 4.0.0
			 *
			 * @see   LP_Template_Course::course_sidebar_preview() - 10
			 * @see   LP_Template_Course::course_featured_review() - 20
			 */
			do_action( 'learn-press/course-summary-sidebar' );

			/**
			 * LP Hook
			 *
			 * @since 4.0.0
			 */
			do_action( 'learn-press/after-course-summary-sidebar' );

			?>
		</div>

		<?php if ( is_active_sidebar( 'learnpress_single' ) ) : ?>
			<div class="course-sidebar-secondary">
				<?php dynamic_sidebar( 'learnpress_single' ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
