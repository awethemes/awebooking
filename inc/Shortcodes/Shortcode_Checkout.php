<?php
namespace AweBooking\Shortcodes;

use Exception;
use AweBooking\AweBooking;
use AweBooking\BAT\Factory;
use AweBooking\BAT\Session_Booking_Request;
use AweBooking\Support\Formatting;
use AweBooking\Room_Type;
use AweBooking\Support\Mail;
use AweBooking\Mails\Booking_Created;
use AweBooking\Support\Date_Period;
use AweBooking\Support\Template;
use AweBooking\Service;
use AweBooking\Pricing\Calculator\Service_Calculator;
use AweBooking\Pricing\Price;

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

			$extra_services = $availability->get_request()->get_request( 'extra_services' );

			$extra_services_infos = [];

			foreach ( $extra_services as $key => $id ) {
				$extra_service = new Service( $id );
				$cal = new Service_Calculator( $extra_service, $booking_request );
				$extra_service_info['name'] = $extra_service->get_name();
				$extra_service_info['label'] = $extra_service->get_price_label( $booking_request );
				$a = $cal->handle( $availability->get_price() );
				$extra_service_info['price_calculator'] = $a->subtract( $availability->get_price() );
				$extra_services_infos[] = $extra_service_info;
			}

			$extra_services_name = [];

			foreach ( $extra_services as $key => $id ) {
				$term = get_term( $id, AweBooking::HOTEL_EXTRA_SERVICE );
				$extra_services_name[] = $term->name;
			}

			Template::get_template( 'checkout.php', array(
				'extra_services_name' => $extra_services_name,
				'extra_services_infos' => $extra_services_infos,
				'extra_services' => $extra_services,
				'availability' => $availability,
				'room_type' => $room_type,
			));
		} catch ( \Exception $e ) {
			echo $message_error = $e->getMessage();
		}
	}
}
