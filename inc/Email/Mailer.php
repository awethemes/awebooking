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
		\AweBooking\Email\Templates\Customer_Note::class,
	];

	/**
	 * Init the mailer (trigger in `init`).
	 *
	 * @return void
	 */
	public function init() {
		$this->register_templates();

		// Email templates.
		add_action( 'awebooking/email_header', [ $this, 'template_header' ] );
		add_action( 'awebooking/email_footer', [ $this, 'template_footer' ] );

		// Trigger send emails.
		add_action( 'awebooking/new_customer_note', [ $this, 'trigger_customer_note' ], 10, 2 );
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
	 * Perform register email templates.
	 *
	 * @return void
	 */
	protected function register_templates() {
		$templates = apply_filters( 'awebooking/email_templates', $this->templates );

		foreach ( $templates as $template ) {
			$this->register( $this->plugin->make( $template ) );
		}

		do_action( 'awebooking/register_email_template', $this );
	}

	/**
	 * Trigger send mail when new_customer_note.
	 *
	 * @param  \AweBooking\Model\Booking $booking       The booking instance.
	 * @param  string                    $customer_note The note to customer.
	 * @return void
	 */
	public function trigger_customer_note( $booking, $customer_note ) {
		$mail = $this->get( 'customer_note' );

		if ( $mail->is_enabled() ) {
			$mail->build( $booking, $customer_note )->send();
		}
	}

	/**
	 * Load header template.
	 *
	 * @param  \AweBooking\Email\Mailable|null $email The curent email.
	 * @return void
	 */
	public function header( Mailable $email = null ) {
		do_action( 'awebooking/email_header', $email );
	}

	/**
	 * Load footer template.
	 *
	 * @param  \AweBooking\Email\Mailable|null $email The curent email.
	 * @return void
	 */
	public function footer( Mailable $email = null ) {
		do_action( 'awebooking/email_footer', $email );
	}

	/**
	 * Default email header.
	 *
	 * @access private
	 */
	public function template_header() {
		abrs_get_template( 'emails/header.php' );
	}

	/**
	 * Default email footer.
	 *
	 * @access private
	 */
	public function template_footer() {
		abrs_get_template( 'emails/footer.php' );
	}
}
