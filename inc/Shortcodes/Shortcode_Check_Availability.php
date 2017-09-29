<?php
namespace AweBooking\Shortcodes;

use AweBooking\Factory;
use AweBooking\Concierge;
use AweBooking\Hotel\Room_Type;
use AweBooking\Support\Template;

class Shortcode_Check_Availability {

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

		$atts = shortcode_atts( array(), $atts, 'awebooking_check_availability' );

		self::check_availability();
	}

	/**
	 * Show the checkout.
	 */
	private static function check_availability() {
		$errors = '';
		$results = [];

		if ( isset( $_REQUEST['start-date'] ) && isset( $_REQUEST['end-date'] ) ) {
			try {
				$booking_request = Factory::create_booking_request();
				$results = Concierge::check_availability( $booking_request );
			} catch ( \InvalidArgumentException $e ) {
				$errors = esc_html__( 'Missing data, please enter the required data.', 'awebooking' );
			} catch ( \LogicException $e ) {
				$errors = esc_html__( 'Period dates are invalid.', 'awebooking' );
			} catch ( \Exception $e ) {
				$errors = esc_html__( 'An error occurred while processing your request.', 'awebooking' );
			}
		}

		Template::get_template( 'check-availability.php', array( 'results' => $results, 'errors' => $errors ) );
	}
}
