<?php
namespace AweBooking\Admin\Settings;

use AweBooking\Admin\Admin_Settings;

class Premium_Setting extends Abstract_Setting {
	/**
	 * The setting ID.
	 *
	 * @var string
	 */
	protected $form_id = 'premium';

	/**
	 * Get the setting label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Premium', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$this->add_field([
			'id'   => '__premium_title',
			'type' => 'title',
			'name' => esc_html__( 'Premium', 'awebooking' ),
			'desc' => esc_html__( 'Here you can manager premium', 'awebooking' ),
		]);

		$this->add_field([
			'id'         => 'purchase_code',
			'type'       => 'text',
			'name'       => esc_html__( 'API Code', 'awebooking' ),
			'save_field' => false,
		]);
	}
}
