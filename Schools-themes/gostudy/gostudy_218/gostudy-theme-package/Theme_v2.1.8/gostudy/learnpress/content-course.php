<?php
/**
 * Template for displaying course content within the loop.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/content-course.php
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$learnpress_archive_layout = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_layout');
$lp_archive_hide_enroll_btn = Gostudy_Theme_Helper::get_mb_option('lp_archive_hide_enroll_btn');
$lp_archive_enroll_btn_switch = Gostudy_Theme_Helper::get_mb_option('lp_archive_enroll_btn_switch');
$lp_see_more_text = Gostudy_Theme_Helper::get_mb_option('lp_see_more_text');
$learnpress_archive_hide_media = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_hide_media');
$learnpress_archive_hide_categories = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_hide_categories');
$learnpress_archive_hide_price = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_hide_price');
$learnpress_archive_hide_title = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_hide_title');
$learnpress_archive_hide_instructor = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_hide_instructor');
$learnpress_archive_hide_students = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_hide_students');
$learnpress_archive_hide_lessons = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_hide_lessons');
$learnpress_archive_hide_reviews = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_hide_reviews');
$lp_archive_hide_enroll_btn = Gostudy_Theme_Helper::get_mb_option('lp_archive_hide_enroll_btn');
$lp_archive_enroll_btn_switch = Gostudy_Theme_Helper::get_mb_option('lp_archive_enroll_btn_switch');
$learnpress_archive_hide_excerpt_content = Gostudy_Theme_Helper::get_mb_option('learnpress_archive_hide_excerpt_content');
 if (class_exists('LearnPress')) {
?>
<?php if ($learnpress_archive_layout == '3'): ?>
	<article class="rt-course">
		<div class="course__container">

			<?php if (!$learnpress_archive_hide_media): ?>
				<div class="course__media">

					<?php if ( has_post_thumbnail() ):?>
					  <a class="course__media-link" href="<?php the_permalink(); ?>">
					    <?php the_post_thumbnail('medium');?>
					  </a>
					 <?php endif; ?>
					 <?php if (!$learnpress_archive_hide_categories): ?>
						<div class="course__categories ">
							<?php echo get_the_term_list(get_the_ID(), 'course_category'); ?>
						</div>
					 <?php endif; ?>
				</div>
			<?php endif ?>

			<div class="course__content">
				<div class="course__content--info">

					<?php if (!$learnpress_archive_hide_title): ?>
						<?php         
							ob_start();
							learn_press_courses_loop_item_instructor();
							$author_name = ob_get_clean();
						?>
					<?php endif; ?>

					<?php if (!$learnpress_archive_hide_instructor): ?>
				        <div class="rt-course-author-name">
				            <?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
				            <?php echo get_the_author(); ?>
				        </div>
					<?php endif; ?>
					<?php if (!$learnpress_archive_hide_title): ?>
					<h4 class="course__title">
						<a href="<?php the_permalink(); ?>" class="course__title-link">
							<?php the_title(); ?>
						</a>
					</h4>
					<?php endif; ?>
					<?php if (!$learnpress_archive_hide_reviews && class_exists('LP_Addon_Course_Review_Preload')): ?>
						<?php 
							// have to add reviews code
				         ?>
			         <?php endif; ?>
				</div>
				<div class="course__content--meta"> 

					<?php if (!$learnpress_archive_hide_students): ?>
							<?php $students = (int)learn_press_get_course()->count_students(); ?>
							<span class="course-students"><i class="flaticon-user-2"></i> <?php echo esc_html($students); ?> </span>
					<?php endif; ?>

					<?php if (!$learnpress_archive_hide_lessons): ?>
						<?php         
					        $course = \LP_Global::course();
					        $lessons = $course->get_items('lp_lesson', false) ? count($course->get_items('lp_lesson', false)) : 0;
					        printf('<span class="course-lessons"><i class="flaticon-book-1"></i> %s </span>', $lessons);
						?>
					<?php endif; ?>

			        <?php get_template_part('templates/learnpress/price_within_button_2'); ?>
				</div>
			</div>

		</div>
	</article>
<?php elseif ($learnpress_archive_layout == '2'): ?>
	<article class="rt-course">
		<div class="course__container">

			<?php if (!$learnpress_archive_hide_media): ?>
				<div class="course__media">

					<?php if ( has_post_thumbnail() ):?>
					  <a class="course__media-link" href="<?php the_permalink(); ?>">
					    <?php the_post_thumbnail('medium');?>
					  </a>
					 <?php endif; ?>
					 <?php if (!$learnpress_archive_hide_categories): ?>
						<div class="course__categories ">
							<?php echo get_the_term_list(get_the_ID(), 'course_category'); ?>
						</div>
					 <?php endif; ?>
				</div>
			<?php endif ?>

			<div class="course__content">
				<div class="course__content--info">

					<?php if (!$learnpress_archive_hide_title): ?>
						<?php         
							ob_start();
							learn_press_courses_loop_item_instructor();
							$author_name = ob_get_clean();
						?>
					<?php endif; ?>

					<?php if (!$learnpress_archive_hide_instructor): ?>
				        <div class="rt-course-author-name">
				            <?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
				            <?php echo get_the_author(); ?>
				        </div>
					<?php endif; ?>
					<?php if (!$learnpress_archive_hide_title): ?>
					<h4 class="course__title">
						<a href="<?php the_permalink(); ?>" class="course__title-link">
							<?php the_title(); ?>
						</a>
					</h4>
					<?php endif; ?>
				</div>
				<div class="course__content--meta"> 

					<?php if (!$learnpress_archive_hide_students): ?>
							<?php $students = (int)learn_press_get_course()->count_students(); ?>
							<span class="course-students"><i class="flaticon-user-2"></i> <?php echo esc_html($students); ?> </span>
					<?php endif; ?>

					<?php if (!$learnpress_archive_hide_lessons): ?>
						<?php         
					        $course = \LP_Global::course();
					        $lessons = $course->get_items('lp_lesson', false) ? count($course->get_items('lp_lesson', false)) : 0;
					        printf('<span class="course-lessons"><i class="flaticon-book-1"></i> %s </span>', $lessons);
						?>
					<?php endif; ?>

					<?php if (!$learnpress_archive_hide_reviews && class_exists('LP_Addon_Course_Review_Preload')): ?>
					<?php 
						$total_reviews = learn_press_get_course_rate_total(get_the_ID());
				        $reviews_title = !empty($total_reviews) ? sprintf(_n('review is submitted', 'reviews are submitted', $total_reviews, 'gostudy'), number_format_i18n($total_reviews)) : esc_html__('No any reviews yet', 'gostudy');
				        printf(
				            '<span class="course-reviews" title="%1$s %2$s"><i class="flaticon-star-1"></i> %1$d </span>',
				            $total_reviews,
				            $reviews_title,
				        );
			         ?>
			         <?php endif; ?>

			        <?php get_template_part('templates/learnpress/price_within_button_2'); ?>
				</div>
			</div>

		</div>
	</article>
<?php else : ?>	
	<article class="rt-course">
		<div class="course__container">

			<?php if (!$learnpress_archive_hide_media): ?>
				<div class="course__media">

					<?php if ( has_post_thumbnail() ):?>
					  <a class="course__media-link" href="<?php the_permalink(); ?>">
					    <?php the_post_thumbnail('medium');?>
					  </a>
					 <?php endif; ?>
					 <?php if (!$learnpress_archive_hide_categories): ?>
						<div class="course__categories ">
							<?php echo get_the_term_list(get_the_ID(), 'course_category'); ?>
						</div>
					 <?php endif; ?>
				</div>
			<?php endif ?>

			<div class="course__content">
				<div class="course__content--info">

					<?php if (!$learnpress_archive_hide_price): ?>
						<?php get_template_part('templates/learnpress/price_within_button'); ?>
					<?php endif; ?>

					<?php if (!$learnpress_archive_hide_title): ?>
						<?php         
							ob_start();
							learn_press_courses_loop_item_instructor();
							$author_name = ob_get_clean();
						?>
					<?php endif; ?>

					<?php if (!$learnpress_archive_hide_instructor): ?>
				        <div class="rt-course-author-name">
				            <?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
				            <?php echo get_the_author(); ?>
				        </div>
					<?php endif; ?>
					<?php if (!$learnpress_archive_hide_title): ?>
					<h4 class="course__title">
						<a href="<?php the_permalink(); ?>" class="course__title-link">
							<?php the_title(); ?>
						</a>
					</h4>
					<?php endif; ?>
				</div>
				<div class="course__content--meta"> 

					<?php if (!$learnpress_archive_hide_students): ?>
							<?php $students = (int)learn_press_get_course()->count_students(); ?>
							<span class="course-students"><i class="flaticon-user-2"></i> <?php echo esc_html($students); ?> </span>
					<?php endif; ?>

					<?php if (!$learnpress_archive_hide_lessons): ?>
						<?php         
					        $course = \LP_Global::course();
					        $lessons = $course->get_items('lp_lesson', false) ? count($course->get_items('lp_lesson', false)) : 0;
					        printf('<span class="course-lessons"><i class="flaticon-book-1"></i> %s </span>', $lessons);
						?>
					<?php endif; ?>

					<?php if (!$learnpress_archive_hide_reviews && class_exists('LP_Addon_Course_Review_Preload')) : ?>
					<?php 
						$total_reviews = learn_press_get_course_rate_total(get_the_ID());
				        $reviews_title = !empty($total_reviews) ? sprintf(_n('review is submitted', 'reviews are submitted', $total_reviews, 'gostudy'), number_format_i18n($total_reviews)) : esc_html__('No any reviews yet', 'gostudy');
				        printf(
				            '<span class="course-reviews" title="%1$s %2$s"><i class="flaticon-star-1"></i> %1$d </span>',
				            $total_reviews,
				            $reviews_title,
				        );
			         ?>
			         <?php endif; ?>

			         <?php if (!$lp_archive_hide_enroll_btn): ?>	
		                <div class="lp-enroll-btn">
			                <?php if ( $lp_archive_enroll_btn_switch  ) { ?>
			                  <a href="<?php the_permalink(); ?>"><?php echo esc_html( $lp_see_more_text ); ?></span></a>
			                <?php } else { ?>
			                   <a href="<?php the_permalink(); ?>"><span class="lp-enroll-btn-icon"><i class="flaticon-right-arrow-2"></i></span></a>
			                <?php } ?>
		                </div>
					 <?php endif ?>
				</div>
			</div>

		</div>
	</article>
<?php endif ?>
<?php } //if class exist learnpress ?>