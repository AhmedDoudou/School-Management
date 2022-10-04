<?php
/**
 * Template for displaying single course
 *
 *
 */

get_header();

the_post();

?>
<div <?php ?>>
    <div class="rt-container">
        <div class="row">
            <div class="rt_col-12">
                <?php the_content(); ?>
            </div> 
        </div>
    </div>
</div>

<?php
get_footer();
