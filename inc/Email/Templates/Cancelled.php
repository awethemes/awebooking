<?php

namespace AweBooking\Email\Templates;

use AweBooking\Email\Booking_Mail;

class Cancelled extends Booking_Mail {
	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->id             = 'cancelled';
		$this->title          = esc_html__( 'Cancelled', 'awebooking' );
		$this->description    = esc_html__( 'Cancelled emails are sent to customers when bookings have been marked cancelled.', 'awebooking' );
		$this->customer_email = true;
		$this->placeholders   = [
			'{booking_number}' => '',
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
		return esc_html__( 'Cancelled booking #{booking_number}', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_content() {
		return 'Your booking #{booking_number} has been cancelled!';
	}
}
