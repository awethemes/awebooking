<?php
namespace AweBooking\Admin\Providers;

use AweBooking\Support\Service_Provider;

class Permalink_Service_Provider extends Service_Provider {
	/* Constants */
	const ROOM_TYPE_SLUG = 'awebooking_room_type_permalink';

	/**
	 * Init the hooks.
	 *
	 * @access private
	 */
	public function init() {
		add_action( 'admin_init', [ $this, 'settings' ] );
		add_action( 'current_screen', [ $this, 'save' ] );
	}

	/**
	 * Initialize the permalink settings.
	 *
	 * @access private
	 */
	public function settings() {
		add_settings_field(
			static::ROOM_TYPE_SLUG,
			esc_html__( 'Room type base', 'awebooking' ),
			$this->input_callback( 'room_type_slug', get_option( static::ROOM_TYPE_SLUG ), 'room_type' ),
			'permalink',
			'optional'
		);
	}

	/**
	 * Handle save the permalink settings.
	 *
	 * @access private
	 */
	public function save() {
		$screen = get_current_screen();

		if ( 'options-permalink' !== $screen->id || ! isset( $_POST['permalink_structure'], $_POST['room_type_slug'] ) ) {
			return;
		}

		check_admin_referer( 'update-permalink' );

		if ( ! empty( $_POST['room_type_slug'] ) ) {
			$permalink = sanitize_title( wp_unslash( $_POST['room_type_slug'] ) );
			update_option( static::ROOM_TYPE_SLUG, $permalink, true );
		} else {
			delete_option( static::ROOM_TYPE_SLUG );
		}
	}

	/**
	 * Returns the input callback.
	 *
	 * @param  string $name        The input name.
	 * @param  string $value       The input value.
	 * @param  string $placeholder The input placeholder.
	 * @return \Closure
	 */
	protected function input_callback( $name, $value, $placeholder = '' ) {
		return function() use ( $name, $value, $placeholder ) {
			printf( '<input name="%1$s" type="text" class="regular-text code" value="%2$s" placeholder="%3$s">', esc_attr( $name ), esc_attr( $value ), esc_attr( $placeholder ) );
		};
	}
}
