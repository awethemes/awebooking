<?php

namespace AweBooking\Email\Templates;

use AweBooking\Email\Mailable;

class Sample_Email extends Mailable {
	/**
	 * {@inheritdoc}
	 */
	public function setup() {}

	/**
	 * Gets email content type.
	 *
	 * @return string
	 */
	public function get_content_type() {
		return 'text/html';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_content_html() {
		return $this->get_template( 'sample' );
	}
}
