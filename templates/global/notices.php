<?php
/**
 * AweBooking message template.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/global/message.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! awebooking( 'flash_message' )->has() ) {
	return;
}

foreach ( awebooking( 'flash_message' )->all() as $message ) : ?>

	<div class="awebooking-notice awebooking-notice--<?php echo esc_attr( $message['type'] ); ?>">
		<?php echo esc_html( $message['message'] ); ?>
	</div>

<?php endforeach;

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
