<?php
namespace AweBooking\Email;

use AweBooking\Support\Manager;

class Mailer extends Manager {
	/**
	 * List of email template.
	 *
	 * @var array
	 */
	protected $templates = [
		\AweBooking\Email\Templates\Invoice::class,
		\AweBooking\Email\Templates\New_Booking::class,
		\AweBooking\Email\Templates\Cancelled_Booking::class,
		\AweBooking\Email\Templates\Failed_Booking::class,
		\AweBooking\Email\Templates\Reserved_Booking::class,
		\AweBooking\Email\Templates\Processing_Booking::class,
		\AweBooking\Email\Templates\Completed_Booking::class,
		\AweBooking\Email\Templates\Customer_Note::class,
	];

	/**
	 * Init the mailer.
	 *
	 * @return void
	 */
	public function init() {
		$templates = apply_filters( 'abrs_email_templates', $this->templates );

		foreach ( $templates as $template ) {
			$this->register( $this->plugin->make( $template ) );
		}

		do_action( 'abrs_register_email_template', $this );
	}

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
		do_action( 'abrs_email_header', $email );
	}

	/**
	 * Load footer template.
	 *
	 * @param  \AweBooking\Email\Mailable|null $email The curent email.
	 * @return void
	 */
	public function footer( Mailable $email = null ) {
		do_action( 'abrs_email_footer', $email );
	}
}
