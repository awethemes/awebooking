<?php
namespace AweBooking\Shortcodes;

use AweBooking\Concierge;
use AweBooking\Hotel\Room_Type;
use AweBooking\Booking\Request;
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

		try {
			$booking_request = Request::instance();
			$room_type = new Room_Type( $booking_request->get_request( 'room-type' ) );

			$availability = Concierge::check_room_type_availability( $room_type, $booking_request );

			Template::get_template( 'booking.php', array( 'availability' => $availability, 'room_type' => $room_type ) );

		} catch ( \Exception $e ) {
			echo $e->getMessage();
		}
	}
}
