<?php
namespace AweBooking\Admin\Settings;

use AweBooking\Support\WP_Data;
use AweBooking\Admin\Admin_Settings;

class Appearance_Setting extends Abstract_Setting {
	/**
	 * The setting ID.
	 *
	 * @var string
	 */
	protected $form_id = 'appearance';

	/**
	 * Get the setting label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Appearance', 'awebooking' );
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	public function setup_fields() {
		$this->add_field([
			'id'    => '__datepicker_title',
			'type'  => 'title',
			'name'  => esc_html__( 'Date Picker', 'awebooking' ),
		]);

		$this->add_field([
			'id'              => 'sdasdsad',
			'type'            => 'text',
			'name'            => esc_html__( 'City', 'awebooking' ),
			'desc'            => esc_html__( 'The city in which your hotel is located.', 'awebooking' ),
			'sanitization_cb' => 'abrs_sanitize_text',
			'tooltip'         => true,
		]);
	}
}
