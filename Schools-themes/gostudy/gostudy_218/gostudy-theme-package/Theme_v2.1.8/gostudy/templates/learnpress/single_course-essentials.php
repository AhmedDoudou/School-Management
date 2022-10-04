<?php

defined( 'ABSPATH' ) || exit();

/**
 * Template for displaying course essentials.
 *
 * @author   RaisTheme
 * @package  Gostudy/templates/learnpress
 * @version  1.0
 */


$course = LP_Global::course();
$course_id = get_the_ID();

$title = sprintf( '%s %s', esc_html__( 'Price:', 'gostudy' ), Gostudy_LearnPress::course_price_html() );
$students_title = strip_tags( $course->get_students_html() );

$duration = get_post_meta( $course_id, '_lp_duration', true );

$meta_fields = [
	'language',
	'skill_level',
	'resources',
	'certificate',
	'description'
];
foreach ($meta_fields as $meta)
	${$meta} = get_post_meta( $course_id, 'gostudy_course_'.$meta, true );

?>
<div class="rt-course-essentials">
	<h3 class="title"><?php echo apply_filters( 'gostudy/learn-press/render-course-essentials/title', $title ) ; ?></h3>
	<ul>
		<li class="students" title="<?php echo esc_attr($students_title); ?>">
			<?php $users = $course->get_users_enrolled(); ?>
			<span class="label"><?php esc_html_e( 'STUDENTS', 'gostudy' ); ?></span>
			<span class="value"><?php $users ? printf('%s', $users) : esc_html_e( '0', 'gostudy' ); ?></span>
		</li><?php
		if ( $language ) : ?>
		  <li class="language">
			<span class="label"><?php esc_html_e( 'LANGUAGE', 'gostudy' ); ?></span>
			<span class="value"><?php echo esc_html($language); ?></span>
		  </li><?php
		endif;
		if ( $v = (int)$duration ) :
			preg_match('/\d (\w*)/', $duration, $match);
			if (isset($match[1])) {
				switch ($match[1]) {
					case 'minute': $s = _n( 'MINUTE', 'MINUTES', $v, 'gostudy' ); break;
					case 'hour': $s = _n( 'HOUR', 'HOURS', $v, 'gostudy' ); break;
					case 'day': $s = _n( 'DAY', 'DAYS', $v, 'gostudy' ); break;
					case 'week': $s = _n( 'WEEK', 'WEEKS', $v, 'gostudy' ); break;
				}
				$duration = sprintf( '%d %s', $v, $s);
			}
			?>
			<li class="duration">
				<span class="label"><?php esc_html_e( 'DURATION', 'gostudy' ); ?></span>
				<span class="value"><?php printf('%s', $duration); ?></span>
			</li><?php
		endif;
		if ( $skill_level ) : ?>
		  <li class="skill">
			<span class="label"><?php esc_html_e( 'SKILL LEVEL', 'gostudy' ); ?></span>
			<span class="value"><?php echo esc_html( $skill_level ); ?></span>
		  </li><?php
		endif; ?>
		<li class="lectures">
			<span class="label"><?php esc_html_e( 'LESSONS', 'gostudy' ); ?></span>
			<span class="value"><?php printf('%s', $course->get_curriculum_items( 'lp_lesson' ) ? count( $course->get_curriculum_items( 'lp_lesson' ) ) : 0); ?></span>
		</li><?php
		if ( $quizzes = $course->get_curriculum_items( 'lp_quiz' ) ) : ?>
		  <li class="quizzes">
			<span class="label"><?php esc_html_e( 'QUIZZES', 'gostudy' ); ?></span>
			<span class="value"><?php printf('%s', $quizzes ? count($quizzes) : 0); ?></span>
		  </li><?php
		endif;
		if ( $resources ) : ?>
		  <li class="resources">
			<span class="label"><?php esc_html_e( 'RESOURCES', 'gostudy' ); ?></span>
			<span class="value"><?php echo esc_html($resources); ?></span>
		  </li><?php
		endif;
		if ( $certificate ) : ?>
		  <li class="certificate">
			<span class="label"><?php esc_html_e( 'CERTIFICATE', 'gostudy' ); ?></span>
			<span class="value"><?php echo esc_html($certificate); ?></span>
		  </li><?php
		endif;
		if ( $description ) : ?>
		  <li class="description">
			<span class="value"><?php echo esc_html($description); ?></span>
		  </li><?php
		endif; ?>
	</ul><?php
	do_action( 'gostudy/learn-press/after-course-essentials' ); ?>
</div><?php
