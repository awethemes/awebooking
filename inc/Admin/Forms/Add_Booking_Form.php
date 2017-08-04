<?php
namespace AweBooking\Admin\Forms;

use AweBooking\AweBooking;

class Add_Booking_Form extends Form_Abstract {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'add_booking_form';

	/**
	 * Register fields in to the CMB2.
	 *
	 * @return void
	 */
	protected function register_fields() {
		$this->add_field([
			'id'          => 'check_in_out',
			'type'        => 'date_range',
			'name'        => esc_html__( 'Check-in/out', 'awebooking' ),
			'validate'    => 'required',
			'attributes'  => [ 'placeholder' => AweBooking::DATE_FORMAT, 'required' => true ],
			'date_format' => AweBooking::DATE_FORMAT,
		]);

		$this->add_field([
			'id'          => 'add_room',
			'type'        => 'select',
			'name'        => esc_html__( 'Room', 'awebooking' ),
			'validate'    => 'required|integer|min:1',
			'sanitization_cb'  => 'absint',
			'show_option_none' => esc_html__( 'Choose a room...', 'awebooking' ),
		]);

		$this->add_field([
			'id'               => 'adults',
			'type'             => 'select',
			'name'             => esc_html__( 'Number of adults', 'awebooking' ),
			'default'          => 1,
			'validate'         => 'required|numeric|min:1',
			'validate_label'   => esc_html__( 'Adults', 'awebooking' ),
			'sanitization_cb'  => 'absint',
		]);

		$this->add_field([
			'id'              => 'children',
			'type'            => 'select',
			'name'            => esc_html__( 'Number of children', 'awebooking' ),
			'default'         => 0,
			'validate'        => 'required|numeric|min:0',
			'sanitization_cb' => 'absint',
		]);

		$this->add_field([
			'id'              => 'price',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Price (per night)', 'awebooking' ),
			'validate'        => 'required|numeric:min:0',
			'sanitization_cb' => 'awebooking_sanitize_price',
		]);
	}
}
