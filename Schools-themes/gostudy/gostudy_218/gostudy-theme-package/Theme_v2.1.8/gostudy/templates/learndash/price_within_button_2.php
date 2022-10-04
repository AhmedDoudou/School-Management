<?php


$learndash_free_text = Gostudy_Theme_Helper::get_mb_option('learndash_free_text');
$learndash_enrolled_text = Gostudy_Theme_Helper::get_mb_option('learndash_enrolled_text');
$learndash_completed_text = Gostudy_Theme_Helper::get_mb_option('learndash_completed_text');


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

    // Show price
    if ( $price) :
         echo '<span class="price">', esc_html($price), '</span>';
    endif;

?>

