<?php
namespace AweBooking\Shortcodes;

use AweBooking\Concierge;
use AweBooking\Hotel\Room_Type;
use AweBooking\Booking\Request;
use AweBooking\Support\Template;
use AweBooking\Factory;

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
		if ( empty( $_REQUEST['room-type'] ) ) {
			return;
		}

		try {
			$room_type = new Room_Type( intval( $_REQUEST['room-type'] ) );
			$booking_request = Factory::create_booking_request();
			$booking_request->set_request( 'room-type', $room_type->get_id() );
			$availability = Concierge::check_room_type_availability( $room_type, $booking_request );

			Template::get_template( 'booking.php', [
				'availability' => $availability,
				'room_type' => $room_type,
			] );

		} catch ( \Exception $e ) {
			echo $e->getMessage();
		}
	}
}
