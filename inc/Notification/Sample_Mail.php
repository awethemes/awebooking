<?php
namespace AweBooking\Notification;

use AweBooking\Mail\Mailable;

class Sample_Mail extends Mailable {
	/**
	 * Build the message.
	 *
	 * @return string
	 */
	public function build() {
		return $this->get_template( 'sample', [
			'date' => date( 'Y-m-d' ),
		]);
	}

	/**
	 * Get email subject.
	 *
	 * @return void
	 */
	public function get_subject() {}

	/**
	 * Get email heading.
	 *
	 * @return void
	 */
	public function get_heading() {}
}
