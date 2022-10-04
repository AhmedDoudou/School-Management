<?php

/**
 * Course loop price
 */

// Render
$course_id     = get_the_ID();
$default_price = apply_filters('tutor-loop-default-price', esc_html__('Free', 'gostudy'));
$price_html    = '<span class="price"> ' . $default_price . '</span>';
if (tutor_utils()->is_course_purchasable()) {

    $product_id = tutor_utils()->get_course_product_id($course_id);
    $product    = wc_get_product($product_id);

    if ($product) {
        $price_html = '<span class="price"> ' . $product->get_price_html() . ' </span>';
    }
}

 echo wp_kses( $price_html, 'gostudy-default' );

