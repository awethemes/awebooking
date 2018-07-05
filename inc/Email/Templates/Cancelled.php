<?php
namespace AweBooking\Email\Templates;

use AweBooking\Email\Booking_Mail;

class Cancelled extends Booking_Mail {
	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->id             = 'cancelled';
		$this->title          = esc_html__( 'Cancelled booking', 'awebooking' );
		$this->description    = esc_html__( 'Cancelled booking emails are sent to chosen recipient(s) when bookings have been marked cancelled.', 'awebooking' );
		$this->customer_email = false;
		$this->placeholders   = [
			'{booking_id}' => '',
		];
	}

	/**
	 * Prepare data for sending.
	 *
	 * @param  \AweBooking\Model\Booking $booking       The booking instance.
	 * @return void
	 */
	protected function prepare_data( $booking ) {
		parent::prepare_data( $booking );

		// If the booking is not cancelled, don't sent.
		if ( 'cancelled' !== $booking->get_status() ) {
			$this->recipient = '';
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_subject() {
		return esc_html__( 'Cancelled booking #{booking_id}', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_content() {
		return '[{site_title}] The booking #{booking_id} from {customer_first_name} has been cancelled. The booking was as follows:';
	}
}
