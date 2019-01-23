<?php

namespace AweBooking\Email\Templates;

use AweBooking\Email\Booking_Mail;

class New_Booking extends Booking_Mail {
	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->id             = 'new_booking';
		$this->title          = esc_html__( 'New booking', 'awebooking' );
		$this->description    = esc_html__( 'New booking emails are sent to chosen recipient(s) when a new booking is received.', 'awebooking' );
		$this->customer_email = false;
		$this->placeholders   = [
			'{booking_date}'   => '',
			'{booking_number}' => '',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_subject() {
		return esc_html__( '[{site_title}] New customer booking #{booking_number} - {booking_date}', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_content() {
		return 'You have received a booking from {customer_first_name}. The booking is as follows:';
	}
}
