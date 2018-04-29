<?php
namespace AweBooking\Email\Templates;

use AweBooking\Email\Mailable;

class Customer_Note extends Mailable {
	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->id             = 'customer_note';
		$this->title          = esc_html__( 'Customer note', 'awebooking' );
		$this->description    = esc_html__( 'Sent when you add a note to a booking.', 'awebooking' );
		$this->customer_email = true;
		$this->placeholders   = [
			'{customer_note}' => '',
		];
	}

	/**
	 * Prepare data for sending.
	 *
	 * @param  \AweBooking\Model\Booking $booking The booking instance.
	 * @param  string                    $note    The note to customer.
	 * @return void
	 */
	protected function prepare_data( $booking, $note = '' ) {
		$this->recipient = $booking->get( 'customer_email' );

		$this->placeholders['{customer_note}'] = $note;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_subject() {
		return esc_html__( 'Note added to your {site_title} booking', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_heading() {
		return esc_html__( 'A note has been added to your booking', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_content() {
		return "Hello, a note has just been added to your booking:\n\n{customer_note}";
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_content_plain() {
		return $this->format_string( $this->get_option( 'content' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_content_html() {
		return abrs_get_template_content( 'emails/customer-note.php', [
			'mail'    => $this,
			'content' => $this->format_string( $this->get_option( 'content' ) ),
		]);
	}
}
