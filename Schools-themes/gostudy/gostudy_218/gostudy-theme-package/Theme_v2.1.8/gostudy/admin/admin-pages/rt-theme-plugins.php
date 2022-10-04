
<?php


if (!class_exists('TGMPA_List_Table')) {
    return;
}

$plugin_table = new TGMPA_List_Table;

wp_clean_plugins_cache(false);

?>
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
    
    <div class="gostudy-system-stats">
        
        <h3><?php esc_html_e( 'Active Plugins (', 'gostudy' ); ?><?php echo esc_attr(count( $plugins_counts )) . ')' ?></h3>
        <table class="system-status-table">
            <tbody>
                <?php
                foreach ( $plugins_counts as $plugin ) {
                    
                    $plugin_info    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
                    $dirname        = dirname( $plugin );
                    $version_string = '';
                    $network_string = '';
                    
                    if ( ! empty( $plugin_info['Name'] ) ) {
                        
                        // Link the plugin name to the plugin url if available.
                        $plugin_name = esc_html( $plugin_info['Name'] );
                        
                        if ( ! empty( $plugin_info['PluginURI'] ) ) {
                            $plugin_name = '<a href="' . esc_url( $plugin_info['PluginURI'] ) . '" target="_blank">' . $plugin_name . '</a>';
                        }
                        
                        ?>
                        <tr>
                            <?php
                            $allowed_html = [
                                'a'      => [
                                    'href'  => [],
                                    'title' => [],
                                ],
                                'br'     => [],
                                'em'     => [],
                                'strong' => [],
                            ];
                            ?>
                            <td><?php echo wp_kses($plugin_name,$allowed_html); ?></td>
                            <td><?php echo sprintf( 'by %s', $plugin_info['Author'] ) . ' &ndash; ' . esc_html( $plugin_info['Version'] ) . $version_string . $network_string; ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

<div class="wgl-tgmpa_dashboard tgmpa wrap">

    <?php $plugin_table->prepare_items(); ?>

    <?php $plugin_table->views(); ?>

    <form id="tgmpa-plugins" action="" method="post">
        <input type="hidden" name="tgmpa-page" value="tgmpa-install-plugins" />
        <input type="hidden" name="plugin_status" value="<?php echo esc_attr( $plugin_table->view_context ); ?>" />
        <?php $plugin_table->display(); ?>
    </form>
</div>


