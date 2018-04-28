<?php
namespace AweBooking\Email\Templates;

use AweBooking\Email\Mailable;

class Invoice extends Mailable {
	/**
	 * Email template ID.
	 *
	 * @var string
	 */
	public $id = 'invoice';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->title = esc_html__( 'Invoice', 'awebooking' );
		$this->description = esc_html__( 'Sent to customers containing their booking information and payment links.', 'awebooking' );

		parent::__construct();
	}

	/**
	 * Build the message.
	 *
	 * @return string
	 */
	protected function build() {
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
