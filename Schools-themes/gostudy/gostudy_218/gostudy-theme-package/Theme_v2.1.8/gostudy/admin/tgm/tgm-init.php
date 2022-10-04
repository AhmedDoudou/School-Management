<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.5.2
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 */
get_template_part('admin/tgm/class-tgm-plugin-activation');

add_action( 'tgmpa_register', 'gostudy_register_required_plugins' );
          
function gostudy_register_required_plugins() {

	$plugins = array(
	    
	    array(
            'name'      => esc_html__('Envato Market', 'gostudy'),
            'slug'      => 'envato-market',
            'source'    => 'https://envato.github.io/wp-envato-market/dist/envato-market.zip',
            'required'  => false,
		),
		array(
			'name'      => esc_html__('Elementor','gostudy'),
			'slug'      => 'elementor',
			'required'  => true,
		),
		array(
            'name'      => esc_html__('Gostudy Core', 'gostudy'),
            'slug'      => 'gostudy-core',
			'source'    => get_template_directory() . '/admin/tgm/plugins/gostudy-core.zip',
            'required'  => true,
            'version'   => '2.1.8',
		),	
        array(
            'name'    => esc_html__( 'Revolution Slider', 'gostudy' ),
            'slug'    => 'revslider',
            'source'  => 'https://raistheme.com/envato/api/revslider_6_5_4.zip',
            'version' => '6.5.4',
        ),
        array(
            'name'     => esc_html__('WooCommerce', 'gostudy'),
            'slug'     => 'woocommerce',
            'required' => false
        ),       
        array(
            'name'     => esc_html__('Tutor LMS', 'gostudy'),
            'slug'     => 'tutor',
            'required' => false,
        ),  
        // array(
        //     'name'      => esc_html__('Tutor LMS', 'gostudy'),
        //     'slug'      => 'tutor',
        //     'source'    => get_template_directory() . '/admin/tgm/plugins/tutor.zip',
        //     'required'  => false,
        //     'version'   => '1.9.15',
        // ),    
        array(
            'name'     => esc_html__('LearnPress LMS', 'gostudy'),
            'slug'     => 'learnpress',
            'required' => false,
        ),
        array(
            'name'      => esc_html__('Contact Form 7','gostudy'),
            'slug'      => 'contact-form-7',
            'required'  => false,
        ),
        array(
            'name'      => esc_html__('One Click Demo Import', 'gostudy'),
            'slug'      => 'one-click-demo-import',
            'required'  => false,
        )

);
	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'gostudy-required-plugins', 			// Menu slug.
		'parent_slug'  => 'admin.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}

function gostudy_plugins_menu_args($args) {
    $args['parent_slug'] = 'gostudy-admin-menu';
    return $args;
}

add_filter( 'tgmpa_admin_menu_args', 'gostudy_plugins_menu_args' );