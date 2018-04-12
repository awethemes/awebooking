<?php
namespace AweBooking\Notification;

class Booking_Processing extends Booking_Notification {
	/**
	 * {@inheritdoc}
	 */
	public function get_markdown_contents() {
		return abrs_option( 'email_processing_content' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_subject() {
		return $this->format_string( abrs_option( 'email_processing_subject' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_heading() {}
}
