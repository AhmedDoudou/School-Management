<?php
function gostudy_let_to_num( $size ) {
	$l   = substr( $size, -1 );
	$ret = substr( $size, 0, -1 );
	switch ( strtoupper( $l ) ) {
		case 'P':
		$ret *= 1024;
		case 'T':
		$ret *= 1024;
		case 'G':
		$ret *= 1024;
		case 'M':
		$ret *= 1024;
		case 'K':
		$ret *= 1024;
	}
	return $ret;
}
$ssl_check = 'https' === substr( get_home_url('/'), 0, 5 );
$green_mark = '<mark class="green"><span class="dashicons dashicons-yes"></span></mark>';

$gostudytheme = wp_get_theme();

$plugins_counts = (array) get_option( 'active_plugins', array() );

if ( is_multisite() ) {
	$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
	$plugins_counts            = array_merge( $plugins_counts, $network_activated_plugins );
}
?>

	<h2 class="nav-tab-wrapper">
		<?php

		printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=gostudy-admin-menu' ), esc_html__( 'Welcome', 'gostudy' ) );

		printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=gostudy-theme-active' ), esc_html__( 'Activate Theme', 'gostudy' ) );

		printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=rt-theme-options-panel' ), esc_html__( 'Theme Options', 'gostudy' ) );

		if (class_exists('OCDI_Plugin')):
			printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'themes.php?page=pt-one-click-demo-import' ), esc_html__( 'Demo Import', 'gostudy' ) );
		endif;

        // printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=gostudy-theme-plugins' ), esc_html__( 'Theme Plugins', 'gostudy' ) );
		
        printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=gostudy-requirements' ), esc_html__( 'Requirements', 'gostudy' ) );
		
		printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=gostudy-help-center' ), esc_html__( 'Help Center', 'gostudy' ) );

		?>
	</h2>
	
	

		<div class="gostudy-getting-started">
				<div class="gostudy-getting-started__box">

					<div class="gostudy-getting-started__content">
						<div class="gostudy-getting-started__content--narrow">
							<h2><?php echo __( 'Welcome to Gostudy', 'gostudy' ); ?></h2>
							<p><?php echo __( 'Just complete the steps below and you will be able to use all functionalities of Gostudy theme by RaisTheme:', 'gostudy' ); ?></p>
						</div>

	

						<div class="gostudy-getting-started__actions gostudy-getting-started__content--narrow">
	
							<a href="<?php echo admin_url( 'admin.php?page=gostudy-setup' ); ?>" class="button-primary button-large button-next"><?php echo __( 'Getting Started', 'gostudy' ); ?></a>
						</div>
					</div>
				</div>
			</div>


	


