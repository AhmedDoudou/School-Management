<?php

defined('ABSPATH') || exit;

use Gostudy_Theme_Helper as Gostudy;

/**
 * @package gostudy
 * @author RaisTheme <help.raistheme@gmail.com>
 * @since 1.0.0
 */

$learndash_archive_layout = Gostudy_Theme_Helper::get_mb_option('learndash_archive_layout');

// Render
get_header();

echo '<div class="learndash-content-area">';
echo '<div class="rt-courses layout-'.$learndash_archive_layout.'">';

        while ( have_posts() ) :
            the_post();

       // LearnDash Archive Template
        get_template_part( 'templates/learndash/learndash_course', 'item' ); 

        endwhile;

        echo Gostudy::pagination();

echo '</div>';
echo '</div>';

get_footer();
