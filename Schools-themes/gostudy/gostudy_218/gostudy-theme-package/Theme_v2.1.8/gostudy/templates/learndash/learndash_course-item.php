<?php
/**
 * Template for displaying course content within the loop.
 *
 * This template can be overridden by copying it to yourtheme/learndash/content-course.php
 *
 * @author  ThimPress
 * @package LearnDash/Templates
 * @version 4.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$learndash_archive_layout = Gostudy_Theme_Helper::get_mb_option('learndash_archive_layout');
$learndash_see_more_text = Gostudy_Theme_Helper::get_mb_option('learndash_see_more_text');
$learndash_archive_hide_media = Gostudy_Theme_Helper::get_mb_option('learndash_archive_hide_media');
$learndash_archive_hide_categories = Gostudy_Theme_Helper::get_mb_option('learndash_archive_hide_categories');
$learndash_archive_hide_price = Gostudy_Theme_Helper::get_mb_option('learndash_archive_hide_price');
$learndash_archive_hide_title = Gostudy_Theme_Helper::get_mb_option('learndash_archive_hide_title');
$learndash_archive_hide_created_by = Gostudy_Theme_Helper::get_mb_option('learndash_archive_hide_created_by');
$learndash_archive_hide_lessons = Gostudy_Theme_Helper::get_mb_option('learndash_archive_hide_lessons');
$learndash_archive_hide_lessons_text = Gostudy_Theme_Helper::get_mb_option('learndash_archive_hide_lessons_text');
$learndash_archive_hide_topic = Gostudy_Theme_Helper::get_mb_option('learndash_archive_hide_topic');
$learndash_archive_hide_topic_text = Gostudy_Theme_Helper::get_mb_option('learndash_archive_hide_topic_text');
$learndash_archive_hide_enroll_btn = Gostudy_Theme_Helper::get_mb_option('learndash_archive_hide_enroll_btn');
$learndash_enroll_now_text = Gostudy_Theme_Helper::get_mb_option('learndash_enroll_now_text');
$learndash_archive_enroll_btn_switch = Gostudy_Theme_Helper::get_mb_option('learndash_archive_enroll_btn_switch');
$learndash_archive_hide_excerpt_content = Gostudy_Theme_Helper::get_mb_option('learndash_archive_hide_excerpt_content');
$learndash_free_text = Gostudy_Theme_Helper::get_mb_option('learndash_free_text');
$learndash_enrolled_text = Gostudy_Theme_Helper::get_mb_option('learndash_enrolled_text');
$learndash_completed_text = Gostudy_Theme_Helper::get_mb_option('learndash_completed_text');


 if (class_exists('SFWD_LMS')) {

   // For ribbon_text 
   global $post; $post_id = $post->ID;
   $course_id = $post_id;
   $user_id   = get_current_user_id();
   $current_id = $post->ID;

   $options = get_option('sfwd_cpt_options');


   $currency = null;

   if ( ! is_null( $options ) ) {
      if ( isset($options['modules'] ) && isset( $options['modules']['sfwd-courses_options'] ) && isset( $options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'] ) )
         $currency = $options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'];

   }

   if( is_null( $currency ) )
      $currency = 'USD';

   $course_options = get_post_meta($post_id, "_sfwd-courses", true);


   $price = $course_options && isset($course_options['sfwd-courses_course_price']) ? $course_options['sfwd-courses_course_price'] : $learndash_free_text;

   $has_access   = sfwd_lms_has_access( $course_id, $user_id );
   $is_completed = learndash_course_completed( $user_id, $course_id );

   if( $price == '' )
      $price .= $learndash_free_text;

   if ( is_numeric( $price ) ) {
      if ( $currency == "USD" )
         $price = '$' . $price;
      else
         $price .= ' ' . $currency;
   }

   $class       = '';
   $ribbon_text = '';

   if ( $has_access && ! $is_completed ) {
      $class = 'ld_course_grid_price ribbon-enrolled';
      $ribbon_text = $learndash_enrolled_text;
   } elseif ( $has_access && $is_completed ) {
      $class = 'ld_course_grid_price';
      $ribbon_text = $learndash_completed_text;
   } else {
      $class = ! empty( $course_options['sfwd-courses_course_price'] ) ? 'ld_course_grid_price price_' . $currency : 'ld_course_grid_price free';
      $ribbon_text = $price;
   }
?>

<?php 
  // For legacy_short_description
	$legacy_short_description = '';
	if ( $post_type == 'sfwd-courses' ) {
	    $legacy_short_description = isset( $course_options['sfwd-courses_course_short_description'] ) ? $course_options['sfwd-courses_course_short_description'] : '';
	}

	if ( ! empty( $cg_short_description ) ) {
	    $short_description = $cg_short_description;
	} elseif ( ! empty( $legacy_short_description ) ) {
	    $short_description = $legacy_short_description;
	} else {
	    $short_description = '';
	}
 ?>
<?php if ($learndash_archive_layout == '3'): ?>
	<article class="rt-course">
		<div class="course__container">

			<?php if (!$learndash_archive_hide_media): ?>
				<div class="course__media">

					<?php if ( has_post_thumbnail() ):?>
					  <a class="course__media-link" href="<?php the_permalink(); ?>">
					    <?php the_post_thumbnail('medium');?>
					  </a>
					 <?php endif; ?>
					 <?php if (!$learndash_archive_hide_categories): ?>
						<div class="course__categories ">
							<?php echo get_the_term_list(get_the_ID(), 'ld_course_category'); ?>
						</div>
					 <?php endif; ?>
				</div>
			<?php endif ?>

			<div class="course__content">
				<div class="course__content--info">

					<?php if (!$learndash_archive_hide_created_by): ?>
				        <div class="rt-course-author-name">
				            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?></a>
				            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_the_author(); ?></a>
				        </div>
					<?php endif; ?>
					<?php if (!$learndash_archive_hide_title): ?>
					<h4 class="course__title">
						<a href="<?php the_permalink(); ?>" class="course__title-link">
							<?php the_title(); ?>
						</a>
					</h4>
					<?php endif; ?>

					<div class="course-meta">
					<?php if (!$learndash_archive_hide_topic): 

						$topic  = learndash_get_course_steps( get_the_ID(), array( 'sfwd-topic') );
						$topic = $topic ? count($topic) : 0;
						$topic_text = ('1' == $topic) ? esc_html__('Topic', 'gostudy') : esc_html__('Topics', 'gostudy'); ?>
						<span class="course-topic"> 
							<i class="flaticon-edit-file"></i> 
							<?php echo esc_html($topic); ?>
							<?php if (!$learndash_archive_hide_topic_text): ?>
							<?php echo esc_html($topic_text); ?>
							<?php endif; ?>
						</span>

					<?php endif; ?>

					</div>

				</div>
				<div class="course__content--meta"> 

					<?php if (!$learndash_archive_hide_lessons): 

						$lesson  = learndash_get_course_steps( get_the_ID(), array('sfwd-lessons') );
						$lesson = $lesson ? count($lesson) : 0;
						$lesson_text = ('1' == $lesson) ? esc_html__('Lesson', 'gostudy') : esc_html__('Lessons', 'gostudy'); ?>
						<span class="course-lesson"> 
							<i class="flaticon-book-1"></i> 
							<?php echo esc_html($lesson); ?>
							<?php if (!$learndash_archive_hide_lessons_text): ?>
							<?php echo esc_html($lesson_text); ?>
							<?php endif; ?>
						</span>

					<?php endif; ?>

			        <?php get_template_part('templates/learndash/price_within_button_2'); ?>
				</div>
			</div>

		</div>
	</article>
<?php elseif ($learndash_archive_layout == '2'): ?>
	<article class="rt-course">
		<div class="course__container">

			<?php if (!$learndash_archive_hide_media): ?>
				<div class="course__media">

					<?php if ( has_post_thumbnail() ):?>
					  <a class="course__media-link" href="<?php the_permalink(); ?>">
					    <?php the_post_thumbnail('medium');?>
					  </a>
					 <?php endif; ?>
					 <?php if (!$learndash_archive_hide_categories): ?>
						<div class="course__categories ">
							<?php echo get_the_term_list(get_the_ID(), 'ld_course_category'); ?>
						</div>
					 <?php endif; ?>
				</div>
			<?php endif ?>

			<div class="course__content">
				<div class="course__content--info">

					<?php if (!$learndash_archive_hide_created_by): ?>
				        <div class="rt-course-author-name">
				          	<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?></a>
				           	<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_the_author(); ?></a>
				        </div>
					<?php endif; ?>
					<?php if (!$learndash_archive_hide_title): ?>
					<h4 class="course__title">
						<a href="<?php the_permalink(); ?>" class="course__title-link">
							<?php the_title(); ?>
						</a>
					</h4>
					<?php endif; ?>
				</div>
				<div class="course__content--meta"> 

					<?php if (!$learndash_archive_hide_lessons): 

						$lesson  = learndash_get_course_steps( get_the_ID(), array('sfwd-lessons') );
						$lesson = $lesson ? count($lesson) : 0;
						$lesson_text = ('1' == $lesson) ? esc_html__('Lesson', 'gostudy') : esc_html__('Lessons', 'gostudy'); ?>
						<span class="course-lesson"> 
							<i class="flaticon-book-1"></i> 
							<?php echo esc_html($lesson); ?>
							<?php if (!$learndash_archive_hide_lessons_text): ?>
							<?php echo esc_html($lesson_text); ?>
							<?php endif; ?>
						</span>

					<?php endif; ?>

					<?php if (!$learndash_archive_hide_topic): 

						$topic  = learndash_get_course_steps( get_the_ID(), array( 'sfwd-topic') );
						$topic = $topic ? count($topic) : 0;
						$topic_text = ('1' == $topic) ? esc_html__('Topic', 'gostudy') : esc_html__('Topics', 'gostudy'); ?>
						<span class="course-topic"> 
							<i class="flaticon-edit-file"></i> 
							<?php echo esc_html($topic); ?>
							<?php if (!$learndash_archive_hide_topic_text): ?>
							<?php echo esc_html($topic_text); ?>
							<?php endif; ?>
						</span>

					<?php endif; ?>

			        <?php get_template_part('templates/learndash/price_within_button_2'); ?>
				</div>
			</div>

		</div>
	</article>
<?php else : ?>	
	<article class="rt-course">
		<div class="course__container">

			<?php if (!$learndash_archive_hide_media): ?>
				<div class="course__media">

					<?php if ( has_post_thumbnail() ):?>
					  <a class="course__media-link" href="<?php the_permalink(); ?>">
					    <?php the_post_thumbnail('medium');?>
					  </a>
					 <?php endif; ?>
					 <?php if (!$learndash_archive_hide_categories): ?>
						<div class="course__categories ">
							<?php echo get_the_term_list(get_the_ID(), 'ld_course_category'); ?>
						</div>
					 <?php endif; ?>
				</div>
			<?php endif ?>

			<div class="course__content">
				<div class="course__content--info">

					<?php if (!$learndash_archive_hide_price): ?>
						<?php get_template_part('templates/learndash/price_within_button'); ?>
					<?php endif; ?>

					<?php if (!$learndash_archive_hide_created_by): ?>
				        <div class="rt-course-author-name">
				            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?></a>
				            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ))); ?>"><?php echo get_the_author(); ?></a>
				        </div>
					<?php endif; ?>
					<?php if (!$learndash_archive_hide_title): ?>
					<h4 class="course__title">
						<a href="<?php the_permalink(); ?>" class="course__title-link">
							<?php the_title(); ?>
						</a>
					</h4>
					<?php endif; ?>

					<?php if (!$learndash_archive_hide_excerpt_content): ?>
						<div class="course__excerpt">
							<p><?php echo esc_html( $short_description ); ?></p>
						</div>
					<?php endif; ?>

				</div>
				<div class="course__content--meta"> 

					<?php if (!$learndash_archive_hide_lessons): 

						$lesson  = learndash_get_course_steps( get_the_ID(), array('sfwd-lessons') );
						$lesson = $lesson ? count($lesson) : 0;
						$lesson_text = ('1' == $lesson) ? esc_html__('Lesson', 'gostudy') : esc_html__('Lessons', 'gostudy'); ?>
						<span class="course-lesson"> 
							<i class="flaticon-book-1"></i> 
							<?php echo esc_html($lesson); ?>
							<?php if (!$learndash_archive_hide_lessons_text): ?>
							<?php echo esc_html($lesson_text); ?>
							<?php endif; ?>
						</span>

					<?php endif; ?>

					<?php if (!$learndash_archive_hide_topic): 

						$topic  = learndash_get_course_steps( get_the_ID(), array( 'sfwd-topic') );
						$topic = $topic ? count($topic) : 0;
						$topic_text = ('1' == $topic) ? esc_html__('Topic', 'gostudy') : esc_html__('Topics', 'gostudy'); ?>
						<span class="course-topic"> 
							<i class="flaticon-edit-file"></i> 
							<?php echo esc_html($topic); ?>
							<?php if (!$learndash_archive_hide_topic_text): ?>
							<?php echo esc_html($topic_text); ?>
							<?php endif; ?>
						</span>

					<?php endif; ?>



			         <?php if (!$learndash_archive_hide_enroll_btn): ?>	
		                <div class="ld-enroll-btn">
			                <?php if ( $learndash_archive_enroll_btn_switch  ) { ?>

			                <?php if ( $has_access && ! $is_completed ) { ?>
			                  	<?php echo esc_html( $learndash_enrolled_text ); ?>
			                <?php } elseif($has_access && $is_completed ) { ?>
			                	<?php echo esc_html( $learndash_completed_text ); ?>
			                <?php } else { ?>
			                	<a href="<?php the_permalink(); ?>"><?php echo esc_html( $learndash_enroll_now_text ); ?></a>
			                <?php } ?>

			                <?php } else { ?>
			                   <a href="<?php the_permalink(); ?>"><span class="ld-enroll-btn-icon"><i class="flaticon-right-arrow-2"></i></span></a>
			                <?php } ?>
		                </div>
					 <?php endif ?>
					 
				</div>
			</div>

		</div>
	</article>
<?php endif ?>
<?php } //if class exist learndash ?>