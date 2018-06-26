<?php
namespace AweBooking\Component\Http\Exceptions;

class ValidationFailedException extends \RuntimeException {
	/**
	 * The errors.
	 *
	 * @var mixed
	 */
	protected $errors;

	/**
	 * Gets the validation errors.
	 *
	 * @return mixed
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Set the validation errors.
	 *
	 * @param mixed $errors The errors.
	 *
	 * @return ValidationFailedException
	 */
	public function set_errors( $errors ) {
		$this->errors = $errors;

		return $this;
	}
}
