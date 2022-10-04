<?php
/**
 * Show error messages
 *
 * This template is overridden by RaisTheme team.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $messages ) {
	return;
}

?>

<div class="gostudy_module_message_box type_error closable">
	<div class="message_icon_wrap" role="alert"><i class="message_icon"></i></div>
	<div class="message_content">
		<div class="message_text">
		<ul class="woocommerce-error" role="alert">
			<?php foreach ( $messages as $message ) if ($message) : ?>
				<li<?php echo wc_get_notice_data_attr( $message ); ?>>
					<?php echo wc_kses_notice( $message ); ?>
				</li>
			<?php endif; ?>
		</ul>
		</div>
	</div>
	<span class="message_close_button"></span>
</div>
