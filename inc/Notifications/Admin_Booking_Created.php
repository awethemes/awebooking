<?php
namespace AweBooking\Notification;

class Admin_Booking_Created extends Booking_Notification {
	/**
	 * {@inheritdoc}
	 */
	public function get_markdown_contents() {
		return abrs_option( 'email_admin_new_content' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_subject() {
		return $this->format_string( abrs_option( 'email_admin_new_subject' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_heading() {}
}
