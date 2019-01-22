<?php

namespace AweBooking\Core\Bootstrap;

use Exception;
use AweBooking\Plugin;

class Boot_Providers {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * Setup environment bootstrapper.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Bootstrap the plugin.
	 *
	 * @return void
	 */
	public function bootstrap() {
		add_action( 'init', [ $this, 'boot_providers' ], 11 );
	}

	/**
	 * Boot the plugin providers.
	 *
	 * @access private
	 */
	public function boot_providers() {
		try {
			$this->plugin->boot();
		} catch ( Exception $e ) {
			$this->plugin->catch_exception( $e );
		}
	}
}
