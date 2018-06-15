<?php
namespace AweBooking\Email;

use AweBooking\Support\Manager;

class Mailer extends Manager {
	/**
	 * Handle register a email template.
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

	/**
	 * Load header template.
	 *
	 * @param  \AweBooking\Email\Mailable|null $email The curent email.
	 * @return void
	 */
	public function header( Mailable $email = null ) {
		do_action( 'awebooking_email_header', $email );
	}

	/**
	 * Load footer template.
	 *
	 * @param  \AweBooking\Email\Mailable|null $email The curent email.
	 * @return void
	 */
	public function footer( Mailable $email = null ) {
		do_action( 'awebooking_email_footer', $email );
	}
}
