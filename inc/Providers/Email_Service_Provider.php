<?php
namespace AweBooking\Providers;

use AweBooking\Email\Mailer;
use AweBooking\Support\Service_Provider;

class Email_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		$this->plugin->singleton( 'mailer', function() {
			return new Mailer( $this->plugin );
		});

		$this->plugin->alias( 'mailer', Mailer::class );
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$this->plugin['mailer']->init();

		// Email templates.
		add_action( 'awebooking_email_header', [ $this, 'template_header' ] );
		add_action( 'awebooking_email_footer', [ $this, 'template_footer' ] );
		add_action( 'awebooking_email_booking_details', [ $this, 'template_hotel_address' ], 10, 2 );
		add_action( 'awebooking_email_booking_details', [ $this, 'template_customer_details' ], 20, 2 );
		add_action( 'awebooking_email_booking_details', [ $this, 'template_booking_details' ], 30, 2 );

		// Trigger send emails.
		add_action( 'abrs_new_customer_note', [ $this, 'send_customer_note' ], 10, 2 );
		add_action( 'abrs_booking_status_changed', [ $this, 'trigger_status_changed' ], 10, 3 );
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

	/**
	 * Trigger send mail when new_customer_note.
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

		switch ( $booking->get_status() ) {
			case 'cancelled':
				$mail = abrs_mailer( 'cancelled' );
				break;
		}

		if ( $mail && $mail->is_enabled() ) {
			$mail->build( $booking )->send();
		}
	}
}
