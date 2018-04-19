<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Constants;
use AweBooking\Support\WP_Data;
use AweBooking\Support\Carbonate;
use AweBooking\Component\Form\Form_Builder;

class Bulk_Price_Form extends Form_Builder {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'bulk_room_price', 0, 'static' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_fields() {
		$this->add_field([
			'id'                => 'bulk_room_types',
			'type'              => 'multicheck',
			'name'              => esc_html__( 'Select room type(s)', 'awebooking' ),
			'select_all_button' => true,
			'sanitization_cb'   => 'wp_parse_id_list',
			'options_cb'        => WP_Data::cb( 'posts', [
				'post_type'      => Constants::ROOM_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			]),
		]);

		$this->add_field([
			'id'          => 'bulk_date',
			'type'        => 'abrs_dates',
			'name'        => esc_html__( 'Select dates', 'awebooking' ),
			'show_js'     => false,
			'default'     => [ Carbonate::today()->format( 'Y-m-d' ), Carbonate::tomorrow()->format( 'Y-m-d' ) ],
		]);

		$this->add_field([
			'id'                => 'bulk_days',
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
			'id'          => 'bulk_operator',
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
			'id'              => 'bulk_amount',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Amount', 'awebooking' ),
			'attributes'      => [ 'tabindex' => '1' ],
			'sanitization_cb' => 'abrs_sanitize_decimal',
		]);
	}
}
