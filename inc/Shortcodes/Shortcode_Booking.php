<?php
namespace AweBooking\Shortcodes;

use AweBooking\Concierge;
use AweBooking\Hotel\Room_Type;
use AweBooking\Booking\Request;
use AweBooking\Support\Template;
use AweBooking\Factory;
use AweBooking\Support\Period;

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

		if ( isset( $_REQUEST['booking-action'] ) && ( 'edit' === $_REQUEST['booking-action'] ) && isset( $_REQUEST['rid'] ) ) {
			static::get_output_edit_booking();
		} else {
			static::get_output_add_booking();
		}
	}

	public static function get_output_add_booking() {
		if ( empty( $_REQUEST['room-type'] ) ) {
			return;
		}

		try {
			$room_type = new Room_Type( intval( $_REQUEST['room-type'] ) );
			$booking_request = Factory::create_booking_request();
			$booking_request->set_request( 'room-type', $room_type->get_id() );
			$availability = Concierge::check_room_type_availability( $room_type, $booking_request );

			Template::get_template( 'booking.php', compact( 'availability', 'room_type' ) );
		} catch ( \Exception $e ) {
			echo $e->getMessage();
		}
	}

	public static function get_output_edit_booking() {
		try {
			$row_id  = sanitize_text_field( $_REQUEST['rid'] );
			$cart    = awebooking( 'cart' );
			$cart_item = $cart->get( $row_id );
			$room_type = $cart_item->model();

			$period = new Period( $cart_item->options['check_in'], $cart_item->options['check_out'], false );
			$booking_request = new Request( $period, [
				'room-type' => $cart_item->model()->get_id(),
				'adults'    => $cart_item->options['adults'],
				'children'  => $cart_item->options['children'],
				'extra_services' => $cart_item->options['extra_services'],
			] );

			$booking_request->set_request( 'extra_services', $cart_item->options['extra_services'] );
			$availability = Concierge::check_room_type_availability( $room_type, $booking_request );

			if ( $availability->unavailable() ) {
				$cart->remove( $row_id );
				$check_availability_link = get_permalink( absint( awebooking_option( 'page_check_availability' ) ) );
				$check_availability_link = add_query_arg( [
					'start-date' => sanitize_text_field( $cart_item->options['start-date'] ),
					'end-date'   => sanitize_text_field( $cart_item->options['end-date'] ),
					'adults'     => absint( $cart_item->options['adults'] ),
					'children'   => absint( $cart_item->options['children'] ),
				], $check_availability_link );

				$message = sprintf( esc_html__( '%s has been removed from your booking. Period dates are invalid for the room type.' ), esc_html( $room_type->get_title() ) );
				$flash_message->success( $message );
				wp_safe_redirect( $check_availability_link , 302 );
				exit;
			}

			Template::get_template( 'booking-edit.php', compact( 'cart_item', 'booking_request' ) );

		} catch ( \Exception $e ) {
			echo $e->getMessage();
		}
	}
}
