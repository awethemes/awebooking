<?php

namespace AweBooking\Core\Bootstrap;

class Include_Functions {
	/**
	 * Bootstrap the plugin.
	 *
	 * @return void
	 */
	public function bootstrap() {
		require dirname( __DIR__ ) . '/functions.php';

		add_action( 'after_setup_theme', [ $this, 'include_functions' ], 11 );
	}

	/**
	 * Function used to init AwweBooking template functions.
	 *
	 * This makes them pluggable by plugins and themes.
	 *
	 * @access private
	 */
	public function include_functions() {
		include_once dirname( dirname( __DIR__ ) ) . '/Frontend/functions.php';
	}
}
