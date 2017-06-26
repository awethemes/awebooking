<?php
namespace AweBooking\Shortcodes;

use AweBooking\BAT\Factory;
use AweBooking\BAT\Session_Booking_Request;
use AweBooking\Support\Formatting;
use AweBooking\Support\Template;

class Shortcode_Booking {

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

		$atts = shortcode_atts( array(), $atts, 'awebooking_booking' );

		self::booking();

	}

	/**
	 * Show the checkout.
	 */
	private static function booking() {
		try {
			$room_type = Factory::create_room_from_request();

			$booking_request = new Session_Booking_Request;
			$booking_request->set_request( 'room-type', $room_type->get_id() );

			$availability = awebooking( 'concierge' )->check_room_type_availability( $room_type, $booking_request );

			Template::get_template( 'booking.php', array( 'availability' => $availability, 'room_type' => $room_type ) );

		} catch ( \Exception $e ) {
			echo $e->getMessage();
		}
	}
}
