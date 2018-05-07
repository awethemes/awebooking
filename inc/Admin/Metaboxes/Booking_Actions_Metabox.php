<?php
namespace AweBooking\Admin\Metaboxes;

use Awethemes\Http\Request;
use AweBooking\Model\Booking;
use AweBooking\Email\Mailer;
use AweBooking\Email\Templates\Invoice;

class Booking_Actions_Metabox {
	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		global $the_booking;

		if ( is_null( $the_booking ) ) {
			$the_booking = abrs_get_booking( $post );
		}

		$booking_actions = apply_filters( 'awebooking/admin_booking_actions', [
			'send_booking_details'       => esc_html__( 'Email invoice to customer', 'awebooking' ),
			'send_booking_details_admin' => esc_html__( 'Resend new booking notification (admin)', 'awebooking' ),
		]);

		include trailingslashit( __DIR__ ) . 'views/html-booking-action.php';
	}

	/**
	 * Handle save the the metabox.
	 *
	 * @param \WP_Post                $post    The WP_Post object instance.
	 * @param \Awethemes\Http\Request $request The HTTP Request.
	 */
	public function save( $post, Request $request ) {
		$action = sanitize_text_field( $request['awebooking_action'] );

		// Nothing to work.
		if ( empty( $action ) ) {
			return;
		}

		// Resolve the booking object.
		$the_booking = abrs_get_booking( $post );

		switch ( $action ) {
			case 'send_booking_details':
				$sended = Mailer::to( 'anhskohbo@gmail.com' )->send( new Invoice );

				if ( $sended ) {
					abrs_add_booking_note( $the_booking, esc_html__( 'Invoice manually sent to customer.', 'awebooking' ), false, true );
				}
				break;

			case 'send_booking_details_admin':
				abrs_mailer( 'new_booking' )->build( $the_booking )->send();
				break;

			default:
				do_action( 'awebooking/booking_action_' . sanitize_title( $action ), $the_booking );
				break;
		}
	}
}
