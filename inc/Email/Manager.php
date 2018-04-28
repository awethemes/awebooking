<?php
namespace AweBooking\Email;

use AweBooking\Support\Manager as Abstract_Manager;

class Manager extends Abstract_Manager {
	/**
	 * Handle register a new email.
	 *
	 * @param  mixed $email The mailable implementation.
	 * @return bool
	 *
	 * @throws \InvalidArgumentException
	 */
	public function register( $email ) {
		if ( ! $email instanceof Mailable ) {
			throw new \InvalidArgumentException( 'Email template must be instance of Mailable.' );
		}

		$key = $email->get_id();
		if ( empty( $key ) || $this->registered( $key ) ) {
			return false;
		}

		$this->drivers[ $key ] = $email;

		return true;
	}
}
