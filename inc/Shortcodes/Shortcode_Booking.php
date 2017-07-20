<?php
namespace AweBooking\Shortcodes;

use AweBooking\Room_Type;
use AweBooking\BAT\Factory;
use AweBooking\Support\Template;
use AweBooking\Support\Formatting;
use AweBooking\BAT\Session_Booking_Request;

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
			$booking_request = new Session_Booking_Request;
			$room_type = new Room_Type( $booking_request->get_request( 'room-type' ) );

			$availability = awebooking( 'concierge' )->check_room_type_availability( $room_type, $booking_request );

			Template::get_template( 'booking.php', array( 'availability' => $availability, 'room_type' => $room_type ) );

		} catch ( \Exception $e ) {
			echo $e->getMessage();
		}
	}
}
