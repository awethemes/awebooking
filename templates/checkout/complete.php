<?php
/**
 * The Template for complete booking.
 *
 * This template can be overridden by copying it to yourtheme/awebooking/complete.php.
 *
 * @author 		Awethemes
 * @package 	Awethemes/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php if ( isset( $_GET['step'] ) && $_GET['step'] === 'complete' && ! empty( $_COOKIE['awebooking-booking-id'] ) ) : ?>
	<p><?php echo sprintf( esc_html__( 'Thanks for your booking. Your booking ID: #%s', 'awebooking' ), $_COOKIE['awebooking-booking-id'] ); ?></p>
	<?php return; ?>
<?php endif ?>
