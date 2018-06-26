<?php
namespace AweBooking\Admin\Settings;

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
		]);

		$this->add_field([
			'id'         => 'purchase_code',
			'type'       => 'text',
			'name'       => esc_html__( 'API Code', 'awebooking' ),
			'save_field' => false,
		]);
	}
}
