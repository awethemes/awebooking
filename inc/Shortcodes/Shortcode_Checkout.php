<?php
namespace AweBooking\Shortcodes;

use Exception;
use AweBooking\Concierge;
use AweBooking\Hotel\Room_Type;
use AweBooking\Support\Template;
use AweBooking\Booking\Request;

class Shortcode_Checkout {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		return Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {
		$atts = shortcode_atts( array(), $atts, 'awebooking_checkout' );
		// TODO:

		if ( isset( $_GET['step'] ) && $_GET['step'] === 'cancelled' ) {
			Template::get_template( 'cancelled.php' );
			return;
		}

		if ( isset( $_GET['step'] ) && $_GET['step'] === 'complete' && ! empty( $_COOKIE['awebooking-booking-id'] ) ) {
			Template::get_template( 'complete.php' );
			return;
		}

		$cart = awebooking( 'cart' );
		$cart_collection = $cart->get_contents();

		if ( 0 === count( $cart_collection ) ) {
			return;
		}

		Template::get_template( 'checkout.php' );
	}
}
