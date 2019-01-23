<?php

namespace AweBooking\Email\Templates;

use AweBooking\Email\Booking_Mail;

class Reserved extends Booking_Mail {
	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->id             = 'reserved';
		$this->title          = esc_html__( 'Reserved', 'awebooking' );
		$this->description    = esc_html__( 'This is a booking notification sent to customers containing booking details after a booking is reserved.', 'awebooking' );
		$this->customer_email = true;
		$this->placeholders   = [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_subject() {
		return esc_html__( 'Your {site_title} booking receipt from {date_created}', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_content() {
		return 'Your booking is reserved until we confirm payment has been received. Your booking details are shown below for your reference:';
	}
}
