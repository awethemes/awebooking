<?php
namespace AweBooking\Shortcodes;

use Exception;
use AweBooking\Hotel\Room_Type;
use AweBooking\Support\Template;
use AweBooking\Booking\Session_Booking_Request;

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

		self::checkout();

	}

	/**
	 * Show the checkout.
	 */
	private static function checkout() {
		if ( isset( $_GET['step'] ) && $_GET['step'] === 'cancelled' ) {
			Template::get_template( 'cancelled.php' );
			return;
		}

		if ( isset( $_GET['step'] ) && $_GET['step'] === 'complete' && ! empty( $_COOKIE['awebooking-booking-id'] ) ) {
			Template::get_template( 'complete.php' );
			return;
		}

		try {
			$booking_request = new Session_Booking_Request;

			$room_type = new Room_Type( $booking_request->get_request( 'room-type' ) );

			$availability = awebooking( 'concierge' )->check_room_type_availability( $room_type, $booking_request );

			if ( $availability->unavailable() ) {
				return;
			}

			Template::get_template( 'checkout.php', array(
				'availability' => $availability,
				'room_type' => $room_type,
			));
		} catch ( \Exception $e ) {
			echo $message_error = $e->getMessage();
		}
	}
}
