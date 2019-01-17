<?php

namespace AweBooking\Admin\Forms;

use AweBooking\Component\Form\Form;
use AweBooking\Model\Service;

class Service_Data_Form extends Form {
	/**
	 * Constructor.
	 *
	 * @param mixed $object The object data.
	 */
	public function __construct( $object ) {
		parent::__construct( 'service-data-form', $object );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup_fields() {
		if ( function_exists( 'wp_simple_iconfonts' ) ) {
			$this->add_field([
				'name' => esc_html__( 'Icon', 'awebooking' ),
				'id'   => 'icon',
				'type' => 'simple_iconfonts',
			]);
		}

		$this->add_field([
			'name'       => esc_html__( 'Operation', 'awebooking' ),
			'id'         => 'operation',
			'type'       => 'select',
			'options_cb' => 'abrs_get_service_operations',
		]);

		$this->add_field([
			'name'     => esc_html__( 'Amount', 'awebooking' ),
			'id'       => 'amount',
			'type'     => 'text_small',
			'validate' => 'required|numeric:min:0',
			'default'  => 0,
		]);

		$this->add_field([
			'name' => esc_html__( 'Description', 'awebooking' ),
			'id'   => 'description',
			'type' => 'textarea',
		]);

		$this->add_field([
			'name' => esc_html__( 'Inventory', 'awebooking' ),
			'id'   => '__inventory__',
			'type' => 'title',
		]);

		$this->add_field([
			'name' => esc_html__( 'Quantity selectable?', 'awebooking' ),
			'id'   => 'quantity_selectable',
			'type' => 'toggle',
		]);

		/*$this->add_field([
			'name' => esc_html__( 'Manage stock', 'awebooking' ),
			'id'   => 'manage_stock',
			'type' => 'toggle',
		]);*/

		$this->add_field([
			'name'    => esc_html__( 'Stock status', 'awebooking' ),
			'id'      => 'stock_status',
			'type'    => 'select',
			'options' => Service::get_stock_statuses(),
		]);

		/*$this->add_field([
			'name'       => esc_html__( 'Stock quantity', 'awebooking' ),
			'id'         => 'stock_quantity',
			'type'       => 'text_small',
			'validate'   => 'required|numeric:min:0',
			'attributes' => [
				'type' => 'number',
				'data-bind' => 'visible: data.manage_stock()',
			],
		]);*/
	}
}
