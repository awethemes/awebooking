<?php

namespace AweBooking\Email\Templates;

use AweBooking\Email\Booking_Mail;

class Invoice extends Booking_Mail {
	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->id             = 'invoice';
		$this->title          = esc_html__( 'Invoice', 'awebooking' );
		$this->description    = esc_html__( 'Sent to customers containing their booking information and payment links.', 'awebooking' );
		$this->manually       = true;
		$this->customer_email = false;
	}
}
