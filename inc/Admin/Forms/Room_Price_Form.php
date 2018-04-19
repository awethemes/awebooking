<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Support\Carbonate;
use AweBooking\Component\Form\Form_Builder;

class Room_Price_Form extends Form_Builder {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'room_price', 0, 'static' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_fields() {
		$this->add_field([
			'id'          => 'date',
			'type'        => 'abrs_dates',
			'name'        => esc_html__( 'Select dates', 'awebooking' ),
			'show_js'     => false,
			'default'     => [ '{{ data.startDate }}', '{{ data.endDate }}' ],
			'attributes'  => [ 'tabindex' => '-1' ],
		]);

		$this->add_field([
			'id'                => 'days',
			'type'              => 'multicheck_inline',
			'name'              => esc_html__( 'Apply on days', 'awebooking' ),
			'default'           => [ 0, 1, 2, 3, 4, 5, 6 ],
			'attributes'        => [ 'tabindex' => '-1' ],
			'select_all_button' => false,
			'options_cb'        => function() {
				return abrs_days_of_week( 'abbrev' );
			},
		]);

		$this->add_field([
			'id'          => 'operator',
			'type'        => 'select',
			'name'        => esc_html__( 'Operator', 'awebooking' ),
			'default'     => 'replace',
			'attributes'  => [ 'tabindex' => '-1' ],
			'options'     => [
				'replace'  => esc_html__( 'Replace', 'awebooking' ),
				'add'      => esc_html__( 'Add', 'awebooking' ),
				'subtract' => esc_html__( 'Subtract', 'awebooking' ),
				'multiply' => esc_html__( 'Multiply', 'awebooking' ),
				'divide'   => esc_html__( 'Divide', 'awebooking' ),
				'increase' => esc_html__( 'Increase', 'awebooking' ),
				'decrease' => esc_html__( 'Decrease', 'awebooking' ),
			],
		]);

		$this->add_field([
			'id'              => 'amount',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Amount', 'awebooking' ),
			'default'         => '{{ data.amount }}',
			'attributes'      => [ 'tabindex' => '1' ],
			'sanitization_cb' => 'abrs_sanitize_decimal',
		]);
	}
}
