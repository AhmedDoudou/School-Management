<?php
/**
 * Template for displaying latest courses in user profile dashboard.
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

$user = LP_Profile::instance()->get_user();

if ( empty( $courses ) ) {
	return;
}

use Gostudy_Theme_Helper as Gostudy;

$learnpress_archive_layout = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_layout');

?>

<div class="profile-courses newest-courses">
	<h3><?php esc_html_e( 'Latest courses', 'gostudy' ); ?></h3>

	<?php if ( ! empty( $courses ) ) : ?>
		<div class="lp-archive-courses">
	
			<div class="rt-courses layout-<?php echo esc_attr($learnpress_archive_layout); ?>">
				<ul <?php lp_item_course_class(); ?> data-size="3" data-layout="grid" id="learn-press-profile-latest-courses">
					<?php
					global $post;

					foreach ( $courses as $item ) {
						$course = learn_press_get_course( $item );
						$post   = get_post( $item );
						setup_postdata( $post );
						learn_press_get_template( 'content-course.php' );
					}

					wp_reset_postdata();
					?>
				</ul>
			</div>
		
		</div>

		<?php if ( isset( $max_num_pages ) && $max_num_pages > 1 ) : ?>
			<button data-container="learn-press-profile-latest-courses"
					data-pages="<?php echo esc_attr($max_num_pages); ?>"
					data-url="<?php echo esc_url( '?lp-ajax=load-more-courses&type=latest&user=' . $user->get_id() ); ?>"
					class="lp-button btn-load-more-courses btn-ajax-off">
				<i class="fas fa-spinner icon"></i>
				<?php esc_html_e( 'View More', 'gostudy' ); ?></button>
		<?php endif; ?>
	<?php else : ?>
		<?php learn_press_display_message( __( 'There is no courses.', 'gostudy' ) ); ?>
	<?php endif; ?>
</div>
