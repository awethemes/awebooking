<?php
namespace AweBooking\Admin;

class Permalink_Settings {
	/**
	 * The unique key for custom rewrite slug.
	 *
	 * @var string
	 */
	protected $permalink_key = 'awebooking_room_type_permalink';

	/**
	 * ADmin permalink settings.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'init_permalink_settings' ) );
		add_action( 'current_screen', array( $this, 'save_permalink_settings' ) );
	}

	/**
	 * Initialize the permalink settings.
	 */
	public function init_permalink_settings() {
		$input_callback = function() {
			printf(
				'<input name="%1$s" type="text" class="regular-text code" value="%2$s" placeholder="room_type">',
				esc_attr( $this->permalink_key ),
				esc_attr( get_option( $this->permalink_key ) )
			);
		};

		add_settings_field(
			$this->permalink_key,
			esc_html__( 'Room type base', 'awebooking' ),
			$input_callback, 'permalink', 'optional'
		);
	}

	/**
	 * Save the permalink settings.
	 */
	public function save_permalink_settings() {
		$screen = get_current_screen();

		if ( ! $screen || 'options-permalink' !== $screen->id ) {
			return;
		}

		if ( isset( $_POST['permalink_structure'] ) && ! empty( $_POST[ $this->permalink_key ] ) ) {
			check_admin_referer( 'update-permalink' );

			$permalink = sanitize_text_field( $_POST[ $this->permalink_key ] );
			update_option( $this->permalink_key, $permalink );
		}
	}
}
