<?php
namespace AweBooking\Admin\Settings;

use AweBooking\Admin\Admin_Settings;

class Premium_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	public function registers( Admin_Settings $settings ) {
		$premium = $settings->add_section( 'premium', [
			'title'      => esc_html__( 'Premium', 'awebooking' ),
			'capability' => 'manage_awebooking',
			'priority'   => 100,
		]);

		$premium->add_field( array(
			'id'   => '__premium___',
			'type' => 'title',
			'name' => esc_html__( 'Premium', 'awebooking' ),
		));

		$premium->add_field( array(
			'id'         => 'purchase_code',
			'type'       => 'text',
			'name'       => esc_html__( 'Purchase code', 'awebooking' ),
			'validate'   => 'length:45',
		));
	}
}
