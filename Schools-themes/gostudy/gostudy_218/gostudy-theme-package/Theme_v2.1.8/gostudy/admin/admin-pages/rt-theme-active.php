


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
	
	
	<div class="gostudy-section nav-tab-active" id="activate-theme">

	<?php gostudy_tv_options_page(); ?>

	</div>

