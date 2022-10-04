<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$animation = (bool) Gostudy_Theme_Helper::get_option('use_animation_shop');
$animation_style = Gostudy_Theme_Helper::get_option('shop_catalog_animation_style');

$classes = (bool)$animation ? ' appear-animation' : "";
$classes .= (bool)$animation && !empty($animation_style) ? ' anim-'.$animation_style : "";

echo '<ul class="rt-products'.esc_attr($classes).'">';
