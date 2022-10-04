<?php

/**
 * Course loop price
 */

// Render
echo '<span class="course-price">';

$course_id     = get_the_ID();
$default_price = apply_filters('tutor-loop-default-price', esc_html__('Free', 'gostudy'));
$price_html    = '<div class="price"> ' . $default_price . '</div>';
if (tutor_utils()->is_course_purchasable()) {

    $product_id = tutor_utils()->get_course_product_id($course_id);
    $product    = wc_get_product($product_id);

    if ($product) {
        $price_html = '<div class="price"> ' . $product->get_price_html() . ' </div>';
    }
}

 echo wp_kses( $price_html, 'gostudy-default' );

echo '</span>';
