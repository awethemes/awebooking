<?php

namespace AweBooking\Component\Http\Exceptions;

class RedirectResponseException extends \RuntimeException {
	/**
	 * Constructor.
	 *
	 * @param string $redirect
	 * @param int    $status
	 */
	public function __construct( $redirect, $status = 302 ) {
		parent::__construct( $redirect, $status );
	}

	/**
	 * Return the redirect url.
	 *
	 * @return string
	 */
	public function getRedirectUrl() {
		return $this->getMessage();
	}

	/**
	 * Return redirect status code.
	 *
	 * @return int
	 */
	public function getStatusCode() {
		return $this->getCode();
	}
}
