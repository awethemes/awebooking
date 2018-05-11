<?php
namespace AweBooking\Email\Templates;

use AweBooking\Email\Mailable;

class Processing_Booking extends Mailable {
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
		$this->id             = 'processing_booking';
		$this->title          = esc_html__( 'Processing booking', 'awebooking' );
		$this->description    = esc_html__( 'This is a booking notification sent to customers containing booking details after payment.', 'awebooking' );
		$this->customer_email = true;
		$this->placeholders   = [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function init() {
		add_action( 'awebooking/awebooking/status_changed', [ $this, 'trigger' ], 10, 3 );
	}

	/**
	 * Trigger send email.
	 *
	 * @return void
	 */
	public function trigger( $new_status, $old_status, $booking ) {
		if ( 'awebooking-inprocess' !== $new_status ) {
			return;
		}

		if ( $this->is_enabled() ) {
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
		$this->recipient = $booking->get( 'customer_email' );

		$this->placeholders = ( new Booking_Placeholder( $booking, $this ) )->apply( $this->placeholders );
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
		return "Your booking has been received and is now being processed. Your booking details are shown below for your reference:\n\n{contents}\n\n{customer_details}";
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
		return abrs_get_template_content( 'emails/processing-booking.php', [
			'email'         => $this,
			'booking'       => $this->booking,
			'content'       => $this->format_string( $this->get_option( 'content' ) ),
		]);
	}
}
