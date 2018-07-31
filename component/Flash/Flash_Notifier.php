<?php
namespace AweBooking\Component\Flash;

use AweBooking\Support\Collection;

class Flash_Notifier {
	/* Constants */
	const LEVEL_INFO    = 'info';
	const LEVEL_SUCCESS = 'success';
	const LEVEL_UPDATED = 'updated';
	const LEVEL_WARNING = 'warning';
	const LEVEL_ERROR   = 'error';

	/**
	 * The session store.
	 *
	 * @var \AweBooking\Component\Flash\Session_Store
	 */
	protected $session;

	/**
	 * The group key.
	 *
	 * @var string
	 */
	protected $session_key;

	/**
	 * The messages collection.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $messages;

	/**
	 * Create the flash message.
	 *
	 * @param Session_Store $session     The WP_Session implementation.
	 * @param string        $session_key The group key.
	 */
	public function __construct( Session_Store $session, $session_key = 'flash_notification' ) {
		$this->session     = $session;
		$this->session_key = $session_key;
		$this->messages    = new Collection;
	}

	/**
	 * Flash an information message.
	 *
	 * @param  string $message Success message.
	 * @return $this
	 */
	public function info( $message ) {
		return $this->add_message( $message, 'info' );
	}

	/**
	 * Flash a success message.
	 *
	 * @param  string $message Success message.
	 * @return $this
	 */
	public function success( $message ) {
		return $this->add_message( $message, 'success' );
	}

	/**
	 * Flash a updated message.
	 *
	 * @param  string $message Updated message.
	 * @return $this
	 */
	public function updated( $message ) {
		return $this->add_message( $message, 'updated' );
	}

	/**
	 * Flash a warning message.
	 *
	 * @param  string $message Warning message.
	 * @return $this
	 */
	public function warning( $message ) {
		return $this->add_message( $message, 'warning' );
	}

	/**
	 * Flash an error message.
	 *
	 * @param  string $error Error message.
	 * @return $this
	 */
	public function error( $error ) {
		return $this->add_message( $error, 'error' );
	}

	/**
	 * Flash an dialog message (alias of overlay).
	 *
	 * @param  string $message The message.
	 * @param  string $title   The title.
	 * @param  string $level   The message level, default "info".
	 * @return $this
	 */
	public function dialog( $message = '', $title = '', $level = null ) {
		return $this->overlay( $message, $title, $level );
	}

	/**
	 * Flash an overlay message.
	 *
	 * @param  string $message The message.
	 * @param  string $title   The title.
	 * @param  string $level   The message level, default "info".
	 * @return $this
	 */
	public function overlay( $message = '', $title = '', $level = null ) {
		$overlay = true;

		// If no message was provided, we should update
		// the most recently added message.
		if ( ! $message ) {
			return $this->update_last_message( compact( 'title', 'overlay', 'level' ) );
		}

		return $this->add_message(
			new Message( compact( 'title', 'message', 'overlay', 'level' ) )
		);
	}

	/**
	 * Add an "important" flash to the session.
	 *
	 * @return $this
	 */
	public function important() {
		return $this->update_last_message( [ 'important' => true ] );
	}

	/**
	 * Flash a general message.
	 *
	 * @param string $message The flash message to be displayed.
	 * @param string $level   The message level: info, updated, success, error, warning.
	 *                        Default is 'info'.
	 * @return $this
	 */
	public function add_message( $message, $level = null ) {
		if ( ! $message instanceof Message ) {
			$message = new Message( compact( 'message', 'level' ) );
		}

		// Push the messages in the queue.
		$this->messages->push( $message );

		return $this->flash();
	}

	/**
	 * Clear all registered messages.
	 *
	 * @return $this
	 */
	public function clear() {
		$this->messages->clear();

		return $this;
	}

	/**
	 * Get all flashed messages.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function all() {
		return $this->session->get_flash(
			$this->session_key, $this->messages
		);
	}

	/**
	 * Flash all messages to the session.
	 *
	 * @return $this
	 */
	protected function flash() {
		$this->session->flash( $this->session_key, $this->messages );

		return $this;
	}

	/**
	 * Modify the most recently added message.
	 *
	 * @param  array $overrides The overrides attributes.
	 * @return $this
	 */
	protected function update_last_message( $overrides = [] ) {
		if ( $this->messages->isNotEmpty() ) {
			$this->messages->last()->update( $overrides );
		}

		return $this;
	}

	/**
	 * Getter protected property.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __get( $property ) {
		return $this->{$property};
	}
}
