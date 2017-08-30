<?php
namespace AweBooking;

use AweBooking\AweBooking;
use AweBooking\Hotel\Service;
use AweBooking\Hotel\Room_Type;
use AweBooking\Booking\Request;
use AweBooking\Booking\Booking;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Booking\Items\Service_Item;
use AweBooking\Notification\Booking_Created;
use AweBooking\Notification\Admin_Booking_Created;
use AweBooking\Support\Mailer;
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
		add_action( 'template_redirect', array( $this, 'handle_add_booking' ) );
		add_action( 'template_redirect', array( $this, 'checkout_handler' ) );
		add_action( 'template_redirect', array( $this, 'single_check_availability_handler' ) );
	}

	/**
	 * //
	 *
	 * @return void
	 */
	public function handle_add_booking() {
		if ( empty( $_REQUEST['add-booking'] ) ) {
			return;
		}

		try {
			$room_type = Factory::create_room_from_request();
			$booking_request = Factory::create_booking_request();
			$booking_request->set_request( 'room-type', $room_type->get_id() );

			$availability = Concierge::check_room_type_availability( $room_type, $booking_request );

			if ( $availability->available() ) {
				$booking_request->store();

				return wp_redirect( awebooking_get_page_permalink( 'booking' ), 302 );
			}
		} catch ( \Exception $e ) {
			// ...
		}
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
		$checkout_url  = awebooking_get_page_permalink( 'checkout' );

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

		try {
			$request = Request::instance();

			$availability = Concierge::check_room_type_availability(
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
			$booking = (new Booking)->fill(apply_filters( 'awebooking/store_booking_args', [
				'status'              => Booking::PENDING,
				'customer_id'         => 0,
				'customer_first_name' => sanitize_text_field( $_POST['customer_first_name'] ),
				'customer_last_name'  => sanitize_text_field( $_POST['customer_last_name'] ),
				'customer_email'      => sanitize_text_field( $_POST['customer_email'] ),
				'customer_phone'      => sanitize_text_field( $_POST['customer_phone'] ),
				'customer_company'    => isset( $_POST['customer_company'] ) ? sanitize_text_field( $_POST['customer_company'] ) : '',
				'customer_note'       => isset( $_POST['customer_note'] ) ? sanitize_text_field( $_POST['customer_note'] ) : '',
			]));

			$room_item = (new Line_Item)->fill([
				'name'      => $availability->get_room_type()->get_title(),
				'room_id'   => $the_room->get_id(),
				'check_in'  => $request->get_check_in()->toDateString(),
				'check_out' => $request->get_check_out()->toDateString(),
				'adults'    => $request->get_adults(),
				'children'  => $request->get_children(),
				'total'     => $availability->get_price()->get_amount(),
			]);

			$booking->add_item( $room_item );
			$booking->save();

			if ( ! $booking->exists() ) {
				wp_die( esc_html__( 'Something went wrong', 'awebooking' ) );
				return; // Something went wrong.
			}

			foreach ( $request->get_services() as $service_id => $quantity ) {
				$service = new Service( $service_id );
				if ( ! $service->exists() ) {
					continue;
				}

				$service_item = (new Service_Item)->fill([
					'name'       => $service->get_name(),
					'service_id' => $service->get_id(),
					'price'      => $service->get_price()->get_amount(),
					'parent_id'  => $room_item->get_id(),
				]);

				$booking->add_item( $service_item );
			}

			$booking->calculate_totals();

			if ( awebooking_option( 'email_new_enable' ) ) {
				try {
					Mailer::to( $booking->get_customer_email() )->send( new Booking_Created( $booking ) );
					Mailer::to( awebooking( 'config' )->get_admin_notify_emails() )->send( new Admin_Booking_Created( $booking ) );
				} catch ( \Exception $e ) {
					// ...
				}
			}

			do_action( 'awebooking/checkout_completed', $booking, $availability );

			$session = awebooking( 'session' );
			// Clear booking request and set booking ID.
			unset( $session['awebooking_request'] );
			awebooking_setcookie( 'awebooking-booking-id', $booking->get_id(), time() + 60 * 60 * 24 );

			wp_session_commit();

			return wp_redirect( add_query_arg( [ 'step' => 'complete' ], $checkout_url ) );

		} catch ( \Exception $e ) {
			$flash_message->error( $e->getMessage() );
			return wp_redirect( $checkout_url );
		} // End try().
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

			$availability = Concierge::check_room_type_availability( $room_type, $booking_request );

			if ( $availability->available() ) {

				$booking_request->set_request( 'room-type', $room_type->get_id() );
				$booking_request->store();

				return wp_redirect( $availability->get_booking_url(), 302 );
			}

			$flash_message->error( esc_html__( 'No room available', 'awebooking' ) );
			return wp_redirect( get_the_permalink( $room_type->get_id() ), 302 );
		} catch ( \Exception $e ) {
			$flash_message->error( $e->getMessage() );

			$request_args = awebooking_get_booking_request_query();
			unset( $request_args['end-date'] );

			$link = get_the_permalink( $room_type->get_id() );
			return wp_redirect( add_query_arg( $request_args, $link ), 302 );
		}
	}
}
