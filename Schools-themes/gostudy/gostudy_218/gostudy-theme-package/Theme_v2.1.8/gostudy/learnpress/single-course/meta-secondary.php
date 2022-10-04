<?php
/**
 * Template for displaying secondary course meta data such as: duration, level, lessons, quizzes, students, etc...
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) or die; 
$course = LP_Global::course();
?>


<div class="header-learnpress-course-summery">
  <?php the_excerpt(); ?>

	<div class="learnpress-single-course-author-meta">

	<div class="learnpress-single-course-avatar">
		<?php echo wp_kses( $course->get_instructor()->get_profile_picture(), 'gostudy-default' ); ?>
	</div>
	<div class="learnpress-single-course-author-name">
	<span><?php esc_html_e( 'Instructor', 'gostudy' ); ?></span>
		<?php echo wp_kses( $course->get_instructor_html(), 'gostudy-default'); ?>
	</div>

	<div class="learnpress-last-course-update">
	<span><?php esc_html_e( 'Updated', 'gostudy' ); ?></span>
		<?php echo esc_html(get_the_modified_date()); ?>   
	</div>

	</div>

</div>