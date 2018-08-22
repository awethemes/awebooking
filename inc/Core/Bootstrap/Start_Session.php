<?php
namespace AweBooking\Core\Bootstrap;

use AweBooking\Plugin;
use Awethemes\WP_Session\WP_Session;
use AweBooking\Component\Flash\Session_Store;
use AweBooking\Component\Flash\WP_Sesstion_Store;
use AweBooking\Component\Flash\Flash_Notifier;

class Start_Session {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * Start session bootstrapper.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Bootstrap the AweBooking.
	 *
	 * @return void
	 */
	public function bootstrap() {
		$this->register_session_binding();

		$this->start_session();

		$this->register_flash_binding();
	}

	/**
	 * Start the session.
	 *
	 * @return void
	 */
	protected function start_session() {
		// Start the session.
		$this->plugin->make( 'session' )->hooks();

		if ( did_action( 'plugins_loaded' ) ) {
			$this->plugin['session']->start_session();
		}
	}

	/**
	 * Register the wp-session binding.
	 *
	 * @return void
	 */
	protected function register_session_binding() {
		$this->plugin->singleton( 'session', function() {
			return new WP_Session( 'awebooking_session', [
				'lifetime'        => 120,
				'expire_on_close' => true,
			]);
		});

		$this->plugin->singleton( 'session.store', function() {
			return $this->plugin['session']->get_store();
		});

		$this->plugin->alias( 'session', WP_Session::class );
	}

	/**
	 * Register the wp-session binding.
	 *
	 * @return void
	 */
	protected function register_flash_binding() {
		$this->plugin->bind( Session_Store::class, WP_Sesstion_Store::class );

		$this->plugin->singleton( 'flash', function () {
			return new Flash_Notifier( $this->plugin->make( Session_Store::class ) );
		});
	}
}
