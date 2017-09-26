<?php
namespace AweBooking\Session;

use AweBooking\Support\Carbonate;
use AweBooking\Support\Service_Hooks;

class Session_Service_Provider extends Service_Hooks {
	public function register( $awebooking ) {
		$this->binding_session( $awebooking );

		// Binding session start and commit in to WP core.
		if ( php_sapi_name() !== 'cli' && ( ! defined( 'WP_CLI' ) || false === WP_CLI ) ) {
			add_action( 'plugins_loaded', [ $this, 'start_session' ] );
			add_action( 'shutdown', [ $this, 'commit_session' ] );
		}

		// Register the garbage collector.
		add_action( 'wp', [ $this, 'register_garbage_collection' ] );
		add_action( 'awebooking_session_garbage_collection', [ $this, 'cleanup_session' ] );
	}

	/**
	 * Binding session in to AweBooking
	 *
	 * @param  [type] $awebooking [description]
	 * @return void
	 */
	protected function binding_session( $awebooking ) {
		// The number of minutes that you wish the session life.
		$awebooking['session_name']            = 'awebooking_session';
		$awebooking['session_lifetime']        = 60 * 48; // 2 days.
		$awebooking['session_expire_on_close'] = false;

		$awebooking->singleton( 'session', function( $a ) {
			return new Store( $a['session_name'], new WP_Session_Handler( $a['session_lifetime'] ) );
		});

		$awebooking->alias( 'session', Store::class );
		$awebooking->alias( 'session', Session::class );
	}

	/**
	 * Start the session when `plugin_loaded`.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function start_session() {
		$session = $this->get_session();

		// Start the session.
		$session->start();

		// Add the session identifier to cookie, so we can re-use that in lifetime.
		awebooking_setcookie(
			$session->get_name(), $session->get_id(), $this->get_cookie_expiration_date()
		);
	}

	/**
	 * Commit session when `shutdown` fired.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function commit_session() {
		$this->get_session()->save();
	}

	/**
	 * Clean up expired sessions by removing data and their expiration entries from
	 * the WordPress options table.
	 *
	 * This method should never be called directly and should instead be triggered as part
	 * of a scheduled task or cron job.
	 *
	 * @access private
	 */
	public function cleanup_session() {
		if ( defined( 'WP_SETUP_CONFIG' ) ) {
			return;
		}

		if ( defined( 'WP_INSTALLING' ) ) {
			return;
		}

		$this->get_session()
			->get_handler()
			->gc( awebooking( 'session_lifetime' ) * 60 );
	}

	/**
	 * Register the garbage collector as a hourly event.
	 *
	 * @access private
	 */
	public function register_garbage_collection() {
		if ( ! wp_next_scheduled( 'awebooking_session_garbage_collection' ) ) {
			wp_schedule_event( time(), 'hourly', 'awebooking_session_garbage_collection' );
		}
	}

	/**
	 * Get the session implementation from the container.
	 */
	protected function get_session() {
		$session = awebooking()->make( Session::class );

		$session_name = $session->get_name();
		$session->set_id( isset( $_COOKIE[ $session_name ] ) ? sanitize_text_field( $_COOKIE[ $session_name ] ) : null );

		return $session;
	}

	/**
	 * Get the cookie lifetime in timestamp.
	 *
	 * @return int
	 */
	protected function get_cookie_expiration_date() {
		$awebooking = awebooking();

		return $awebooking['session_expire_on_close'] ? 0 :
			Carbonate::now()->addMinutes( $awebooking['session_lifetime'] )->getTimestamp();
	}
}
