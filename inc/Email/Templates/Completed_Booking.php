<?php
namespace AweBooking\Email\Templates;

use AweBooking\Email\Mailable;

class Completed_Booking extends Mailable {
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
		$this->id             = 'completed_booking';
		$this->title          = esc_html__( 'Completed booking', 'awebooking' );
		$this->description    = esc_html__( 'Booking complete emails are sent to customers when their bookings are marked completed.', 'awebooking' );
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
		if ( 'awebooking-completed' !== $new_status ) {
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

		$this->placeholders = ( new Booking_Placeholder( $booking ) )->apply( $this->placeholders );
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
		return "Hi there. Your recent booking on {site_title} has been completed. Your booking details are shown below for your reference:\n\n{contents}\n\n{customer_details}";
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
		return abrs_get_template_content( 'emails/completed-booking.php', [
			'email'         => $this,
			'booking'       => $this->booking,
			'content'       => $this->format_string( $this->get_option( 'content' ) ),
		]);
	}
}
