<?php
/**
 * Template Activate Theme
 *
 * 
 * @package gostudy\core\dashboard
 * @link https://themeforest.net/user/raistheme
 * @author RaisTheme <help.raistheme@gmail.com>
 * @since 1.0.0
 */

?>
<div class="rt-activation-theme_form">
    <div class="container-form">
        <?php
            if(!Gostudy_Theme_Helper::rt_theme_activated()):
            ?>
            <h1 class="rt-title"><?php esc_html_e( 'Activate your Licence', 'gostudy' ); ?></h1>
            <div class="rt-content">
                <p class="rt-content_subtitle">
                    <?php echo sprintf( esc_html__('Welcome and thank you for Choosing %s Theme!', 'gostudy'), esc_html(wp_get_theme()->get('Name')));?>
                    <br/>
                    <?php echo sprintf(esc_html__('The %s theme needs to be activated to enable demo import installation and customer support service.', 'gostudy'), esc_html(wp_get_theme()->get('Name')));?>
                </p>
            </div>

            <form class="form rt-purchase" action="<?php echo esc_url( admin_url( 'admin.php?page=rt-activate-theme-panel' ) ); ?>" method="post">
                <div class="help-description">
                    <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><?php esc_html_e('How to find purchase code?', 'gostudy');?></a>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="user_email"><?php esc_html_e( 'E-mail address', 'gostudy' ); ?></label>
                        <input class="form-control" type="text" placeholder="<?php esc_attr_e( 'E-mail address', 'gostudy' ); ?>" name="user_email" value="<?php echo esc_attr( get_option('admin_email') ); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="purchase_item"><?php esc_html_e( 'Enter Your Purchase Code', 'gostudy' ); ?></label>
                        <input class="form-control" placeholder="<?php esc_attr_e( 'Enter Your Purchase Code', 'gostudy' ); ?>" type="text" name="purchase_item" required>
                    </div>
                </div>


                <?php wp_nonce_field( 'purchase-activation', 'security' ); ?>

                <input type="hidden" name="action" value="purchase_activation">

                <input type="hidden" name="content">
				<input type="hidden" name="js_activation">

                <button type="submit" class="button button-primary activate-license" value="submit">
                    <span class="text-btn"><?php esc_html_e( 'Activate', 'gostudy' ); ?></span>
                    <span class="loading-icon"></span>
                </button>
            </form>

            <?php
            else:
                $js_activation = get_option( 'rt_js_activation' );
				$deactivation_form = !empty($js_activation) ? ' deactivation_form' : '';
				$deactivation_class = !empty($js_activation) ? ' js_deactivate' : '';
            ?>
                <div class="rt-activation-theme_congratulations">
                    <h1 class="rt-title">
                        <span>
                            <?php esc_html_e( 'Thank you!', 'gostudy' ); ?>
                        </span>
                        <br/>
                        <?php esc_html_e( 'Your theme\'s license is activated successfully.', 'gostudy' ); ?>
                    </h1>
                </div>
    			<form class="form rt-deactivate_theme<?php echo esc_attr($deactivation_form);?>" action="" method="post">
    				<div class="form-group hidden_group">
    					<input type="hidden" name="deactivate_theme" value="1" class="form-control">
    				</div>

					<?php
						if(!empty($js_activation)){
						?>
							<input type="hidden" name="js_deactivate_theme" value="1" class="form-control">
						<?php
						}
					?>

					<?php wp_nonce_field( 'purchase-activation', 'security' ); ?>

					<button type="submit" class="button button-primary deactivate_theme-license<?php echo esc_attr($deactivation_class);?>" value="submit">
						<span class="text-btn"><?php esc_html_e( 'Deactivate', 'gostudy' ); ?></span>
						<span class="loading-icon"></span>
					</button>
    			</form>
            <?php
            endif;
        ?>
        <div class="text-desc-info">
            <p class="text-desc-info_license"><?php esc_html_e('1 license  = 1 domain = 1 website', 'gostudy');?></p>
            <p class="text-desc-info_author"><?php esc_html_e('You can always buy more licences for this product:', 'gostudy');?>
                <a href="https://themeforest.net/user/raistheme">ThemeForest RaisTheme</a>
            </p>
        </div>
    </div>
</div>
