<?php

    /**
     * Add body class for some pages
     */

    add_filter( 'body_class','gostudy_body_classes' );
    function gostudy_body_classes( $classes ) {
     
        if ( function_exists('tutor') && is_singular( 'courses' ) ) {
         
            $classes[] = 'gostudy-tutor-page';
        }
        
        if ( function_exists('tutor') && is_page( 'student-registration' ) ) {
            $classes[] = 'tutor-student-page';
        }

        if ( function_exists('tutor') && is_page( 'instructor-registration' )) {
           $classes[] = 'tutor-instructor-page';
        }

        return $classes;

    }  


    /**
     * //Add single course sidebar
     */

    function gostudy_tutor_course_single_widget()
    { 

     dynamic_sidebar( 'tutor_single' ); 

    }
    add_action('tutor_course/single/after/sidebar', 'gostudy_tutor_course_single_widget'); 
    add_action('tutor_course/single/enrolled/after/sidebar', 'gostudy_tutor_course_single_widget'); 
    add_action('tutor_course/single/instructor/after/sidebar', 'gostudy_tutor_course_single_widget'); 

    /**
     * //Course header background images
    */

    function gostudy_tutor_header() { 
        global $post, $authordata;
        $profile_url = tutor_utils()->profile_url($authordata->ID);

    $mb_featured_image_replace = rwmb_meta( 'mb_featured_image_replace', array( 'size' => 'full' ) );

    foreach ( $mb_featured_image_replace as $image ) {

        $featured_thumb_replace_url = $image['url'];
    }

    $image = rwmb_meta('mb_page_title_bg');
    $src = $image['image'] ?? '';

    $header_thumb_url = $src;
    $feature_thumb_url = get_the_post_thumbnail_url(); 

    $tutor_single__page_title_bg_image = Gostudy_Theme_Helper::get_mb_option('tutor_single__page_title_bg_image', 'background-image', true)['background-image'];

    $page_title_bg_image = Gostudy_Theme_Helper::get_mb_option('page_title_bg_image', 'background-image', true)['background-image'];


    if (!empty($featured_thumb_replace_url)) {
        $thumbnail_url = $featured_thumb_replace_url;
    }
    elseif (!empty($header_thumb_url)) {
        $thumbnail_url = $header_thumb_url;
    } 
     elseif (!empty($tutor_single__page_title_bg_image)) {
         $thumbnail_url = $tutor_single__page_title_bg_image; 
     }
    else {
       $thumbnail_url = $page_title_bg_image; 
    }


     ?>
    <div class="gostudy-tutor-header" style="
        background: url(<?php echo esc_attr($thumbnail_url); ?> );background-repeat: no-repeat;
        background-position: bottom;
        background-size: cover;">
       <div class="rt-container rt-tutor-container">
          <div class="tutor-header-content">
              <?php
              $disable = get_tutor_option('disable_course_review');
              if ( ! $disable){
                ?>
                    <div class="tutor-leadinfo-top-meta">
                    <span class="tutor-single-course-rating">
                        <?php
                        $course_rating = tutor_utils()->get_course_rating();
                        tutor_utils()->star_rating_generator($course_rating->rating_avg);
                        ?>
                        <span class="tutor-single-rating-count">
                            <?php
                            echo esc_attr( $course_rating->rating_avg );
                            echo '<i>('.$course_rating->rating_count.')</i>';
                            ?>
                        </span>
                    </span>
                    </div>
              <?php } ?>

                <h1 class="tutor-course-header-h1"><?php the_title(); ?></h1>

                <div class="header-tutor-course-summery">
                  <?php the_excerpt(); ?>                        
                </div>
                <?php 
                $disable_course_author = get_tutor_option('disable_course_author');
                $disable_update_date = get_tutor_option('disable_course_update_date');
                 ?>
                <?php if ( !$disable_course_author || !$disable_update_date){ ?>
                    <div class="tutor-single-course-author-meta">

                       <?php if ( !$disable_course_author){ 

                        $instructors = tutor_utils()->get_instructors_by_course();

                         foreach ( $instructors as $key => $instructor ) : 
                        ?>
                          <div class="tutor-single-course-avatar">
                            <a href="<?php echo tutor_utils()->profile_url( $instructor->ID, true ); ?>"> <?php echo get_avatar( get_the_author_meta( 'ID' ), 60 ); ?> </a>
                          </div>
                          <div class="tutor-single-course-author-name">
                              <span><?php esc_html_e('Instructor', 'gostudy'); ?></span>
                              <a href="<?php echo tutor_utils()->profile_url( $instructor->ID, true ); ?>"><?php echo get_the_author(); ?></a>
                          </div>
                          <?php endforeach; ?>
                        <?php } ?>

                       <?php if ( !$disable_update_date){ ?>
                          <div class="tutor-last-course-update">
                            <span><?php esc_html_e('Updated', 'gostudy'); ?></span>
                                <?php echo esc_html(get_the_modified_date()); ?>             
                          </div>
                        <?php } ?>

                    </div>
                <?php } ?>

          </div>
       </div>
    </div>

    <?php
    }

    add_action('tutor_course/single/before/wrap', 'gostudy_tutor_header'); //Single course page
    add_action('tutor_course/single/enrolled/before/wrap', 'gostudy_tutor_header'); //Single course enrolled page
    add_action('tutor_course/single/instructor/before/wrap', 'gostudy_tutor_header'); //Single course enrolled page


    /**
     * // Single page sidebar meta
     */

    function gostudy_tutor_course_meata()
    { 

    $tutor_single_hide_language = Gostudy_Theme_Helper::get_mb_option('tutor_single_hide_language');
    $tutor_single_hide_cat = Gostudy_Theme_Helper::get_mb_option('tutor_single_hide_cat');

    global $post, $authordata;
    $profile_url = tutor_utils()->profile_url($authordata->ID);

        $disable_course_duration = get_tutor_option('disable_course_duration');
        $disable_total_enrolled = get_tutor_option('disable_course_total_enrolled');
        $disable_update_date = get_tutor_option('disable_course_update_date');
        $disable_course_level = get_tutor_option('disable_course_level');
        $disable_course_author = get_tutor_option('disable_course_author');
        $course_duration = get_tutor_course_duration_context();


    $topics = tutor_utils()->get_topics();
    $course_id = get_the_ID();
    $is_enrolled = tutor_utils()->is_enrolled($course_id);

        ?>
    <div class="tutor-single-course-meta tutor-meta-top">

         <?php if ( !$disable_course_author){ ?>
            <?php 
            $instructors = tutor_utils()->get_instructors_by_course();
                foreach ( $instructors as $key => $instructor ) : 
             ?>
           <div class="tutor-course-level">
                <span class="meta-label">
                    <i class="meta-icon flaticon-user-1"></i>
                    <?php esc_html_e( 'Instructor', 'gostudy' ); ?> 
                </span>
              <div class="meta-value">  <a href="<?php echo tutor_utils()->profile_url( $instructor->ID, true ); ?>"> <?php echo get_the_author(); ?></a></div>
           </div>
           <?php endforeach; ?>
        <?php } ?>

        <?php if ( !$disable_course_level){ ?>
           <div class="tutor-course-level">
              <span class="meta-label">
               <i class="meta-icon flaticon-bar-chart-1"></i>
                <?php esc_html_e( 'Level', 'gostudy' ); ?>          
            </span>
              <div class="meta-value"> <?php echo get_tutor_course_level(); ?></div>
           </div>
        <?php } ?>
        <?php if( !$disable_total_enrolled){ ?> 
           <div class="tutor-course-lesson-count">
                <span class="meta-label">
                    <i class="meta-icon flaticon-shopping-cart-1"></i>
                    <?php esc_html_e( 'Enrolled', 'gostudy' ); ?>
                </span>
              <div class="meta-value">
                <?php echo (int) tutor_utils()->count_enrolled_users_by_course(); ?>
              </div>
           </div>
        <?php } ?>
        <?php if( !empty($course_duration) && !$disable_course_duration){ ?>
           <div class="tutor-course-duration">
            <span class="meta-label">
                <i class="meta-icon flaticon-wall-clock"></i>
                <?php esc_html_e( 'Duration', 'gostudy' ); ?>
            </span>
              <?php echo wp_kses_post( $course_duration ); ?>    
           </div>
        <?php } ?>
           <div class="tutor-course-lesson-count">
                <span class="meta-label">
                    <i class="meta-icon flaticon-google-docs"></i>
                    <?php esc_html_e( 'Lectures', 'gostudy' ); ?> 
                </span>
              <div class="meta-value">
                <?php
                $tutor_lesson_count = tutor_utils()->get_lesson_count_by_course($course_id);
                if($tutor_lesson_count) {
                    echo "<span> $tutor_lesson_count";
                    esc_html_e(' Lessons', 'gostudy');
                    echo "</span>";
                }
                ?>     
              </div>
           </div>

    <?php if ( !$tutor_single_hide_cat){ ?>
      
       <div class="tutor-course-categories">

            <span class="meta-label">
                <i class="meta-icon flaticon-price-tag"></i>
                <?php esc_html_e( 'Subject', 'gostudy' ); ?>        
            </span>

          <div class="meta-value">
                    <?php
                $course_categories = get_tutor_course_categories();
                if(is_array($course_categories) && count($course_categories)){
                    ?>

                        <?php
                        foreach ($course_categories as $course_category){
                            $category_name = $course_category->name;
                            $category_link = get_term_link($course_category->term_id);
                            echo "<a href='$category_link'>$category_name</a>";
                        }
                        ?>

                <?php } ?>     
          </div>
       </div>
    <?php } ?>  

   <?php if ( !$tutor_single_hide_language){ ?>
       <div class="tutor-course-language">
            <span class="meta-label">
                <i class="meta-icon flaticon-translate"></i>
                <?php esc_html_e( 'Language', 'gostudy' ); ?>
            </span>
          <div class="meta-value">
           <?php esc_html_e( 'English', 'gostudy' ); ?>    
          </div>
       </div>
    <?php } ?>  

    </div>

    <?php
    }

    add_action('tutor_course/single/before/material_includes', 'gostudy_tutor_course_meata', 1);
    /**
     * // Social share for sidebar
     */

    function gostudy_social_shear_for_sidebar(){ 

        $disable_course_share = get_tutor_option('disable_course_share');

         if ( !$disable_course_share){ ?>
            <?php if (class_exists('Gostudy_Theme_Helper') ): ?> 
                <div class="course-share-container">
                    <span class="share_social-title">
                        <?php echo esc_html__('Share this course:','gostudy') ?>  
                    </span>
                    <?php rt_theme_helper()->render_post_share(); ?>
                </div>
            <?php endif ?>
        <?php } ?>

    <?php
    }


    add_action('tutor_course/single/before/material_includes', 'gostudy_social_shear_for_sidebar', 1);



