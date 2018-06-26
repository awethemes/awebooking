<?php
namespace AweBooking\Email\Templates;

use AweBooking\Email\Mailable;

class Cancelled_Booking extends Mailable {
	/**
	 * The booking instance.
	 *
	 * @var \AweBooking\Model\Booking
	 */
	protected $booking;

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
	 * Trigger send email.
	 *
	 * @param \AweBooking\Model\Booking $booking The booking instance.
	 * @return void
	 */
	public function trigger( $booking ) {
		if ( 'cancelled' !== $booking->get_status() ) {
			return;
		}

		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->build( $booking )->send();
		}
	}

	/**
	 * Prepare data for sending.
	 *
	 * @param  \AweBooking\Model\Booking $booking       The booking instance.
	 * @return void
	 */
	protected function prepare_data( $booking ) {
		$this->booking = $booking;

		if ( 'cancelled' !== $booking->get_status() ) {
			$this->recipient = '';
		}

		$this->placeholders = ( new Booking_Placeholder( $booking, $this ) )->apply( $this->placeholders );
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
		return "The booking #{booking_id} from {customer_first_name} has been cancelled. The booking was as follows:\n\n{contents}\n\n{customer_details}";
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
		return abrs_get_template_content( 'emails/cancelled-booking.php', [
			'email'         => $this,
			'booking'       => $this->booking,
			'content'       => $this->format_string( $this->get_option( 'content' ) ),
		]);
	}
}
