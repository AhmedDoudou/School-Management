<?php


    /**
     * Enable override template
     */
    add_filter( 'learn-press/override-templates', '__return_true' );

    /**
     * Add removed action
     */
    remove_action( 'learn-press/before-courses-loop', LP()->template( 'course' )->func( 'courses_top_bar' ), 10 );
    remove_action( 'learn-press/course-summary-sidebar', LP()->template( 'course' )->func( 'course_featured_review' ), 20 );

    /**
     * Custom Course features
     */
    add_action('learn-press/after-course-summary-sidebar', 'gostudy_learnpress_course_meata', 1);
    function gostudy_learnpress_course_meata(){ 

    $course = LP_Global::course();
    $learnpress_single_hide_instructor = Gostudy_Theme_Helper::get_mb_option('learnpress_single_hide_instructor');
    $learnpress_single_hide_level = Gostudy_Theme_Helper::get_mb_option('learnpress_single_hide_level');
    $learnpress_single_hide_duration = Gostudy_Theme_Helper::get_mb_option('learnpress_single_hide_duration');
    $learnpress_single_hide_enrolled = Gostudy_Theme_Helper::get_mb_option('learnpress_single_hide_enrolled');
    $learnpress_single_hide_lesson = Gostudy_Theme_Helper::get_mb_option('learnpress_single_hide_lesson');
    $learnpress_single_hide_category = Gostudy_Theme_Helper::get_mb_option('learnpress_single_hide_category');

    
    ?>
    <div class="learnpress-single-course-meta learnpress-meta-top">

        <?php if(!$learnpress_single_hide_instructor){ ?>
           <div class="learnpress-course-level">
                <span class="meta-label">
                    <i class="meta-icon flaticon-user-1"></i>
                    <?php esc_html_e( 'Instructor', 'gostudy' ); ?> 
                </span>

              <div class="meta-value">  
                <?php echo get_the_author(); ?>  
            </div>
           </div>
        <?php } ?>

        <?php if( !$learnpress_single_hide_level ){ ?>
           <div class="learnpress-course-level">
              <span class="meta-label">
               <i class="meta-icon flaticon-bar-chart-1"></i>
                <?php esc_html_e( 'Level', 'gostudy' ); ?>          
            </span>
            <?php $level = learn_press_get_post_level( get_the_ID() ); ?>
              <div class="meta-value"> <?php echo esc_html( $level ); ?></div>
           </div>
        <?php } ?>

        <?php if( !$learnpress_single_hide_duration ){ ?>
           <div class="learnpress-course-duration">
            <span class="meta-label">
                <i class="meta-icon flaticon-wall-clock"></i>
                <?php esc_html_e( 'Duration', 'gostudy' ); ?>
            </span>
             <?php echo learn_press_get_post_translated_duration( get_the_ID(), esc_html__( 'Lifetime access', 'gostudy' ) ); ?>
           </div>
        <?php } ?>

        <?php if( !$learnpress_single_hide_enrolled){ ?> 
           <div class="learnpress-course-lesson-count">
                <span class="meta-label">
                    <i class="meta-icon flaticon-shopping-cart-1"></i>
                    <?php esc_html_e( 'Enrolled', 'gostudy' ); ?>
                </span>
              <div class="meta-value">
                    <?php   
                        $students = (int)learn_press_get_course()->count_students();
                        $students_text = ('1' == $students) ? esc_html__(' Student', 'gostudy') : esc_html__(' Students', 'gostudy');

                        echo esc_attr( $students ) . $students_text;
                     ?>
              </div>
           </div>
        <?php } ?>
        <?php if( !$learnpress_single_hide_lesson){ ?> 
           <div class="learnpress-course-lesson-count">
                <span class="meta-label">
                    <i class="meta-icon flaticon-google-docs"></i>
                    <?php esc_html_e( 'Lesson', 'gostudy' ); ?> 
                </span>
              <div class="meta-value">
                <?php
                    $lessons = $course->get_items('lp_lesson', false) ? count($course->get_items('lp_lesson', false)) : 0;
                    $lessons_text = ('1' == $lessons) ? esc_html__(' Lesson', 'gostudy') : esc_html__(' Lessons', 'gostudy');
                    echo esc_attr( $lessons ) . $lessons_text;
                ?>     
              </div>
           </div>
        <?php } ?>

    <?php if( !$learnpress_single_hide_category ): ?>
      
       <div class="learnpress-course-categories">

            <span class="meta-label">
                <i class="meta-icon flaticon-price-tag"></i>
                <?php esc_html_e( 'Category', 'gostudy' ); ?>        
            </span>

          <div class="meta-value">
            <?php
                if ( ! get_the_terms( get_the_ID(), 'course_category' ) ) {
                    esc_html_e( 'Uncategorized', 'gostudy' );
                } else {
                    echo get_the_term_list( get_the_ID(), 'course_category', '');
                }
            ?>  
          </div>
       </div>
    <?php endif ?>

    </div>

    <?php
    }


// Fix learnpress archive page pagination 
add_action( 'pre_get_posts', 'gostudy_cpt_archive_items' );
function gostudy_cpt_archive_items( $query ) {
if( $query->is_main_query() && !is_admin() && is_post_type_archive( 'lp_course' ) ) {
        $query->set( 'posts_per_page', '8' );
    }

}


    