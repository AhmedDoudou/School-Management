<?php
/**
 * The header for Gostudy theme
 *
 * This is the template that displays all of the <head> section and everything up until <main>
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package gostudy
 * @since 1.0.0
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <?php
    wp_head();
    ?>
</head>

<body <?php body_class(); ?>>
    <?php
    wp_body_open();

    do_action('gostudy/preloader');

    do_action('gostudy/elementor_pro/header');

    if (apply_filters('gostudy/header/enable', true)) {
        get_template_part('templates/header/section', 'header');
    }

    $page_title = apply_filters('gostudy/page_title/enable', true);
    if (isset($page_title['page_title_switch']) && $page_title['page_title_switch'] !== 'off') {

        if(function_exists('tutor') && is_singular( 'courses' )) {  
            // Breadcrumb only
            get_template_part('templates/header/section', 'page_bradcrumbs');
            // defaults
         } 
        if(class_exists('LearnPress') && is_singular( 'lp_course' )) {  
            // Hide header for LearnPress
         } 
        // if(class_exists('SFWD_LMS') && is_singular( 'sfwd-courses' )) {  
        //     // Hide header for LearnDash
        //  } 
         else {
            get_template_part('templates/header/section', 'page_title'); 
        }

    }

    ?>
    <main id="main" class="site-main">
