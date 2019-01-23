<?php

namespace AweBooking\Admin\Metaboxes;

use AweBooking\Constants;
use WPLibs\Http\Request;
use AweBooking\Admin\Metabox;

class Booking_Actions_Metabox extends Metabox {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id       = 'awebooking-booking-actions';
		$this->title    = esc_html__( 'Actions', 'awebooking' );
		$this->screen   = Constants::BOOKING;
		$this->context  = 'side';
		$this->priority = 'high';
	}

	/**
	 * Output the metabox.
	 *
	 * @param \WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		global $the_booking;

		if ( is_null( $the_booking ) ) {
			$the_booking = abrs_get_booking( $post );
		}

		$booking_actions = apply_filters( 'abrs_admin_booking_actions', [
			'send_booking_details'       => esc_html__( 'Email invoice to customer', 'awebooking' ),
			'send_booking_details_admin' => esc_html__( 'Resend new booking notification (admin)', 'awebooking' ),
		]);

		$checkout_scheduled = wp_next_scheduled( 'abrs_schedule_update_checkout_status', [ $the_booking->get_id() ] );

		include trailingslashit( __DIR__ ) . 'views/html-booking-action.php';
	}

	/**
	 * Handle save the the metabox.
	 *
	 * @param \WP_Post                $post    The WP_Post object instance.
	 * @param \WPLibs\Http\Request $request The HTTP Request.
	 */
	public function save( $post, Request $request ) {
		$action = sanitize_text_field( $request->awebooking_action );
		if ( empty( $action ) ) {
			return;
		}

		// Resolve the booking object.
		$the_booking = abrs_get_booking( $post );

		switch ( $action ) {
			case 'send_booking_details':
				abrs_mailer( 'invoice' )->build( $the_booking )->send();
				break;

			case 'send_booking_details_admin':
				abrs_mailer()->send_new_booking( $the_booking );
				break;

			default:
				do_action( 'abrs_booking_action_' . sanitize_title( $action ), $the_booking );
				break;
		}
	}
}
