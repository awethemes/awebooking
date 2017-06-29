<?php
namespace AweBooking\Support;

class Flash_Message {
	/* Constants */
	const COOKIE_NAME = 'awebooking-messages';

	/**
	 * The messages collection.
	 *
	 * @var array
	 */
	protected $messages;

	/**
	 * Flash message.
	 */
	public function __construct() {
		$this->messages = [];
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
	 * @return boolean
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
	 * Add a feedback (error/success) message to the WP cookie
	 * so it can be displayed after the page reloads.
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

		// Send the values to the cookie for page reload display.
		Utils::setcookie( static::COOKIE_NAME, maybe_serialize( $this->messages ), time() + 60 * 60 * 24 );

		return $this;
	}

	/**
	 * Set up the flash message.
	 *
	 * After the message is mapped, it removes the message vars from the cookie
	 * so that the message is not shown to the user multiple times.
	 */
	public function setup_message() {
		if ( empty( $this->messages ) && isset( $_COOKIE[ static::COOKIE_NAME ] ) ) {
			$cookie_messages = maybe_unserialize( wp_unslash( $_COOKIE[ static::COOKIE_NAME ] ) );
			$messages = [];

			foreach ( $cookie_messages as $message ) {
				if ( ! isset( $message['message'] ) || ! isset( $message['type'] ) ) {
					continue;
				}

				$messages[] = [ // Don't hack me, please!
					'type'    => stripslashes( $message['type'] ),
					'message' => sanitize_text_field( wp_unslash( $message['message'] ) ),
				];
			}

			$this->messages = $messages;
			Utils::setcookie( static::COOKIE_NAME, null, time() - 1000 );
		}
	}
}
