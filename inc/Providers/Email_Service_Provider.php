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
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$mailer = $this->plugin->make( 'mailer' );

		$this->register_emails();

		// Handle trigger emails.
		add_action( 'awebooking/new_customer_note', function( $booking, $note ) use ( $mailer ) {
			$mailer['customer_note']->build( $booking, $note )->send();
		}, 10, 2 );
	}

	/**
	 * Register the emails into the manager.
	 *
	 * @return void
	 */
	protected function register_emails() {
		$manager = $this->plugin->make( 'mailer' );

		$templates = apply_filters( 'awebooking/email_templates', [
			\AweBooking\Email\Templates\Invoice::class,
			\AweBooking\Email\Templates\Customer_Note::class,
		]);

		foreach ( $templates as $template ) {
			$manager->register( $this->plugin->make( $template ) );
		}
	}
}
