<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Component\Form\Form_Builder;
use AweBooking\Model\Service;

class Service_Data_Form extends Form_Builder {
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
				'name'      => esc_html__( 'Icon', 'awebooking' ),
				'id'        => 'icon',
				'type'      => 'simple_iconfonts',
			]);
		}

		$this->add_field([
			'name'            => esc_html__( 'Operation', 'awebooking' ),
			'id'              => 'operation',
			'type'            => 'select',
			'options'         => Service::get_operations(),
		]);

		$this->add_field([
			'name'            => esc_html__( 'Value', 'awebooking' ),
			'id'              => 'value',
			'type'            => 'text_small',
			'validate'        => 'required|numeric:min:0',
		]);

		$this->add_field([
			'name'            => esc_html__( 'Description', 'awebooking' ),
			'id'              => 'description',
			'type'            => 'textarea',
		]);
	}
}
