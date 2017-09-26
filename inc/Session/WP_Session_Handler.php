<?php
namespace AweBooking\Session;

use SessionHandlerInterface;
use AweBooking\Support\Carbonate;

class WP_Session_Handler implements SessionHandlerInterface {
	/**
	 * The number of minutes the session should be valid.
	 *
	 * @var int
	 */
	protected $minutes;

	/**
	 * The existence state of the session.
	 *
	 * @var bool
	 */
	protected $exists = false;

	/**
	 * Create a new database session handler instance.
	 *
	 * @param  int $minutes The number of minutes.
	 * @return void
	 */
	public function __construct( $minutes ) {
		$this->minutes = $minutes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function open( $save_path, $session_name ) {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function close() {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function read( $session_id ) {
		$session = get_option( $this->get_option_name( $session_id ), null );

		if ( $this->expired( $session ) ) {
			$this->exists = true;

			return;
		}

		if ( isset( $session['payload'] ) ) {
			$this->exists = true;

			return $session['payload'];
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function write( $session_id, $data ) {
		$payload = [
			'payload' => $data,
			'last_activity' => Carbonate::now()->getTimestamp(),
		];

		// Try determines  existence state of session ID.
		if ( ! $this->exists ) {
			$this->read( $session_id );
		}

		if ( $this->exists ) {
			update_option( $this->get_option_name( $session_id ), $payload, false );
		} else {
			add_option( $this->get_option_name( $session_id ), $payload, '', 'no' );
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function destroy( $session_id ) {
		delete_option( $this->get_option_name( $session_id ) );

		$this->exists = false;

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function gc( $lifetime ) {
		global $wpdb;

		$sessions = $wpdb->get_results( "SELECT * FROM `$wpdb->options` WHERE `option_name` LIKE '_awebooking_session_%' LIMIT 0, 1000", ARRAY_A );
		if ( empty( $sessions ) ) {
			return;
		}

		$expired = [];
		$expired_time = Carbonate::now()->getTimestamp() - $lifetime;

		foreach ( $sessions as $session ) {
			$payload = maybe_unserialize( $session['option_value'] );

			if ( ! isset( $payload['last_activity'] ) || $payload['last_activity'] <= $expired_time ) {
				$expired[] = (int) $session['option_id'];
			}
		}

		// Delete expired sessions.
		if ( ! empty( $expired ) ) {
			$placeholders = implode( ', ', $expired );
			// @codingStandardsIgnoreLine
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_id` IN ($placeholders)" );
		}
	}

	/**
	 * Determine if the session is expired.
	 *
	 * @param  array $session An array session payload data.
	 * @return bool
	 */
	protected function expired( $session ) {
		return isset( $session['last_activity'] ) &&
			$session['last_activity'] < Carbonate::now()->subMinutes( $this->minutes )->getTimestamp();
	}

	/**
	 * Returns option name.
	 *
	 * @param  string $session_id Session ID.
	 * @return string
	 */
	protected function get_option_name( $session_id ) {
		return "_awebooking_session_{$session_id}";
	}
}
