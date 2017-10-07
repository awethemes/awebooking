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
use AweBooking\Support\Service_Hooks;
use AweBooking\Cart\Cart;
use AweBooking\Pricing\Price;
use AweBooking\Support\Period;

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
		add_action( 'template_redirect', array( $this, 'handle_edit_booking' ) );
		add_action( 'template_redirect', array( $this, 'handle_remove_booking' ) );
		add_action( 'template_redirect', array( $this, 'checkout_handler' ) );
		add_action( 'template_redirect', array( $this, 'single_check_availability_handler' ) );
	}

	/**
	 * //
	 *
	 * @return void
	 */
	public function handle_add_booking() {
		if ( empty( $_POST['booking-action'] ) || ( 'add' !== $_POST['booking-action'] ) ) {
			return;
		}

		// Alway checking the nonce before process.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'awebooking-add-booking-nonce' ) ) {
			return;
		}

		// Setup somethings.
		$flash_message = awebooking()->make( 'flash_message' );

		// Do validator the input before doing checkout.
		$validator = new Validator( $_POST, apply_filters( 'awebooking/add_booking/validator_rules', [
			'start-date'     => 'required|date',
			'end-date'       => 'required|date',
			'adults'         => 'required|integer|min:1',
			'children'       => 'integer|min:0',
			'extra_services' => 'array',
		]));

		// If have any errors.
		if ( $validator->fails() ) {
			// Loop through errors and set first error found.
			foreach ( $validator->errors() as $errors ) {
				$flash_message->error( $errors[0] );
			}
			return;
		}

		try {
			$room_type = Factory::create_room_from_request();

			$booking_request = Factory::create_booking_request();
			$booking_request->set_request( 'room-type', $room_type->get_id() );

			$availability = Concierge::check_room_type_availability( $room_type, $booking_request );

			if ( $availability->available() ) {
				$services = $room_type->get_services();
				$mandatory_services = collect( $services )->where( 'type', Service::MANDATORY );
				$mandatory_services_ids = $mandatory_services->pluck( 'id' )->toArray();

				// TODO: validate extra services.
				$extra_services = isset( $_POST['awebooking_services'] ) && is_array( $_POST['awebooking_services'] ) ? $_POST['awebooking_services'] : [];
				$extra_services = array_unique( array_merge( $extra_services, $mandatory_services_ids ) );

				// Add to cart.
				$cart_item = awebooking( 'cart' )->add( $room_type, 1, [
					'check_in'       => sanitize_text_field( $_POST['start-date'] ),
					'check_out'      => sanitize_text_field( $_POST['end-date'] ),
					'adults'         => absint( $_POST['adults'] ),
					'children'       => absint( $_POST['children'] ),
					'extra_services' => $extra_services,
				] );

				do_action( 'awebooking/add_booking', $cart_item );

				if ( isset( $_POST['go-to-checkout'] ) ) {
					wp_safe_redirect( get_permalink( absint( awebooking_option( 'page_checkout' ) ) ), 302 );
					exit;
				} else {
					$check_availability_link = get_permalink( absint( awebooking_option( 'page_check_availability' ) ) );
					$check_availability_link = add_query_arg( [
						'start-date' => sanitize_text_field( $_POST['start-date'] ),
						'end-date'   => sanitize_text_field( $_POST['end-date'] ),
						'adults'     => absint( $_POST['adults'] ),
						'children'   => absint( $_POST['children'] ),
					], $check_availability_link );

					$message = sprintf( esc_html__( '%s has been added to your booking.' ), esc_html( $room_type->get_title() ) );
					$flash_message->success( $message );

					wp_safe_redirect( $check_availability_link , 302 );
					exit;
				}
			}
		} catch ( \Exception $e ) {
			// ...
		}
	}

	/**
	 * Handle Edit Booking.
	 *
	 * @return void
	 */
	public function handle_edit_booking() {
		if ( empty( $_REQUEST['booking-action'] ) || ( 'edit' !== $_REQUEST['booking-action'] ) || empty( $_REQUEST['rid'] ) ) {
			return;
		}

		// Alway checking the nonce before process.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'awebooking-edit-booking-nonce' ) ) {
			return;
		}

		try {
			$row_id  = sanitize_text_field( $_REQUEST['rid'] );
			$cart    = awebooking( 'cart' );
			$cart_item = $cart->get( $row_id );
			$room_type = $cart_item->model();

			$period = new Period( $cart_item->options['check_in'], $cart_item->options['check_out'], false );
			$booking_request = new Request( $period, [
				'room-type' => $room_type->get_id(),
				'adults'    => $cart_item->options['adults'],
				'children'  => $cart_item->options['children'],
				'extra_services' => $cart_item->options['extra_services'],
			] );

			$booking_request->set_request( 'extra_services', $cart_item->options['extra_services'] );
			$availability = Concierge::check_room_type_availability( $room_type, $booking_request );

			// Re-check availability.
			if ( $availability->unavailable() ) { // Redirect if unavailable.
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

			// Is availability.
			// Setup somethings.
			$flash_message = awebooking()->make( 'flash_message' );
			// Do validator the input before doing checkout.
			$validator = new Validator( $_POST, apply_filters( 'awebooking/edit_booking/validator_rules', [
				'room-type'      => 'required|integer|min:1',
				'start-date'     => 'required|date',
				'end-date'       => 'required|date',
				'adults'         => 'required|integer|min:1',
				'children'       => 'integer|min:0',
				'extra_services' => 'array',
			]));

			// If have any errors.
			if ( $validator->fails() ) {
				// Loop through errors and set first error found.
				foreach ( $validator->errors() as $errors ) {
					$flash_message->error( $errors[0] );
				}
				return;
			}

			$extra_services = isset( $_POST['awebooking_services'] ) && is_array( $_POST['awebooking_services'] ) ? $_POST['awebooking_services'] : [];

			$cart_item->options['extra_services'] = $extra_services;
			$cart_item->set_price( $room_type->get_buyable_price( $cart_item->options ) );

			do_action( 'awebooking/cart/update_item', $cart_item );
			$cart->store_cart_contents();

			$flash_message->success( sprintf( esc_html__( '%s has been edited.' ), esc_html( $room_type->get_title() ) ) );
		} catch ( \Exception $e ) {
			echo $e->getMessage();
		}
	}

	/**
	 * Handler Remove Booking
	 *
	 * @return void
	 */
	public function handle_remove_booking() {
		global $wp;
		if ( empty( $_REQUEST['booking-action'] ) || ( 'remove' !== $_REQUEST['booking-action'] ) || empty( $_REQUEST['rid'] ) ) {
			return;
		}

		try {
			$row_id = sanitize_text_field( $_REQUEST['rid'] );

			$cart    = awebooking( 'cart' );
			$cart_item = $cart->get( $row_id );

			do_action( 'awebooking/cart/remove_item', $cart_item, $cart );
			$cart->remove( $row_id );

			// Setup somethings.
			$flash_message = awebooking()->make( 'flash_message' );
			$message = sprintf( esc_html__( '%s removed.' ), esc_html( $cart_item->model()->get_title() ) );
			$flash_message->info( $message );

			$referer  = wp_get_referer() ? remove_query_arg( array( 'remove_item' ), add_query_arg( 'removed_booking', '1', wp_get_referer() ) ) : home_url( $wp->request );
			wp_safe_redirect( $referer );
			exit;

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
			'customer_last_name'  => esc_html__( 'Last name', 'awebooking' ),
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
			wp_redirect( $checkout_url );
			exit;
		}

		$cart = awebooking( 'cart' );
		$cart_collection = $cart->get_contents();

		// Return if emty cart.
		if ( $cart_collection->isEmpty() ) {
			return;
		}

		// Set price.
		foreach ( $cart_collection as $row_id => $cart_item ) {
			$room_type = $cart_item->model();
			$cart_item->options['room-type'] = $room_type->get_id();
			$cart_item->set_price( $room_type->get_buyable_price( $cart_item->options ) );
		}

		do_action( 'awebooking/checkout/before_create_booking', $flash_message );

		try {
			// Create new booking.
			$booking = (new Booking)->fill( apply_filters( 'awebooking/store_booking_args', [
				'status'              => Booking::PENDING,
				'customer_id'         => 0,
				'customer_first_name' => sanitize_text_field( $_POST['customer_first_name'] ),
				'customer_last_name'  => sanitize_text_field( $_POST['customer_last_name'] ),
				'customer_email'      => sanitize_text_field( $_POST['customer_email'] ),
				'customer_phone'      => sanitize_text_field( $_POST['customer_phone'] ),
				'customer_company'    => isset( $_POST['customer_company'] ) ? sanitize_text_field( $_POST['customer_company'] ) : '',
				'customer_note'       => isset( $_POST['customer_note'] ) ? sanitize_text_field( $_POST['customer_note'] ) : '',
			] ) );

			// Re-check and set price.
			foreach ( $cart_collection as $row_id => $cart_item ) {
				$room_type = $cart_item->model();
				$period = new Period( $cart_item->options['check_in'], $cart_item->options['check_out'], false );
				$request = new Request( $period, [
					'room-type' => $room_type->get_id(),
					'adults'    => $cart_item->options['adults'],
					'children'  => $cart_item->options['children'],
					'extra_services' => $cart_item->options['extra_services'],
				] );

				$availability = Concierge::check_room_type_availability( $room_type, $request );
				// Take last room in list rooms available.
				$rooms = $availability->get_rooms();
				$the_room = end( $rooms );

				$room_item = (new Line_Item)->fill( [
					'name'      => $availability->get_room_type()->get_title(),
					'room_id'   => $the_room->get_id(),
					'check_in'  => $request->get_check_in()->toDateString(),
					'check_out' => $request->get_check_out()->toDateString(),
					'adults'    => $request->get_adults(),
					'children'  => $request->get_children(),
					'total'     => $cart_item->get_total(),
				] );

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

					$service_item = ( new Service_Item )->fill( [
						'name'       => $service->get_name(),
						'service_id' => $service->get_id(),
						'parent_id'  => $room_item->get_id(),
					] );

					$booking->add_item( $service_item );
				}

				$booking->calculate_totals();
			}

			do_action( 'awebooking/booking_created', $booking );

			// Send mail.
			if ( awebooking_option( 'email_new_enable' ) ) {
				try {
					Mailer::to( $booking->get_customer_email() )->send( new Booking_Created( $booking ) );
					Mailer::to( awebooking( 'config' )->get_admin_notify_emails() )->send( new Admin_Booking_Created( $booking ) );
				} catch ( \Exception $e ) {
					// ...
				}
			}

			do_action( 'awebooking/checkout_completed', $booking );

			// Clear booking request and set booking ID.
			awebooking_setcookie( 'awebooking-booking-id', $booking->get_id(), time() + 60 * 60 * 24 );
			$cart->destroy();

			wp_redirect( add_query_arg( [ 'step' => 'complete' ], $checkout_url ) );
			exit;
		} catch ( \Exception $e ) {
			$flash_message->error( $e->getMessage() );

			wp_redirect( $checkout_url );
			exit;
		} // End try().
	}

	/**
	 * Check availability in single room type.
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

		$validator = new Validator( $_POST, apply_filters( 'awebooking/add_booking/validator_rules', [
			'start-date'     => 'required|date',
			'end-date'       => 'required|date',
			'adults'         => 'required|integer|min:1',
			'children'       => 'integer|min:0',
		]));

		// If have any errors.
		if ( $validator->fails() ) {
			// Loop through errors and set first error found.
			foreach ( $validator->errors() as $errors ) {
				$flash_message->error( $errors[0] );
			}
			return;
		}

		try {
			$room_type = Factory::create_room_from_request( $_POST );
			$booking_request = Factory::create_booking_request( $_POST );
			$availability = Concierge::check_room_type_availability( $room_type, $booking_request );

			if ( $availability->available() ) {
				$default_args = awebooking_get_booking_request_query( array( 'room-type' => $room_type->get_id() ) );
				$booking_url = add_query_arg( array_merge( array( 'booking-action' => 'view' ), (array) $default_args ), awebooking_get_page_permalink( 'booking' ) );
				wp_safe_redirect( $booking_url, 302 );
				exit;
			}

			$flash_message->error( esc_html__( 'No room available', 'awebooking' ) );

			wp_safe_redirect( get_the_permalink( $room_type->get_id() ), 302 );
			exit;
		} catch ( \Exception $e ) {
			$flash_message->error( $e->getMessage() );

			$request_args = awebooking_get_booking_request_query();
			unset( $request_args['end-date'] );

			$link = get_the_permalink( $room_type->get_id() );
			wp_safe_redirect( add_query_arg( $request_args, $link ), 302 );
			exit;
		}
	}
}
