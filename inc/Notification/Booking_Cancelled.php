<?php
namespace AweBooking\Notification;

class Booking_Cancelled extends Booking_Notification {
	/**
	 * {@inheritdoc}
	 */
	public function get_markdown_contents() {
		return awebooking_option( 'email_cancelled_content' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_subject() {
		return $this->format_string( awebooking_option( 'email_cancelled_content' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_heading() {}
}
