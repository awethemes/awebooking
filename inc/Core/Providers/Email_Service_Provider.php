<?php

namespace AweBooking\Core\Providers;

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
	}
}
