<?php
namespace AweBooking;

use Exception;
use AweBooking\AweBooking;

use AweBooking\Support\Date_Period;
use AweBooking\Support\Date_Utils;
use AweBooking\BAT\Booking_Request;
use AweBooking\BAT\Factory;
use AweBooking\Support\Utils;
use AweBooking\BAT\Session_Booking_Request;
use AweBooking\Support\Formatting;
use AweBooking\Room_Type;
use AweBooking\Support\Mail;
use AweBooking\Mails\Booking_Created;

use Skeleton\Support\Validator;
use Skeleton\Container\Service_Hooks;

class Request_Handler extends Service_Hooks {
	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param AweBooking $awebooking AweBooking Container instance.
	 */
	public function init( $awebooking ) {
		add_action( 'template_redirect', array( $this, 'booking_handler' ) );
		add_action( 'template_redirect', array( $this, 'checkout_handler' ) );
		add_action( 'template_redirect', array( $this, 'single_check_availability_handler' ) );
	}

	/**
	 * Handler checkout action.
	 *
	 * @return void
	 */
	public function checkout_handler() {
		if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		// Prevent if not see any checkout action.
		if ( empty( $_POST['awebooking-action'] ) || 'checkout' !== $_POST['awebooking-action'] ) {
			return;
		}

		// Alway checking the nonce before process.
		check_admin_referer( 'awebooking-checkout-nonce' );

		// Setup somethings.
		$flash_message = awebooking()->make( 'flash_message' );
		$checkout_url  = get_the_permalink( absint( abkng_config( 'page_checkout' ) ) );

		// Do validator the input before doing checkout.
		$validator = new Validator( $_POST, apply_filters( 'awebooking/checkout/validator_rules', [
			'customer_first_name' => 'required',
			'customer_last_name'  => 'required',
			'customer_email'      => 'required|email',
			'customer_phone'      => 'required',
		]));

		$validator->labels( apply_filters( 'awebooking/checkout/validator_labels', [
			'customer_first_name' => esc_html__( 'First name', 'awebooking' ),
			'customer_first_name' => esc_html__( 'Last name', 'awebooking' ),
			'customer_email'      => esc_html__( 'Email address', 'awebooking' ),
			'customer_phone'      => esc_html__( 'Phone number', 'awebooking' ),
		]));

		// If have any errors.
		if ( $validator->fails() ) {

			// Loop through errors and set first error found.
			foreach ( $validator->errors() as $errors ) {
				$flash_message->error( $errors[0] );
			}

			// TODO: May be we don't need redirect to back URL,
			// but flash_message have trouble (BUGS) in case not redirect page.
			return wp_redirect( $checkout_url );
		}

		// Call the hotel concierge,
		// He so powerful, he know everything about your hotel, etc...
		// Just simple, he can make your request booking :).
		$concierge = awebooking()->make( 'concierge' );

		try {
			$request = new Session_Booking_Request;

			$availability = $concierge->check_room_type_availability(
				new Room_Type( $request->get_request( 'room-type' ) ),
				$request
			);

			if ( $availability->unavailable() ) {
				$flash_message->error( esc_html__( 'Unavailable', 'awebooking' ) );
				return wp_redirect( $checkout_url );
			}

			// Take last room in list rooms available.
			$rooms = $availability->get_rooms();
			$the_room = end( $rooms );

			// Create new booking.
			$booking = awebooking( 'factory' )->create_booking([
				'customer_id'   => 0,
				'adults'        => $request->get_adults(),
				'children'      => $request->get_children(),
				'check_in'      => $request->get_check_in()->toDateString(),
				'check_out'     => $request->get_check_out()->toDateString(),
				'room_id'       => $the_room->get_id(),
				'availability'  => $availability,
				'customer_note' => isset( $_POST['customer_note'] ) ? sanitize_text_field( $_POST['customer_note'] ) : '',
				'customer_first_name' => sanitize_text_field( $_POST['customer_first_name'] ),
				'customer_last_name'  => sanitize_text_field( $_POST['customer_last_name'] ),
				'customer_email'      => sanitize_text_field( $_POST['customer_email'] ),
				'customer_phone'      => sanitize_text_field( $_POST['customer_phone'] ),
				'customer_company'    => isset( $_POST['customer_company'] ) ? sanitize_text_field( $_POST['customer_company'] ) : '',
			]);

			if ( ! $booking ) {
				wp_die( esc_html__( 'Something went wrong', 'awebooking' ) );
				return; // Something went wrong.
			}

			do_action( 'awebooking/checkout_completed', $booking, $availability );

			// Clear booking request and set booking ID.
			Utils::setcookie( 'awebooking-request', null, time() - 1000 );
			Utils::setcookie( 'awebooking-booking-id', $booking->get_id(), time() + 60 * 60 * 24 );

			return wp_redirect( add_query_arg( [ 'step' => 'complete' ], $checkout_url ) );

		} catch ( Exception $e ) {
			$flash_message->error( $e->getMessage() );
			return wp_redirect( $checkout_url );
		} // End try().
	}

	/**
	 * //
	 *
	 * @return void
	 */
	public function booking_handler() {
		if ( empty( $_REQUEST['add-booking'] ) ) {
			return;
		}

		try {
			$room_type = Factory::create_room_from_request();

			$booking_request = Factory::create_booking_request();
			$booking_request->set_request( 'room-type', $room_type->get_id() );

			$availability = awebooking( 'concierge' )->check_room_type_availability( $room_type, $booking_request );

			Session_Booking_Request::set_instance( $booking_request );

			$default_args = Date_Utils::get_booking_request_query( array( 'room-type' => $room_type->get_id() ) );
			$link = add_query_arg( (array) $default_args, get_the_permalink( intval( abkng_config( 'page_booking' ) ) ) );

			return wp_redirect( $link, 302 );

		} catch ( Exception $e ) {
			// ...
		}
	}

	/**
	 * //
	 *
	 * @return void
	 */
	public function single_check_availability_handler() {
		if ( ! is_room_type() ) {
			return;
		}

		if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		$flash_message = awebooking( 'flash_message' );

		try {
			$room_type = Factory::create_room_from_request( $_POST );
			$booking_request = Factory::create_booking_request( $_POST );

			$availability = awebooking( 'concierge' )->check_room_type_availability( $room_type, $booking_request );

			if ( $availability->available() ) {
				return wp_redirect( Utils::get_booking_url( $availability ), 302 );
			}

			$flash_message->error( esc_html__( 'No room available', 'awebooking' ) );

			$link = get_the_permalink( $room_type->get_id() );
			return wp_redirect( $link, 302 );

		} catch ( \Exception $e ) {
			$flash_message->error( $e->getMessage() );

			$request_args = Utils::get_booking_request_query();
			unset( $request_args['end-date'] );

			$link = get_the_permalink( $room_type->get_id() );
			return wp_redirect( add_query_arg( $request_args, $link ), 302 );
		}
	}
}
