    <?php

        use Gostudy_Theme_Helper as Gostudy;

        $tutor_archive_layout = Gostudy_Theme_Helper::get_mb_option('tutor_archive_layout');
        $tutor_see_more_text = Gostudy_Theme_Helper::get_mb_option('tutor_see_more_text');
        $tutor_archive_hide_media = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_media');
        $tutor_archive_hide_categories = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_categories');
        $tutor_archive_hide_categories_popup = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_categories_popup');
        $tutor_archive_hide_price = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_price');
        $tutor_archive_hide_price_popup = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_price_popup');
        $tutor_archive_hide_title = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_title');
        $tutor_archive_hide_title_popup = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_title_popup');
        $tutor_archive_hide_created_by = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_created_by');
        $tutor_archive_hide_created_by_popup = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_created_by_popup');
        $tutor_archive_hide_lessons = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_lessons');
        $tutor_archive_hide_lessons_popup = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_lessons_popup');
        $tutor_archive_hide_lessons_text = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_lessons_text');
        $tutor_archive_hide_lessons_text_popup = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_lessons_text_popup');
        $tutor_archive_hide_duration = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_duration');
        $tutor_archive_hide_duration_popup = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_duration_popup');
        // $tutor_archive_hide_topic_text = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_topic_text');
        // $tutor_archive_hide_topic_text_popup = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_topic_text_popup');
        $tutor_archive_hide_preview_btn = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_preview_btn');
        $tutor_archive_hide_enroll_btn = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_enroll_btn');
        $tutor_enroll_now_text = Gostudy_Theme_Helper::get_mb_option('tutor_enroll_now_text');
        $tutor_archive_enroll_btn_switch = Gostudy_Theme_Helper::get_mb_option('tutor_archive_enroll_btn_switch');
        $tutor_archive_hide_excerpt_content = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_excerpt_content');
        $tutor_archive_hide_excerpt_content_popup = Gostudy_Theme_Helper::get_mb_option('tutor_archive_hide_excerpt_content_popup');
        $tutor_free_text = Gostudy_Theme_Helper::get_mb_option('tutor_free_text');
        $tutor_enrolled_text = Gostudy_Theme_Helper::get_mb_option('tutor_enrolled_text');
        $tutor_completed_text = Gostudy_Theme_Helper::get_mb_option('tutor_completed_text');

        $tutor_tf_hover_popup_type = Gostudy_Theme_Helper::get_mb_option('tutor_tf_hover_popup_type');
    ?>

    <?php if ($tutor_archive_layout == '1'): ?>


            <?php  if ($tutor_tf_hover_popup_type == 'show_content_popup') : ?>
                <div class="course__popup-wrap">

                    <?php if (!$tutor_archive_hide_created_by_popup): ?>
                        <div class="rt-course-author-name">
                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
                            <?php echo get_the_author(); ?>
                        </div>
                    <?php endif; ?>

                      <div class="courses-content">

                        <div class="course__top-meta">
                             <?php if (!$tutor_archive_hide_categories_popup): ?>
                                <div class="course__categories ">
                                    <?php echo get_the_term_list(get_the_ID(), 'course-category'); ?>
                                </div>
                             <?php endif; ?>
                             <?php if (!$tutor_archive_hide_price_popup): ?>
                             <div class="price">
                                  <?php get_template_part('templates/tutor/price_within_button_2'); ?>
                             </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!$tutor_archive_hide_title_popup): ?>
                        <h4 class="course__title">
                            <a href="<?php the_permalink(); ?>" class="course__title-link">
                                <?php the_title(); ?>
                            </a>
                        </h4>
                        <?php endif; ?>
                     </div>
                    <div class="course__content--meta"> 

                        <?php if (!$tutor_archive_hide_lessons_popup): ?>

                            <span class="course-lessons">
                                <i class="flaticon-book-1"></i>
                                <?php
                                    $tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                                    if($tutor_lesson_count) {
                                        echo "<span> $tutor_lesson_count";
                                        esc_html_e(' Lessons', 'gostudy');
                                        echo "</span>";
                                    }
                                ?>  
                            </span>

                        <?php endif; ?>

                        <?php if (!$tutor_archive_hide_duration_popup): ?>
                            <?php 
                                 $course_duration = get_tutor_course_duration_context();
                            if (!$tutor_archive_hide_lessons_popup && !empty($course_duration)): ?>
                                <span class="course-lessons">
                                    <i class="flaticon-wall-clock"></i> 
                                    <?php echo wp_kses_post( $course_duration ); ?>  
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                    <?php if (!$tutor_archive_hide_excerpt_content_popup): ?>
                      <div class="course__excerpt">
                        <p><?php the_excerpt(); ?></p>
                     </div>
                    <?php endif; ?>

                    <?php if (!$tutor_archive_hide_preview_btn): ?>
                    <div class="btn__wrap">
                        <a class="btn__a" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Preview This Course', 'gostudy' ); ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="course__container">

                <div class="rt-video_popup with_image">
                    <div class="videobox_content">

                <?php if (!$tutor_archive_hide_media): ?>
                    <div class="course__media">

                        <?php if ( has_post_thumbnail() ):?>
                          <a class="course__media-link" href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('large');?>
                          </a>
                         <?php endif; ?>

                    </div>

                <!-- Start video -->
                <?php       
                    $video = maybe_unserialize( get_post_meta( get_the_ID(), '_video', true ) );
                    $videoSource    = tutor_utils()->avalue_dot( 'source', $video );

                    if ($video["source_youtube"] && $videoSource == 'youtube') {
                        $tutor_intro_video =    $video["source_youtube"];
                    }
                    elseif($video["source_vimeo"] && $videoSource == 'vimeo'){
                        $tutor_intro_video =    $video["source_vimeo"];
                    }
                    elseif($video["source_external_url"] && $videoSource == 'external_url'){
                        $tutor_intro_video =    $video["source_external_url"];
                    }
                    // elseif($video["source_video_id"] && $videoSource == 'html5'){
                    //  $tutor_intro_video =    $video["source_video_id"];
                    // }
                    // elseif($video["source_embedded"] && $videoSource == 'embedded'){
                    //  $tutor_intro_video =    $video["source_embedded"];
                    // }
                    else{
                        $tutor_intro_video =    '';
                    }

                    ?>

                    <?php
                        if ($tutor_tf_hover_popup_type == 'show_video_popup' && !empty($tutor_intro_video )) {
                    ?>

                    <div class="videobox_link_wrapper">

                        <div class="videobox_link" data-lity="" href="<?php echo esc_url( $tutor_intro_video ); ?>">
                            <svg class="videobox_icon" width="33%" height="33%" viewBox="0 0 232 232"><path d="M203,99L49,2.3c-4.5-2.7-10.2-2.2-14.5-2.2 c-17.1,0-17,13-17,16.6v199c0,2.8-0.07,16.6,17,16.6c4.3,0,10,0.4,14.5-2.2 l154-97c12.7-7.5,10.5-16.5,10.5-16.5S216,107,204,100z"></path>
                            </svg>
                        </div>
                    </div>
                <?php }  // End video popup ?>
                <?php endif; // End media ?>
                </div>
            </div>

                <div class="course__content">
                    <div class="course__content--info">

                        <div class="course__top-meta">

                            <?php if(!$tutor_archive_hide_categories): ?>
                                    <div class="course__categories ">
                                        <?php echo get_the_term_list(get_the_ID(), 'course-category'); ?>
                                    </div>
                             <?php endif; ?>

                        </div>
     
                        <?php if (!$tutor_archive_hide_title): ?>
                            <h4 class="course__title">
                                <a href="<?php the_permalink(); ?>" class="course__title-link">
                                    <?php the_title(); ?>
                                </a>
                            </h4>
                        <?php endif; ?>

                    <?php if (!$tutor_archive_hide_excerpt_content): ?>
                      <div class="course__excerpt">
                        <p><?php the_excerpt(); ?></p>
                     </div>
                    <?php endif; ?>

                        <?php if (!$tutor_archive_hide_created_by): ?>
                            <div class="rt-course-author-name">
                                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?></a>
                                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_the_author(); ?></a>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="course__content--meta"> 

                        <div class="course__meta-left">
                            <?php if (!$tutor_archive_hide_enroll_btn): ?>
                                <?php
                                    $disable_total_enrolled = get_tutor_option('disable_course_total_enrolled');
                                    if( !$disable_total_enrolled){ ?>
                                        <span class="course-enroll"><i class="tutor-icon-user"></i> <?php echo (int) tutor_utils()->count_enrolled_users_by_course(); ?></span>
                                    <?php } 
                                ?>
                            <?php endif; ?>

                            <?php if (!$tutor_archive_hide_lessons): ?>
                                <?php
                                    $tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                                    if($tutor_lesson_count) { ?>
                                     <span class="course-lesson"><i class="flaticon-book-1"></i> <?php echo esc_attr($tutor_lesson_count); ?></span>
                                    <?php }
                                ?>   
                            <?php endif; ?>

                            <?php
                                $disable_course_review = get_tutor_option('disable_course_review');

                                $course_rating = tutor_utils()->get_course_rating();
                                $total_reviews = apply_filters('tutor_course_rating_count', $course_rating->rating_count);
                                if( !$disable_course_review){ 
                                    if ($course_rating->rating_avg > 0) {
                                        printf(
                                            '<span class="course-rating"><i class="flaticon-star-1"></i> %1$d</span>',
                                            $total_reviews,
                                        ); 
                                    }
                                 }
                            ?>
                        </div>

                        <div class="course__meta-right">
                            <?php 
                                //ob_start();
                                get_template_part('templates/tutor/price_within_button_2');
                                //$button = ob_get_clean();
                            ?>
                        </div>

                    </div>
                </div>

            </div>

    <?php elseif($tutor_archive_layout == '2') : ?>

            <?php  if ($tutor_tf_hover_popup_type == 'show_content_popup') : ?>
                <div class="course__popup-wrap">

                    <?php if (!$tutor_archive_hide_created_by_popup): ?>
                        <div class="rt-course-author-name">
                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
                            <?php echo get_the_author(); ?>
                        </div>
                    <?php endif; ?>

                      <div class="courses-content">

                        <div class="course__top-meta">
                             <?php if (!$tutor_archive_hide_categories_popup): ?>
                                <div class="course__categories ">
                                    <?php echo get_the_term_list(get_the_ID(), 'course-category'); ?>
                                </div>
                             <?php endif; ?>
                             <?php if (!$tutor_archive_hide_price_popup): ?>
                             <div class="price">
                                  <?php get_template_part('templates/tutor/price_within_button_2'); ?>
                             </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!$tutor_archive_hide_title_popup): ?>
                        <h4 class="course__title">
                            <a href="<?php the_permalink(); ?>" class="course__title-link">
                                <?php the_title(); ?>
                            </a>
                        </h4>
                        <?php endif; ?>
                     </div>
                    <div class="course__content--meta"> 

                        <?php if (!$tutor_archive_hide_lessons_popup): ?>

                            <span class="course-lessons">
                                <i class="flaticon-book-1"></i>
                                <?php
                                    $tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                                    if($tutor_lesson_count) {
                                        echo "<span> $tutor_lesson_count";
                                        esc_html_e(' Lessons', 'gostudy');
                                        echo "</span>";
                                    }
                                ?>  
                            </span>

                        <?php endif; ?>

                        <?php if (!$tutor_archive_hide_duration_popup): ?>
                            <?php 
                                 $course_duration = get_tutor_course_duration_context();
                            if (!$tutor_archive_hide_lessons_popup && !empty($course_duration)): ?>
                                <span class="course-lessons">
                                    <i class="flaticon-wall-clock"></i> 
                                    <?php echo wp_kses_post( $course_duration ); ?>  
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                    <?php if (!$tutor_archive_hide_excerpt_content_popup): ?>
                      <div class="course__excerpt">
                        <p><?php the_excerpt(); ?></p>
                     </div>
                    <?php endif; ?>

                    <?php if (!$tutor_archive_hide_preview_btn): ?>
                    <div class="btn__wrap">
                        <a class="btn__a" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Preview This Course', 'gostudy' ); ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="course__container">

                <div class="rt-video_popup with_image">
                    <div class="videobox_content">

                <?php if (!$tutor_archive_hide_media): ?>
                    <div class="course__media">

                        <?php if ( has_post_thumbnail() ):?>
                          <a class="course__media-link" href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('large');?>
                          </a>
                         <?php endif; ?>

                    </div>

                <!-- Start video -->
                <?php       
                    $video = maybe_unserialize( get_post_meta( get_the_ID(), '_video', true ) );
                    $videoSource    = tutor_utils()->avalue_dot( 'source', $video );

                    if ($video["source_youtube"] && $videoSource == 'youtube') {
                        $tutor_intro_video =    $video["source_youtube"];
                    }
                    elseif($video["source_vimeo"] && $videoSource == 'vimeo'){
                        $tutor_intro_video =    $video["source_vimeo"];
                    }
                    elseif($video["source_external_url"] && $videoSource == 'external_url'){
                        $tutor_intro_video =    $video["source_external_url"];
                    }
                    // elseif($video["source_video_id"] && $videoSource == 'html5'){
                    //  $tutor_intro_video =    $video["source_video_id"];
                    // }
                    // elseif($video["source_embedded"] && $videoSource == 'embedded'){
                    //  $tutor_intro_video =    $video["source_embedded"];
                    // }
                    else{
                        $tutor_intro_video =    '';
                    }

                    ?>

                    <?php
                        if ($tutor_tf_hover_popup_type == 'show_video_popup' && !empty($tutor_intro_video )) {
                    ?>

                    <div class="videobox_link_wrapper">

                        <div class="videobox_link" data-lity="" href="<?php echo esc_url( $tutor_intro_video ); ?>">
                            <svg class="videobox_icon" width="33%" height="33%" viewBox="0 0 232 232"><path d="M203,99L49,2.3c-4.5-2.7-10.2-2.2-14.5-2.2 c-17.1,0-17,13-17,16.6v199c0,2.8-0.07,16.6,17,16.6c4.3,0,10,0.4,14.5-2.2 l154-97c12.7-7.5,10.5-16.5,10.5-16.5S216,107,204,100z"></path>
                            </svg>
                        </div>
                    </div>
                <?php }  // End video popup ?>
                <?php endif; // End media ?>
                </div>
            </div>

                <div class="course__content">
                    <div class="course__content--info">

                        <div class="course__top-meta">

                            <?php if(!$tutor_archive_hide_categories): ?>
                                    <div class="course__categories ">
                                        <?php echo get_the_term_list(get_the_ID(), 'course-category'); ?>
                                    </div>
                             <?php endif; ?>

                            <?php if(!$tutor_archive_hide_price): ?>
                              <?php get_template_part('templates/tutor/price_within_button'); ?>
                            <?php endif; ?>

                        </div>
     
                        <?php if (!$tutor_archive_hide_title): ?>
                            <h4 class="course__title">
                                <a href="<?php the_permalink(); ?>" class="course__title-link">
                                    <?php the_title(); ?>
                                </a>
                            </h4>
                        <?php endif; ?>

                    <?php if (!$tutor_archive_hide_excerpt_content): ?>
                      <div class="course__excerpt">
                        <p><?php the_excerpt(); ?></p>
                     </div>
                    <?php endif; ?>

                        <?php if (!$tutor_archive_hide_created_by): ?>
                            <div class="rt-course-author-name">
                                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?></a>
                                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_the_author(); ?></a>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="course__content--meta"> 

                        <div class="course__meta-left">
                            <?php if (!$tutor_archive_hide_enroll_btn): ?>
                                <?php
                                    $disable_total_enrolled = get_tutor_option('disable_course_total_enrolled');
                                    if( !$disable_total_enrolled){ ?>
                                        <span class="course-enroll"><i class="tutor-icon-user"></i> <?php echo (int) tutor_utils()->count_enrolled_users_by_course(); ?></span>
                                    <?php } 
                                ?>
                            <?php endif; ?>

                            <?php if (!$tutor_archive_hide_lessons): ?>
                                <?php
                                    $tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                                    if($tutor_lesson_count) { ?>
                                     <span class="course-lesson"><i class="flaticon-book-1"></i> <?php echo esc_attr($tutor_lesson_count); ?></span>
                                    <?php }
                                ?>   
                            <?php endif; ?>
                        </div>

                        <div class="course__meta-right">
                     <?php
                      $disable = get_tutor_option('disable_course_review');
                      if ( ! $disable){
                        ?>

                            <?php
                            $course_rating = tutor_utils()->get_course_rating();
                            tutor_utils()->star_rating_generator($course_rating->rating_avg);
                            ?>
         
                      <?php } ?>
                        </div>

                    </div>
                </div>

            </div>

    <?php elseif($tutor_archive_layout == '3') : ?>

            <?php  if ($tutor_tf_hover_popup_type == 'show_content_popup') : ?>
                <div class="course__popup-wrap">

                    <?php if (!$tutor_archive_hide_created_by_popup): ?>
                        <div class="rt-course-author-name">
                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
                            <?php echo get_the_author(); ?>
                        </div>
                    <?php endif; ?>

                      <div class="courses-content">

                        <div class="course__top-meta">
                             <?php if (!$tutor_archive_hide_categories_popup): ?>
                                <div class="course__categories ">
                                    <?php echo get_the_term_list(get_the_ID(), 'course-category'); ?>
                                </div>
                             <?php endif; ?>
                             <?php if (!$tutor_archive_hide_price_popup): ?>
                             <div class="price">
                                  <?php get_template_part('templates/tutor/price_within_button_2'); ?>
                             </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!$tutor_archive_hide_title_popup): ?>
                        <h4 class="course__title">
                            <a href="<?php the_permalink(); ?>" class="course__title-link">
                                <?php the_title(); ?>
                            </a>
                        </h4>
                        <?php endif; ?>
                     </div>
                    <div class="course__content--meta"> 

                        <?php if (!$tutor_archive_hide_lessons_popup): ?>

                            <span class="course-lessons">
                                <i class="flaticon-book-1"></i>
                                <?php
                                    $tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                                    if($tutor_lesson_count) {
                                        echo "<span> $tutor_lesson_count";
                                        esc_html_e(' Lessons', 'gostudy');
                                        echo "</span>";
                                    }
                                ?>  
                            </span>

                        <?php endif; ?>

                        <?php if (!$tutor_archive_hide_duration_popup): ?>
                            <?php 
                                 $course_duration = get_tutor_course_duration_context();
                            if (!$tutor_archive_hide_lessons_popup && !empty($course_duration)): ?>
                                <span class="course-lessons">
                                    <i class="flaticon-wall-clock"></i> 
                                    <?php echo wp_kses_post( $course_duration ); ?>  
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                    <?php if (!$tutor_archive_hide_excerpt_content_popup): ?>
                      <div class="course__excerpt">
                        <p><?php the_excerpt(); ?></p>
                     </div>
                    <?php endif; ?>

                    <?php if (!$tutor_archive_hide_preview_btn): ?>
                    <div class="btn__wrap">
                        <a class="btn__a" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Preview This Course', 'gostudy' ); ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="course__container">

                <div class="rt-video_popup with_image">
                    <div class="videobox_content">

                <?php if (!$tutor_archive_hide_media): ?>
                    <div class="course__media">

                        <?php if ( has_post_thumbnail() ):?>
                          <a class="course__media-link" href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('large');?>
                          </a>
                         <?php endif; ?>

                    </div>

                <!-- Start video -->
                <?php       
                    $video = maybe_unserialize( get_post_meta( get_the_ID(), '_video', true ) );
                    $videoSource    = tutor_utils()->avalue_dot( 'source', $video );

                    if ($video["source_youtube"] && $videoSource == 'youtube') {
                        $tutor_intro_video =    $video["source_youtube"];
                    }
                    elseif($video["source_vimeo"] && $videoSource == 'vimeo'){
                        $tutor_intro_video =    $video["source_vimeo"];
                    }
                    elseif($video["source_external_url"] && $videoSource == 'external_url'){
                        $tutor_intro_video =    $video["source_external_url"];
                    }
                    // elseif($video["source_video_id"] && $videoSource == 'html5'){
                    //  $tutor_intro_video =    $video["source_video_id"];
                    // }
                    // elseif($video["source_embedded"] && $videoSource == 'embedded'){
                    //  $tutor_intro_video =    $video["source_embedded"];
                    // }
                    else{
                        $tutor_intro_video =    '';
                    }

                    ?>

                    <?php
                        if ($tutor_tf_hover_popup_type == 'show_video_popup' && !empty($tutor_intro_video )) {
                    ?>

                    <div class="videobox_link_wrapper">

                        <div class="videobox_link" data-lity="" href="<?php echo esc_url( $tutor_intro_video ); ?>">
                            <svg class="videobox_icon" width="33%" height="33%" viewBox="0 0 232 232"><path d="M203,99L49,2.3c-4.5-2.7-10.2-2.2-14.5-2.2 c-17.1,0-17,13-17,16.6v199c0,2.8-0.07,16.6,17,16.6c4.3,0,10,0.4,14.5-2.2 l154-97c12.7-7.5,10.5-16.5,10.5-16.5S216,107,204,100z"></path>
                            </svg>
                        </div>
                    </div>
                <?php }  // End video popup ?>
                <?php endif; // End media ?>
                </div>
            </div>

                <div class="course__content">
                    <div class="course__content--info">

                        <div class="course__top-meta">

                            <?php if(!$tutor_archive_hide_categories): ?>
                                    <div class="course__categories ">
                                        <?php echo get_the_term_list(get_the_ID(), 'course-category'); ?>
                                    </div>
                             <?php endif; ?>

                            <?php if(!$tutor_archive_hide_price): ?>
                              <?php get_template_part('templates/tutor/price_within_button_2'); ?>
                            <?php endif; ?>

                        </div>
     
                        <?php if (!$tutor_archive_hide_title): ?>
                            <h4 class="course__title">
                                <a href="<?php the_permalink(); ?>" class="course__title-link">
                                    <?php the_title(); ?>
                                </a>
                            </h4>
                        <?php endif; ?>

                    <?php if (!$tutor_archive_hide_excerpt_content): ?>
                      <div class="course__excerpt">
                        <p><?php the_excerpt(); ?></p>
                     </div>
                    <?php endif; ?>

                        <?php if (!$tutor_archive_hide_created_by): ?>
                            <div class="rt-course-author-name">
                                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?></a>
                                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_the_author(); ?></a>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="course__content--meta"> 

                        <div class="course__meta-left">
                            <?php if (!$tutor_archive_hide_enroll_btn): ?>
                                <?php
                                    $disable_total_enrolled = get_tutor_option('disable_course_total_enrolled');
                                    if( !$disable_total_enrolled){ ?>
                                        <span class="course-enroll"><i class="tutor-icon-user"></i> <?php echo (int) tutor_utils()->count_enrolled_users_by_course(); ?></span>
                                    <?php } 
                                ?>
                            <?php endif; ?>

                            <?php if (!$tutor_archive_hide_lessons): ?>
                                <?php
                                    $tutor_lesson_count = tutor_utils()->get_lesson_count_by_course(get_the_ID());
                                    if($tutor_lesson_count) { ?>
                                     <span class="course-lesson"><i class="flaticon-book-1"></i> <?php echo esc_attr($tutor_lesson_count); ?></span>
                                    <?php }
                                ?>   
                            <?php endif; ?>
                        </div>

                        <div class="course__meta-right">
                     <?php
                      $disable = get_tutor_option('disable_course_review');
                      if ( ! $disable){
                        ?>
                            <div class="course__review">
                                <span class="review-stars-rated">
                                    <?php
                                    $course_rating = tutor_utils()->get_course_rating();
                                    tutor_utils()->star_rating_generator($course_rating->rating_avg);
                                    ?>
                                </span>
                            </div>
                      <?php } ?>
                        </div>

                    </div>
                </div>

            </div>

    <?php endif; ?>

