<?php

namespace AweBooking\Email;

use AweBooking\Model\Booking;
use AweBooking\Support\Manager;

class Mailer extends Manager {
	/**
	 * List of email template.
	 *
	 * @var array
	 */
	protected $templates = [
		'invoice'       => \AweBooking\Email\Templates\Invoice::class,
		'new_booking'   => \AweBooking\Email\Templates\New_Booking::class,
		'reserved'      => \AweBooking\Email\Templates\Reserved::class,
		'processing'    => \AweBooking\Email\Templates\Processing::class,
		'completed'     => \AweBooking\Email\Templates\Completed::class,
		'cancelled'     => \AweBooking\Email\Templates\Cancelled::class,
		'customer_note' => \AweBooking\Email\Templates\Customer_Note::class,
	];

	/**
	 * Init the mailer.
	 *
	 * @return void
	 */
	public function init() {
		$this->register_templates();

		// Email templates.
		add_action( 'abrs_email_header', [ $this, 'template_header' ] );
		add_action( 'abrs_email_footer', [ $this, 'template_footer' ] );
		add_action( 'abrs_email_booking_details', [ $this, 'template_hotel_address' ], 10, 2 );
		add_action( 'abrs_email_booking_details', [ $this, 'template_customer_details' ], 20, 2 );
		add_action( 'abrs_email_booking_details', [ $this, 'template_booking_details' ], 30, 2 );

		// Trigger send emails.
		add_action( 'abrs_checkout_processed', [ $this, 'send_new_booking' ], 10, 1 );
		add_action( 'abrs_new_customer_note', [ $this, 'send_customer_note' ], 10, 2 );
		add_action( 'abrs_booking_status_changed', [ $this, 'trigger_status_changed' ], 10, 3 );
	}

	/**
	 * Register default templates.
	 *
	 * @return void
	 */
	protected function register_templates() {
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
	 * Trigger send email when new booking created.
	 *
	 * @param  \AweBooking\Model\Booking|int $booking The booking instance or booking ID.
	 * @return void
	 */
	public function send_new_booking( $booking ) {
		if ( ! $booking instanceof Booking ) {
			$booking = abrs_get_booking( $booking );
		}

		if ( is_null( $booking ) ) {
			return;
		}

		$mail = abrs_mailer( 'new_booking' );

		if ( $mail && $mail->is_enabled() && $mail->get_recipient() ) {
			$mail->build( $booking )->send();
		}
	}

	/**
	 * Trigger send email when new_customer_note.
	 *
	 * @param  \AweBooking\Model\Booking $booking       The booking instance.
	 * @param  string                    $customer_note The note to customer.
	 * @return void
	 */
	public function send_customer_note( $booking, $customer_note ) {
		$mail = abrs_mailer( 'customer_note' );

		if ( $mail && $mail->is_enabled() ) {
			$mail->build( $booking, $customer_note )->send();
		}
	}

	/**
	 * Trigger send email when booking status changed.
	 *
	 * @param string                    $new_status The new status.
	 * @param string                    $old_status The old status.
	 * @param \AweBooking\Model\Booking $booking    The booking instance.
	 * @return void
	 */
	public function trigger_status_changed( $new_status, $old_status, $booking ) {
		$mail = null;

		if ( ( 'awebooking-cancelled' === $new_status ) && in_array( $old_status, [ 'awebooking-inprocess', 'awebooking-on-hold' ] ) ) {
			$mail = abrs_mailer( 'cancelled' );
		}

		if ( ( 'awebooking-inprocess' === $new_status ) && in_array( $old_status, [ 'awebooking-on-hold', 'awebooking-pending' ] ) ) {
			$mail = abrs_mailer( 'processing' );
		}

		if ( ( 'awebooking-on-hold' === $new_status ) && ( 'awebooking-pending' === $old_status ) ) {
			$mail = abrs_mailer( 'reserved' );
		}

		if ( 'awebooking-completed' === $new_status ) {
			$mail = abrs_mailer( 'completed' );
		}

		if ( $mail && $mail->is_enabled() ) {
			$mail->build( $booking )->send();
		}
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

	/**
	 * Display default email header.
	 *
	 * @param \AweBooking\Email\Mailable $email The mailable instance.
	 * @access private
	 */
	public function template_header( $email ) {
		abrs_get_template( 'emails/header.php', compact( 'email' ) );
	}

	/**
	 * Display default email footer.
	 *
	 * @param \AweBooking\Email\Mailable $email The mailable instance.
	 * @access private
	 */
	public function template_footer( $email ) {
		abrs_get_template( 'emails/footer.php', compact( 'email' ) );
	}

	/**
	 * Display the hotel address.
	 *
	 * @param \AweBooking\Model\Booking  $booking The booking instance.
	 * @param \AweBooking\Email\Mailable $email   The mailable instance.
	 */
	public function template_hotel_address( $booking, $email ) {
		abrs_get_template( 'emails/partials/line-hotel.php', compact( 'booking', 'email' ) );
	}

	/**
	 * Display the customer details.
	 *
	 * @param \AweBooking\Model\Booking  $booking The booking instance.
	 * @param \AweBooking\Email\Mailable $email   The mailable instance.
	 */
	public function template_customer_details( $booking, $email ) {
		abrs_get_template( 'emails/partials/line-customer.php', compact( 'booking', 'email' ) );
	}

	/**
	 * Display the booking details.
	 *
	 * @param \AweBooking\Model\Booking  $booking The booking instance.
	 * @param \AweBooking\Email\Mailable $email   The mailable instance.
	 */
	public function template_booking_details( $booking, $email ) {
		abrs_get_template( 'emails/partials/line-booking.php', compact( 'booking', 'email' ) );
	}
}
