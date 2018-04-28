<?php
namespace AweBooking\Providers;

use AweBooking\Email\Manager;
use AweBooking\Support\Service_Provider;

class Email_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		$this->plugin->singleton( 'mailer', function() {
			return new Manager( $this->plugin );
		});
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$mailer = $this->plugin->make( 'mailer' );

		$templates = apply_filters( 'awebooking/email_templates', [
			\AweBooking\Email\Templates\Invoice::class,
		]);

		foreach ( $templates as $template ) {
			$mailer->register( $this->plugin->make( $template ) );
		}
	}
}
