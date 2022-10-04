<?php

/**
 * Custom Course features
 */

function gostudy_learndash_course_meata()
{

    $learndash_single_hide_instructor = Gostudy_Theme_Helper::get_mb_option('learndash_single_hide_instructor');
    $learndash_single_hide_topic      = Gostudy_Theme_Helper::get_mb_option('learndash_single_hide_topic');
    $learndash_single_hide_quiz       = Gostudy_Theme_Helper::get_mb_option('learndash_single_hide_quiz');
    $learndash_single_hide_enrolled   = Gostudy_Theme_Helper::get_mb_option('learndash_single_hide_enrolled');
    $learndash_single_hide_lesson     = Gostudy_Theme_Helper::get_mb_option('learndash_single_hide_lesson');
    $learndash_single_hide_category   = Gostudy_Theme_Helper::get_mb_option('learndash_single_hide_category');

    $learndash_free_text      = Gostudy_Theme_Helper::get_mb_option('learndash_free_text');
    $learndash_enrolled_text  = Gostudy_Theme_Helper::get_mb_option('learndash_enrolled_text');
    $learndash_completed_text = Gostudy_Theme_Helper::get_mb_option('learndash_completed_text');

    global $post;
    $post_id    = $post->ID;
    $course_id  = $post_id;
    $user_id    = get_current_user_id();
    $current_id = $post->ID;

    $options = get_option('sfwd_cpt_options');

    $currency = null;

    if (!is_null($options)) {
        if (isset($options['modules']) && isset($options['modules']['sfwd-courses_options']) && isset($options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'])) {
            $currency = $options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'];
        }

    }

    if (is_null($currency)) {
        $currency = 'USD';
    }

    $course_options = get_post_meta($post_id, "_sfwd-courses", true);

    $price = $course_options && isset($course_options['sfwd-courses_course_price']) ? $course_options['sfwd-courses_course_price'] : $learndash_free_text;

    $has_access   = sfwd_lms_has_access($course_id, $user_id);
    $is_completed = learndash_course_completed($user_id, $course_id);

    if ($price == '') {
        $price .= $learndash_free_text;
    }

    if (is_numeric($price)) {
        if ($currency == "USD") {
            $price = '$' . $price;
        } else {
            $price .= ' ' . $currency;
        }

    }

    $class       = '';
    $ribbon_text = '';

    if ($has_access && !$is_completed) {
        $class       = 'ld_course_grid_price ribbon-enrolled';
        $ribbon_text = $learndash_enrolled_text;
    } elseif ($has_access && $is_completed) {
        $class       = 'ld_course_grid_price';
        $ribbon_text = $learndash_completed_text;
    } else {
        $class       = !empty($course_options['sfwd-courses_course_price']) ? 'ld_course_grid_price price_' . $currency : 'ld_course_grid_price free';
        $ribbon_text = $price;
    }

    ?>
    <div class="learndash-single-course-meta learndash-meta-top">

            <div class="course-sidebar-preview">
                <div class="media-preview">
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('medium_large');?>
                    </div>
                </div>
            </div>
        <?php if (!$learndash_single_hide_instructor) {?>
           <div class="learndash-course-features instructor">
                <span class="meta-label">
                    <i class="meta-icon flaticon-user-1"></i>
                    <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php esc_html_e('Created by', 'gostudy');?></a>
                </span>

              <div class="meta-value">
                <?php echo get_the_author(); ?>
            </div>
           </div>
        <?php }?>

        <?php if (!$learndash_single_hide_enrolled) {

        $lesson      = learndash_get_course_steps(get_the_ID(), array('sfwd-lessons'));
        $lesson      = $lesson ? count($lesson) : 0;
        $lesson_text = ('1' == $lesson) ? esc_html__('Lesson', 'gostudy') : esc_html__('Lessons', 'gostudy');

        ?>
           <div class="learndash-course-features lesson-count">
                <span class="meta-label">
                    <i class="meta-icon flaticon-book-1"></i>
                    <?php esc_html_e('Lessons', 'gostudy');?>
                </span>
              <div class="meta-value">
                    <?php echo esc_attr($lesson); ?>
                    <?php echo esc_html($lesson_text); ?>
              </div>
           </div>
        <?php }?>

        <?php if (!$learndash_single_hide_topic) {

        $topic      = learndash_get_course_steps(get_the_ID(), array('sfwd-topic'));
        $topic      = $topic ? count($topic) : 0;
        $topic_text = ('1' == $topic) ? esc_html__('Topic', 'gostudy') : esc_html__('Topics', 'gostudy');
        ?>
           <div class="learndash-course-features topic">
              <span class="meta-label">
               <i class="meta-icon flaticon-edit-file"></i>
                    <?php esc_html_e('Topic', 'gostudy');?>
            </span>
              <div class="meta-value">
                <?php echo esc_attr($topic); ?>
                <?php echo esc_html($topic_text); ?>
            </div>
           </div>
        <?php }?>

        <?php if (2>3 ) {
        $quiz      = learndash_get_course_steps(get_the_ID(), array('sfwd-quiz'));
        $quiz      = $quiz ? count($quiz) : 0;
        $quiz_text = ('1' == $quiz) ? esc_html__('Quiz', 'gostudy') : esc_html__('Quizzes', 'gostudy');
        ?>
           <div class="learndash-course-features duration">
            <span class="meta-label">
                <i class="meta-icon flaticon-question"></i>
                <?php esc_html_e('Quiz', 'gostudy');?>
            </span>
              <?php echo esc_attr($quiz); ?>
              <?php echo esc_html($quiz_text); ?>
           </div>
        <?php }?>


    <?php if (!$learndash_single_hide_category): ?>

       <div class="learndash-course-features categories">

            <span class="meta-label">
                <i class="meta-icon flaticon-price-tag"></i>
                <?php esc_html_e('Category', 'gostudy');?>
            </span>

          <div class="meta-value">
            <?php
            if (!get_the_terms(get_the_ID(), 'ld_course_category')) {
                    esc_html_e('Uncategorized', 'gostudy');
                } else {
                    echo get_the_term_list(get_the_ID(), 'ld_course_category', '');
                }
            ?>
          </div>
       </div>
    <?php endif?>

    <div class="course-share-container">
        <span class="share_social-title">
            <?php echo esc_html__('Share this course:','gostudy') ?>  
        </span>
        <?php rt_theme_helper()->render_post_share(); ?>
    </div>

    </div>

    <?php
}

/**
 * Show course for user archive page
 */

$gostudy_author_archive_posts_type = Gostudy_Theme_Helper::get_mb_option('gostudy_author_archive_posts_type');
$gostudy_author_archive_posts      = Gostudy_Theme_Helper::get_mb_option('gostudy_author_archive_posts');

if ($gostudy_author_archive_posts) {
    add_action('pre_get_posts', $gostudy_author_archive_posts_type);
}

function gostudy_author_learndash($query)
{
    if (!is_admin() && $query->is_author() && $query->is_main_query()) {
        $query->set('post_type', 'sfwd-courses');
    }
}

function gostudy_author_any($query)
{
    if (!is_admin() && $query->is_author() && $query->is_main_query()) {
        $query->set('post_type', 'any');
    }
}
