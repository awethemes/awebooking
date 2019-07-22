<?php
namespace AweBooking\Admin\Settings;

class Availability_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this->form_id  = 'availability';
		$this->label    = esc_html__( 'Availability', 'awebooking' );
		$this->priority = 15;
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	public function setup_fields() {
		$this->add_field( [
			'id'   => '__availability_title',
			'type' => 'title',
			'name' => esc_html__( 'Availability', 'awebooking' ),
		] );

		$this->add_field( [
			'id'              => 'display_datepicker_minnights',
			'type'            => 'text',
			'name'            => esc_html__( 'Minimum nights', 'awebooking' ),
			'desc'            => esc_html__( 'Minimum nights required to select a range of dates.', 'awebooking' ),
			'default'         => 1,
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type'  => 'number',
				'min'   => 1,
				'step'  => 1,
				'style' => 'width: 100px;',
			],
		] );

		$this->add_field( [
			'id'              => 'display_datepicker_maxnights',
			'type'            => 'text',
			'name'            => esc_html__( 'Maximum nights', 'awebooking' ),
			'desc'            => esc_html__( 'Maximum nights required to select a range of dates.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type'  => 'number',
				'step'  => 1,
				'min'   => 0,
				'style' => 'width: 100px;',
			],
		] );

		$this->add_field( [
			'id'              => 'display_datepicker_mindate',
			'type'            => 'text',
			'name'            => esc_html__( 'Limit available days from today', 'awebooking' ),
			'desc'            => esc_html__( 'A number of days from today are available for making the reservation. For example 2 represents two days from today. All the dates before the additional date will be disabled. When set to "0", there is no minimum.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type'  => 'number',
				'step'  => 1,
				'style' => 'width: 100px;',
			],
		] );

		$this->add_field( [
			'id'   => 'display_datepicker_disabledates',
			'type' => 'text',
			'name' => esc_html__( 'Disabled dates', 'awebooking' ),
			'desc' => esc_html__( 'Enter dates by ", " separating values in this format: `Y-m-d`. All the dates passed to the list will be disabled.', 'awebooking' ),
		] );

		$this->add_field( [
			'id'                => 'display_datepicker_disabledays',
			'type'              => 'multicheck_inline',
			'name'              => esc_html__( 'Disabled week days', 'awebooking' ),
			'desc'              => esc_html__( 'All the days passed to the list will be disabled.', 'awebooking' ),
			'select_all_button' => false,
			'options_cb'        => 'abrs_days_of_week',
			'sanitization_cb'   => 'abrs_sanitize_days_of_week',
		] );
	}
}
