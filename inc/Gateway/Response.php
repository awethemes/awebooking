<?php

namespace AweBooking\Gateway;

class Response {
	/**
	 * The response status (success, pending or error).
	 *
	 * @var string
	 */
	protected $status;

	/**
	 * The redirect URL.
	 *
	 * @var string|false
	 */
	protected $redirect;

	/**
	 * The response data.
	 *
	 * @var mixed
	 */
	protected $data;

	/**
	 * Constructor.
	 *
	 * @param string  $status   The response status.
	 * @param boolean $redirect The redirect URL.
	 * @param mixed   $data     The response data.
	 */
	public function __construct( $status, $redirect = false, $data = null ) {
		$this->status   = strtolower( $status );
		$this->redirect = $redirect;
		$this->data     = $data;
	}

	/**
	 * Is the response successful?
	 *
	 * @return boolean
	 */
	public function is_successful() {
		return 'success' === $this->status;
	}

	/**
	 * Is the response is error?
	 *
	 * @return boolean
	 */
	public function is_error() {
		return empty( $this->status ) || 'error' === $this->status;
	}

	/**
	 * Does the response require a redirect?
	 *
	 * @return boolean
	 */
	public function is_redirect() {
		return $this->redirect && abrs_valid_url( $this->redirect );
	}

	/**
	 * Gets the redirect target url.
	 *
	 * @return string
	 */
	public function get_redirect_url() {
		return $this->redirect;
	}

	/**
	 * Gets the data.
	 *
	 * @return mixed
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Sets the data.
	 *
	 * @param  mixed $data The data.
	 * @return $this
	 */
	public function set_data( $data ) {
		$this->data = $data;

		return $this;
	}

	/**
	 * Sets the data.
	 *
	 * @param  mixed $data The data.
	 * @return $this
	 */
	public function data( $data ) {
		return $this->set_data( $data );
	}
}
