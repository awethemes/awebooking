<?php

namespace AweBooking\Email\Templates;

use AweBooking\Email\Booking_Mail;

class Processing extends Booking_Mail {
	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->id             = 'processing';
		$this->title          = esc_html__( 'Processing', 'awebooking' );
		$this->description    = esc_html__( 'This is a booking notification sent to customers containing booking details after payment.', 'awebooking' );
		$this->customer_email = true;
		$this->placeholders   = [];
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepare_data( $booking ) {
		parent::prepare_data( $booking );
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
		return 'Your booking has been received and is now being processed. Your booking details are shown below for your reference:';
	}
}
