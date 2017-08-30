<?php
namespace AweBooking\Admin;

use AweBooking\Support\Flash_Message;

class Admin_Notices extends Flash_Message {
	/**
	 * Setup and display admin notice.
	 *
	 * Call this: add_action( 'admin_notices', [ $this, 'display' ] );
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	 *
	 * @return void
	 */
	public function display() {
		$this->setup_message();

		if ( ! $this->has() ) {
			return;
		}

		foreach ( $this->all() as $message ) : ?>
			<div class="notice notice-<?php echo esc_attr( $message['type'] ); ?> is-dismissible">
				<p><?php echo esc_html( $message['message'] ); ?></p>
			</div>
		<?php endforeach;
	}

	/**
	 * Store the messages.
	 *
	 * @param  array|mixed $messages The messages.
	 * @return void
	 */
	protected function store_messages( $messages ) {
		set_transient( '_awebooking_messages', $messages, 60 );
	}

	/**
	 * Flush the messages from the store.
	 *
	 * @return void
	 */
	protected function flush_messages() {
		delete_transient( '_awebooking_messages' );
	}

	/**
	 * Get the messages in the store.
	 *
	 * @return array|null
	 */
	protected function get_messages() {
		return get_transient( '_awebooking_messages' );
	}
}
