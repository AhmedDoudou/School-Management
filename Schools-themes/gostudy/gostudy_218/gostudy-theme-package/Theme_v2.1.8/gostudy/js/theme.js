"use strict";

is_visible_init();
gostudy_slick_navigation_init();

jQuery(document).ready(function($) {
    gostudy_sticky_init();
    gostudy_search_init();
    gostudy_side_panel_init();
    gostudy_mobile_header();
    gostudy_woocommerce_helper();
    gostudy_woocommerce_login_in();
    gostudy_init_timeline_appear();
    gostudy_accordion_init();
    gostudy_services_accordion_init();
    gostudy_progress_bars_init();
    gostudy_carousel_slick();
    gostudy_image_comparison();
    gostudy_counter_init();
    gostudy_countdown_init();
    gostudy_img_layers();
    gostudy_page_title_parallax();
    gostudy_extended_parallax();
    gostudy_portfolio_parallax();
    gostudy_message_anim_init();
    gostudy_scroll_up();
    gostudy_link_scroll();
    gostudy_skrollr_init();
    gostudy_sticky_sidebar();
    gostudy_videobox_init();
    gostudy_parallax_video();
    gostudy_tabs_init();
    gostudy_circuit_service();
    gostudy_select_wrap();
    jQuery( '.rt_module_title .carousel_arrows' ).gostudy_slick_navigation();
    jQuery( '.rt-filter_wrapper .carousel_arrows' ).gostudy_slick_navigation();
    jQuery( '.rt-products > .carousel_arrows' ).gostudy_slick_navigation();
    jQuery( '.gostudy_module_custom_image_cats > .carousel_arrows' ).gostudy_slick_navigation();
    gostudy_scroll_animation();
    gostudy_woocommerce_mini_cart();
    gostudy_text_background();
    gostudy_dynamic_styles();
    gostudy_learnpress_helper();
});

jQuery(window).load(function () {
    gostudy_images_gallery();
    gostudy_isotope();
    gostudy_blog_masonry_init();
    setTimeout(function(){
        jQuery('#preloader-wrapper').fadeOut();
    },1100);

    gostudy_particles_custom();
    gostudy_particles_image_custom();
    gostudy_menu_lavalamp();
    jQuery(".rt-currency-stripe_scrolling").each(function(){
        jQuery(this).simplemarquee({
            speed: 40,
            space: 0,
            handleHover: true,
            handleResize: true
        });
    })
});






