<?php
namespace AweBooking\Interfaces;

interface Mailable {
	/**
	 * Get email message.
	 *
	 * @return string
	 */
	public function message();
}
