<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $rt_template_header;
$rt_template_header = 'bottom';


if (!empty($this->header_page_select_id)) {
	
	if ( did_action( 'elementor/loaded' ) ) {
		echo \Elementor\Plugin::$instance->frontend->get_builder_content( $this->header_page_select_id );
	}
}
