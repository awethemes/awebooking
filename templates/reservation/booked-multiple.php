<?php
/**
 * This template show the booked room items.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/reservation/booked-multiple.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

dump( $room_stays );

?>

<?php if ( ! abrs_is_checkout_page() ) : ?>

	<a href="<?php echo esc_url( abrs_get_checkout_url() ); ?>" class="button button--block-checkout"><?php esc_html_e( 'Checkout', 'awebooking' ); ?></a>

<?php endif; ?>
