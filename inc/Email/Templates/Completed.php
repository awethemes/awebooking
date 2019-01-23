<?php

namespace AweBooking\Email\Templates;

use AweBooking\Email\Booking_Mail;

class Completed extends Booking_Mail {
	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->id             = 'completed';
		$this->title          = esc_html__( 'Completed', 'awebooking' );
		$this->description    = esc_html__( 'Booking complete emails are sent to customers when their bookings are marked completed.', 'awebooking' );
		$this->customer_email = true;
		$this->placeholders   = [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_subject() {
		return esc_html__( 'Your {site_title} booking receipt from {date_created} is complete', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_content() {
		return 'Hi there. Your recent booking on {site_title} has been completed. Your booking details are shown below for your reference:';
	}
}
