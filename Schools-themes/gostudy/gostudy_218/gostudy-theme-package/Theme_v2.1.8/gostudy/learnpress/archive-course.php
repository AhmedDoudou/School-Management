<?php
/**
 * Template for displaying content of archive courses page.
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

use Gostudy_Theme_Helper as Gostudy;

$learnpress_archive_layout = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_layout');

/**
 * @since 4.0.0
 *
 * @see LP_Template_General::template_header()
 */
do_action( 'learn-press/template-header' );

/**
 * LP Hook
 */
do_action( 'learn-press/before-main-content' );

$page_title = learn_press_page_title( false );
?>

<div class="lp-content-area">

    <section class="rt-courses layout-<?php echo esc_attr($learnpress_archive_layout); ?>">
    <?php
    /**
     * LP Hook
     */
    do_action( 'learn-press/before-courses-loop' );

    LP()->template( 'course' )->begin_courses_loop();

    $loop = new WP_Query( array( 
        'post_type' => 'lp_course', 
        'paged' => $paged 
    ) );
    if ( $loop->have_posts() ) :
            while ( $loop->have_posts() ) : $loop->the_post(); 

            learn_press_get_template_part( 'content', 'course' );

        endwhile;
    endif;


    LP()->template( 'course' )->end_courses_loop();

    /**
     * @since 3.0.0
     */
    //do_action( 'learn-press/after-courses-loop' ); 

    // Pagination
    echo Gostudy::pagination();
    wp_reset_postdata();
    ?>
        
    </section>

    <?php
    /**
     * LP Hook
     */
    do_action( 'learn-press/after-main-content' );

    /**
     * LP Hook
     *
     * @since 4.0.0
     */
    do_action( 'learn-press/sidebar' );
    ?>
</div>

<?php
/**
 * @since 4.0.0
 *
 * @see   LP_Template_General::template_footer()
 */
do_action( 'learn-press/template-footer' );
