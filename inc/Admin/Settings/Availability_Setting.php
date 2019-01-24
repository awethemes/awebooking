<?php
namespace AweBooking\Admin\Settings;

class Availability_Setting extends Abstract_Setting {
	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this->form_id  = 'availability';
		$this->label    = esc_html__( 'Availability', 'awebooking' );
		$this->priority = 20;
	}

	/**
	 * Setup the fields.
	 *
	 * @return void
	 */
	public function setup_fields() {
		$this->add_field([
			'id'   => '__availability_title',
			'type' => 'title',
			'name' => esc_html__( 'Availability', 'awebooking' ),
		]);

		$this->add_field( [
			'id'                => 'availability_allowed_checkin_days',
			'type'              => 'multicheck_inline',
			'name'              => esc_html__( 'Allowed check-in days', 'awebooking' ),
			'desc'              => esc_html__( 'All the days passed to the list will be abled.', 'awebooking' ),
			'options_cb'        => 'abrs_days_of_week',
			'default'           => [ 0, 1, 2, 3, 4, 5, 6 ],
			'select_all_button' => false,
			'tooltip'           => true,
			'grid_row'          => true,
		]);

		$this->add_field([
			'id'              => 'availability_period_bookable',
			'type'            => 'text',
			'name'            => esc_html__( 'Period bookable', 'awebooking' ),
			'desc'            => esc_html__( 'When set to "0", there is unlimit. A number of days from today. For example 7 represents seven days from today. All the dates after the additional date will be disabled.', 'awebooking' ),
			'sanitization_cb' => 'absint',
			'attributes'      => [
				'type'  => 'number',
				'step'  => 1,
				'style' => 'width: 100px;',
			],
			'tooltip'         => true,
			'grid_row'        => true,
		]);
	}
}
