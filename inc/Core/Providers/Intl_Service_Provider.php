<?php

namespace AweBooking\Core\Providers;

use AweBooking\Support\Service_Provider;
use AweBooking\Component\Country\ISO3166;
use AweBooking\Component\Currency\ISO4217;

class Intl_Service_Provider extends Service_Provider {
	/**
	 * Internationalization service provider.
	 *
	 * @access private
	 */
	public function register() {
		$this->plugin->singleton( 'countries', function() {
			return new ISO3166;
		});

		$this->plugin->singleton( 'currencies', function() {
			return new ISO4217;
		});

		$this->plugin->alias( 'countries', ISO3166::class );
		$this->plugin->alias( 'currencies', ISO4217::class );
	}

	/**
	 * Init service provider.
	 *
	 * @access private
	 */
	public function init() {
		// ...
	}
}
