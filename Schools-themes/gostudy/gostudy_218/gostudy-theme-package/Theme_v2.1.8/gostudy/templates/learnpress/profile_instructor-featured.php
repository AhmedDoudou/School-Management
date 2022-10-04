<?php

defined( 'ABSPATH' ) || exit();

/**
 * Template for displaying instructor's featured info.
 *
 * @author   RaisTheme
 * @package  Gostudy/templates/learnpress
 * @version  1.0
 */

global $this_profile; // fetch global var

$user = $this_profile->get_user();
$role = $user->get_role();
$user_id = $user->get_id();

// Abort if no any user at profle page.
if ( empty( get_the_author_meta('login', $user_id) ) ) return;

if ( in_array($role, ['admin', 'instructor', 'lp_teacher']) ) :
	$is_instructor = true;
else :
	$is_instructor = false;
endif;

$reviews_unactive = ! class_exists('LP_Addon_Course_Review_Preload');

$wrapper_class = $this_profile->main_class(false, 'gostudy__instructor');
$ava_width = learn_press_get_avatar_thumb_size()['width'];
$user_img = $this_profile->get_profile_picture('', $ava_width); // is_avatar_enabled()
$user_name = get_the_author_meta( 'display_name', $user_id );
$user_spec = get_the_author_meta( 'occupation', $user_id );
$user_bio = get_the_author_meta( 'description', $user_id );

$count_total_students = $count_total_courses = $my_total_reviews = 0;
$user_socials = '';

if ($is_instructor) :
	// get dashboard values
	$user_curd        = new LP_User_CURD();
	$query_list_table = $user_curd->query_own_courses( $user_id, array(
		'paged'  => 1,
		'limit'  => 9999,
		'status' => 'publish'
	) );
	$own_courses = $query_list_table->get_items();

	foreach ($own_courses as $course) {
		$count_total_students += learn_press_get_course($course)->count_students();
		$count_total_courses++;
		if ( $reviews_unactive ) continue;
		$my_total_reviews += learn_press_get_course_rate_total($course);
	}
	$socials = [ 'twitter', 'facebook-f', 'linkedin-in', 'instagram', 'telegram-plane' ];
	foreach ($socials as $soc_name) $user_social[ $soc_name ] = get_the_author_meta( $soc_name, $user_id );
	foreach ($user_social as $soc_name => $soc_link) if ($soc_link) {
		$user_socials .= sprintf( '<a href="%s" class="social-link fab fa-%s"></a>', $soc_link, $soc_name );
	}
endif;

Gostudy_LearnPress::logged_in_message();

printf('<div %s>', $wrapper_class);
	printf( '<div class="instructor__overall" %s>', $user_img ? 'style="width: '.esc_attr($ava_width).'px"' : '' );
		! $user_img || printf( '<div class="instructor__avatar">%s</div>', $user_img );
		?>
	</div>
	<div class="instructor__description"><?php
		! $user_name || printf( '<h2 class="instructor__name">%s</h2>', $user_name);
		! $user_spec || printf( '<div class="instructor__spec">%s</div>', $user_spec);

		if ( $count_total_students || $count_total_courses || $my_total_reviews ) : ?>
			<div class="instructor__dashboard"><?php
			if ( $count_total_students ) : ?>
			<span class="dashboard__students">
				<span class="data__value"><?php echo (int)$count_total_students; ?></span>
				<span class="data__title"><?php esc_html_e( 'STUDENTS', 'gostudy' ); ?></span>
			</span><?php
			endif;
			if ( $count_total_courses ) : ?>
			<span class="dashboard__courses">
				<span class="data__value"><?php echo (int)$count_total_courses; ?></span>
				<span class="data__title"><?php esc_html_e( 'COURCES', 'gostudy' ); ?></span>
			</span><?php
			endif;
			if ( $my_total_reviews ) : ?>
			<span class="dashboard__reviews">
				<span class="data__value"><?php echo number_format_i18n($my_total_reviews); ?></span>
				<span class="data__title"><?php esc_html_e( 'REVIEWS', 'gostudy' ); ?></span>
			</span><?php
			endif; ?>
		</div><?php
		endif;

		! $user_bio || printf( '<p class="instructor__bio">%s</p>', $user_bio);
		! $user_socials || printf( '<div class="instructor__socials">%s</div>', $user_socials);

		?>
	</div>
</div><?php
