<?php
namespace AweBooking\Support;

use Awethemes\WP_Session\WP_Session;

class Flash_Message {
	/**
	 * WP_Session instance.
	 *
	 * @var \Awethemes\WP_Session\WP_Session
	 */
	protected $session;

	/**
	 * The group key.
	 *
	 * @var string
	 */
	protected $group_key;

	/**
	 * The messages collection.
	 *
	 * @var array
	 */
	protected $messages = [];

	/**
	 * Create the flash message.
	 *
	 * @param WP_Session $session   The WP_Session implementation.
	 * @param string     $group_key The group key.
	 */
	public function __construct( WP_Session $session, $group_key = 'flash_messages' ) {
		$this->session = $session;
		$this->group_key = $group_key;
	}

	/**
	 * Get messages.
	 *
	 * @param  string $type Optional, specify message type.
	 * @return array
	 */
	public function get( $type = null ) {
		if ( is_null( $type ) ) {
			return $this->messages;
		}

		$filter = array_filter( $this->messages, function( $message ) use ( $type ) {
			return $message['type'] === $type;
		});

		return array_values( $filter );
	}

	/**
	 * Checks if have any message.
	 *
	 * @param  string $type Optional, specify message type.
	 * @return bool
	 */
	public function has( $type = null ) {
		if ( is_null( $type ) ) {
			return count( $this->messages ) > 0;
		}

		return count( $this->get( $type ) ) > 0;
	}

	/**
	 * Get first message.
	 *
	 * @param  string $type Optional, specify message type.
	 * @return array|null
	 */
	public function first( $type = null ) {
		if ( is_null( $type ) ) {
			return isset( $this->messages[0] ) ? $this->messages[0] : null;
		}

		$messages = $this->get( $type );
		return isset( $messages[0] ) ? $messages[0] : null;
	}

	/**
	 * Get all messages.
	 *
	 * @return array
	 */
	public function all() {
		return $this->messages;
	}

	/**
	 * Add updated to flash message.
	 *
	 * @param  string $message Updated message.
	 * @return $this
	 */
	public function updated( $message ) {
		return $this->add_message( $message, 'updated' );
	}

	/**
	 * Add success to flash message.
	 *
	 * @param  string $message Success message.
	 * @return $this
	 */
	public function success( $message ) {
		return $this->add_message( $message, 'success' );
	}

	/**
	 * Add info to flash message.
	 *
	 * @param  string $message Success message.
	 * @return $this
	 */
	public function info( $message ) {
		return $this->add_message( $message, 'info' );
	}

	/**
	 * Add warning to flash message.
	 *
	 * @param  string $message Warning message.
	 * @return $this
	 */
	public function warning( $message ) {
		return $this->add_message( $message, 'warning' );
	}

	/**
	 * Add error to flash message.
	 *
	 * @param  string $error Error message.
	 * @return $this
	 */
	public function error( $error ) {
		return $this->add_message( $error, 'error' );
	}

	/**
	 * Clear all registered messages.
	 *
	 * @return $this
	 */
	public function clear() {
		$this->messages = [];

		return $this;
	}

	/**
	 * Add a feedback (error/success) message.
	 *
	 * @param string $message Feedback message to be displayed.
	 * @param string $type    Message type. 'updated', 'success', 'info', 'error', 'warning'.
	 *                        Default: 'success'.
	 * @return $this
	 */
	public function add_message( $message, $type = 'success' ) {
		// Create an array of message.
		$message = compact( 'message', 'type' );

		// Save messages so we can still output messages without a page reload.
		$this->messages[] = $message;

		// Store messages for page reload display.
		$this->store_messages( $this->messages );

		return $this;
	}

	/**
	 * Set up the flash message.
	 *
	 * After the message is mapped, it removes the message vars from
	 * the store so that the message is not shown to the user multiple times.
	 */
	public function setup_message() {
		if ( ! empty( $this->messages ) ) {
			return;
		}

		$raw_messages = $this->get_messages();
		if ( ! $raw_messages ) {
			return;
		}

		$messages = [];
		foreach ( $raw_messages as $message ) {
			if ( ! isset( $message['message'] ) || ! isset( $message['type'] ) ) {
				continue;
			}

			$messages[] = [
				'type'    => stripslashes( $message['type'] ),
				'message' => wp_kses_post( wp_unslash( $message['message'] ) ),
			];
		}

		$this->messages = $messages;

		$this->flush_messages();
	}

	/**
	 * Store the messages.
	 *
	 * @param  array|mixed $messages The messages.
	 * @return void
	 */
	protected function store_messages( $messages ) {
		$this->session->flash( $this->group_key, maybe_serialize( $messages ) );
	}

	/**
	 * Flush the messages from the store.
	 *
	 * @return void
	 */
	protected function flush_messages() {
		$this->session->remove( $this->group_key );
	}

	/**
	 * Get the messages in the store.
	 *
	 * @return array|null
	 */
	protected function get_messages() {
		return maybe_unserialize(
			$this->session->pull( $this->group_key, [] )
		);
	}
}
