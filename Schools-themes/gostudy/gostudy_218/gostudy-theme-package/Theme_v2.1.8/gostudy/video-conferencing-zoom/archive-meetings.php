<?php
/**
 * The template for displaying archive of meetings
 *
 * This template can be overridden by copying it to yourtheme/video-conferencing-zoom/archive-meetings.php.
 *
 * @author Deepen
 * @since 3.0.0
 * @updated 3.6.0
 */

defined( 'ABSPATH' ) || exit;

use Gostudy_Theme_Helper as Gostudy;

get_header();

/**
 * Before the LOOP archive page
 */
do_action( 'vczapi_before_main_archive_content' );
?>
	<div class="rt-container">
	<div class="row">
    <div id="vczapi-primary" class="vczapi-primary container">

	<?php if (1>1) { ?>

        <header class="page-header">
            <h1 class="page-title vczapi-archive-page-title"><?php echo esc_html__( 'Archives: Zoom Meetings', 'gostudy' ); ?></h1>
        </header><!-- .page-header -->
	<?php } ?>
		<?php
		/**
		 * BEFORE LOOP HOOK
		 */
		do_action( 'vczapi_before_main_content_post_loop' );

		if ( have_posts() ) {
			?>
            <div class="vczapi-list-zoom-meetings">
                <div class="vczapi-list-zoom-meetings--items">
					<?php
					// Start the Loop.
					while ( have_posts() ) {
						the_post();

						do_action( 'vczapi_main_content_post_loop' );

						vczapi_get_template_part( 'content', 'meeting' );
					}
					?>
                </div>
            </div>
            <div class="gostudy__vczapi-pagination">
				<?php echo Gostudy::pagination(); ?>
            </div>
			<?php
		} else {
			echo "<p class='vczapi-no-meeting-found'>" . esc_html__( 'No Meetings found.', 'gostudy' ) . "</p>";
		}

		/**
		 * AFTER LOOP HOOK
		 */
		do_action( 'vczapi_after_main_content_post_loop' );
		?>
    </div>
    </div>
    </div>
<?php
/**
 * After loop call
 */
do_action( 'vczapi_after_main_archive_content' );

get_footer();
