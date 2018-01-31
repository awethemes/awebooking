<?php
namespace AweBooking\Admin\Settings;

use AweBooking\Admin\Admin_Settings;

class Reservation_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	public function registers( Admin_Settings $settings ) {
		$reservation = $settings->add_panel( 'reservation', [
			'title'      => esc_html__( 'Reservation', 'awebooking' ),
			'priority'   => 10,
			'capability' => 'manage_awebooking',
		]);

		$sources = $settings->add_section( 'reservation_sources', [
			'title'      => esc_html__( 'Sources', 'awebooking' ),
			'capability' => 'manage_awebooking',
		])->as_child_of( 'reservation' );

		$this->register_sources_fields( $sources );
	}

	/**
	 * Register the sources section fields.
	 *
	 * @param  Skeleton\CMB2\Section $sources Section instance.
	 * @return void
	 */
	protected function register_sources_fields( $sources ) {
		$sources->options['render_callback'] = function() {
			awebooking( 'admin_template' )->partial( 'sources/manager.php' );
		};
	}
}
