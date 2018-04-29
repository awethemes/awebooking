<?php
namespace AweBooking\Email\Templates;

use AweBooking\Email\Mailable;

class Invoice extends Mailable {
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

	/**
	 * {@inheritdoc}
	 */
	public function get_subject() {
		return $this->format_string( abrs_get_option( 'email_new_subject' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_heading() {}
}
