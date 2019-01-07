<?php

namespace AweBooking\Admin\Providers;

use AweBooking\Support\Service_Provider;

class Permalink_Service_Provider extends Service_Provider {
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
	 * Handle save the permalink settings.
	 *
	 * @param \WP_Screen $current_screen The current screen.
	 *
	 * @access private
	 */
	public function save( $current_screen ) {
		if ( 'options-permalink' !== $current_screen->id || ! isset( $_POST['permalink_structure'] ) ) {
			return;
		}

		check_admin_referer( 'update-permalink' );

		if ( ! empty( $_POST['room_type_slug'] ) ) {
			$permalink = sanitize_title( wp_unslash( $_POST['room_type_slug'] ) );
			update_option( 'awebooking_room_type_permalink', $permalink, true );
		} else {
			delete_option( 'awebooking_room_type_permalink' );
		}

		if ( ! empty( $_POST['hotel_slug'] ) ) {
			$permalink = sanitize_title( wp_unslash( $_POST['hotel_slug'] ) );
			update_option( 'awebooking_hotel_permalink', $permalink, true );
		} else {
			delete_option( 'awebooking_hotel_permalink' );
		}
	}

	/**
	 * Initialize the permalink settings.
	 *
	 * @access private
	 */
	public function settings() {
		add_settings_field( 'awebooking_room_type_permalink',
			esc_html__( 'Room type base', 'awebooking' ),
			$this->input_callback( 'room_type_slug', get_option( 'awebooking_room_type_permalink' ), 'room_type' ),
			'permalink', 'optional'
		);

		if ( abrs_multiple_hotels() ) {
			add_settings_field( 'awebooking_hotel_permalink',
				esc_html__( 'Hotel base', 'awebooking' ),
				$this->input_callback( 'hotel_slug', get_option( 'awebooking_hotel_permalink' ), 'hotel_location' ),
				'permalink', 'optional'
			);
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
