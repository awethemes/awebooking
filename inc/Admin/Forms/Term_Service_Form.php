<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Component\Form\Form_Builder;

class Term_Service_Form extends Form_Builder {
	/**
	 * Constructor.
	 *
	 * @param mixed $object The object data.
	 */
	public function __construct( $object ) {
		parent::__construct( 'payment-form', $object );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_fields() {
		if ( function_exists( 'wp_simple_iconfonts' ) ) {
			$this->add_field([
				'name'      => esc_html__( 'Icon', 'awebooking' ),
				'id'        => '_icon',
				'type'      => 'simple_iconfonts',
			]);
		}

		$this->add_field([
			'name'            => esc_html__( 'Operation', 'awebooking' ),
			'id'              => '_service_operation',
			'type'            => 'select',
			// 'options'         => awebooking( 'setting' )->get_service_operations(),
		]);

		$this->add_field([
			'id'              => '_service_value',
			'type'            => 'text_small',
			'validate'        => 'required|numeric:min:0',
			'sanitization_cb' => 'awebooking_sanitize_price',
		]);

		$this->add_field([
			'name'      => esc_html__( 'Type', 'awebooking' ),
			'id'        => '_service_type',
			'type'      => 'select',
			'options'   => [
				'optional'  => esc_html__( 'Optional', 'awebooking' ),
				'mandatory' => esc_html__( 'Mandatory', 'awebooking' ),
			],
		]);
	}
}
