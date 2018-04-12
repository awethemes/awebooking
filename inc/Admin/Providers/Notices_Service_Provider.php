<?php
namespace AweBooking\Admin\Providers;

use AweBooking\Support\Service_Provider;
use AweBooking\Component\Flash\Session_Store;
use AweBooking\Component\Flash\Flash_Notifier;

class Notices_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @return void
	 */
	public function register() {
		$this->plugin->singleton( 'admin_notices', function() {
			return new Flash_Notifier( $this->plugin->make( Session_Store::class ), '_admin_notices' );
		});
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_notices', [ $this, 'display_admin_notices' ] );
	}

	/**
	 * Setup and display admin notices.
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	 *
	 * @access private
	 */
	public function display_admin_notices() {
		$messages = $this->plugin['admin_notices']->all();

		if ( $messages && $messages->isNotEmpty() ) {
			include dirname( __DIR__ ) . '/views/admin-notices.php';
		}
	}
}
