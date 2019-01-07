<?php

namespace AweBooking\Core\Providers;

use AweBooking\Premium;
use AweBooking\Support\Plugin_Updater;
use AweBooking\Support\Service_Provider;

class Addons_Service_Provider extends Service_Provider {
	/**
	 * The hook that trigger register.
	 *
	 * @var string
	 */
	protected $when = 'admin_init';

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! is_admin() || ! Premium::get_api_code() ) {
			return;
		}

		foreach ( Premium::$addons as $addon_name => $addon ) {
			$updater = new Plugin_Updater( $addon_name, $addon );

			if ( is_plugin_active( $addon ) || $updater->get_plugin_file() ) {
				$updater->hooks();
			}
		}
	}
}
