<?php
namespace AweBooking\Shortcodes;

use AweBooking\AweBooking;
use AweBooking\Concierge;
use AweBooking\Hotel\Room_Type;
use AweBooking\Booking\Request;
use AweBooking\Support\Template;
use AweBooking\Factory;

class Shortcode_Cart {

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
		$atts = shortcode_atts( array(), $atts, 'awebooking_cart' );

		Template::get_template( 'cart.php' );
	}
}
