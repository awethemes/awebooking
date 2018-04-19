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
			'id'    => '__display_datepicker_title',
			'type'  => 'title',
			'name'  => esc_html__( 'Date Picker', 'awebooking' ),
		]);

		$this->add_field([
			'id'              => 'display_datepicker_mindates',
			'type'            => 'text',
			'name'            => esc_html__( 'Minimum dates', 'awebooking' ),
			'desc'            => esc_html__( 'The minimum selectable date. When set to null, there is no minimum. A number of days from today. For example 2 represents two days from today. All the dates before the additional date will be disabled.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type' => 'number',
				'step' => 1,
			],
		]);

		$this->add_field([
			'id'              => 'display_datepicker_maxdates',
			'type'            => 'text',
			'name'            => esc_html__( 'Maximum dates', 'awebooking' ),
			'desc'         => esc_html__( 'The maximum selectable date. When set to null, there is no maximum. A number of days from today. For example 2 represents two days from today. All the dates after the additional date will be disabled.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type' => 'number',
				'step' => 1,
			],
		]);

		$this->add_field([
			'id'              => 'display_datepicker_minnights',
			'type'            => 'text',
			'name'            => esc_html__( 'Minimum nights', 'awebooking' ),
			'desc'            => esc_html__( 'Minimum nights required to select a range of dates.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type' => 'number',
				'step' => 1,
			],
		]);

		$this->add_field([
			'id'              => 'display_datepicker_maxnights',
			'type'            => 'text',
			'name'            => esc_html__( 'Maximum nights', 'awebooking' ),
			'desc'            => esc_html__( 'Maximum nights required to select a range of dates.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type' => 'number',
				'step' => 1,
			],
		]);

		$this->add_field([
			'id'              => 'display_datepicker_disabledates',
			'type'            => 'text',
			'name'            => esc_html__( 'Disabled dates', 'awebooking' ),
			'desc'            => esc_html__( 'Enter dates by "," separating values in this format: `YYYY-MM-DD`. All the dates passed to the list will be disabled.', 'awebooking' ),
		]);

		$this->add_field([
			'id'                => 'display_datepicker_disabledays',
			'type'              => 'multicheck_inline',
			'name'              => esc_html__( 'Disabled days', 'awebooking' ),
			'desc'              => esc_html__( 'All the days passed to the list will be disabled.', 'awebooking' ),
			'options_cb'        => 'abrs_week_days',
			'select_all_button' => false,
		]);

		$this->add_field([
			'id'              => 'display_datepicker_months',
			'type'            => 'select',
			'name'            => esc_html__( 'The number of months displayed', 'awebooking' ),
			'desc'            => esc_html__( 'Display on month or two months.', 'awebooking' ),
			'options'         => [ 1, 2 ],
		]);
	}
}
