<?php
namespace AweBooking\Admin\Meta_Boxes;

use AweBooking\Room;
use AweBooking\Room_Type;

class Service_Meta_Boxes extends Meta_Boxes_Abstract {
	/**
	 * Post type ID to register meta-boxes.
	 *
	 * @var string
	 */
	protected $post_type = AweBooking::HOTEL_SERVICE;

	/**
	 * Constructor of class.
	 */
	public function __construct() {
		parent::__construct();

		$this->add_field([
			'name'      => esc_html__( 'Operation', 'awebooking' ),
			'id'        => 'operation',
			'type'      => 'select',
			'options'   => static::operation_options(),
			'sanitization_cb' => array( $this, 'sanitize_operation' ),
			'render_field_cb'   => array( $this, '_operation_field_callback' ),
		]);

		$this->add_field([
			'name' => esc_html__( 'Price', 'awebooking' ),
			'id'   => 'price',
			'type' => 'text_small',
			'validate'   => 'required|numeric:min:0',
			'sanitization_cb' => 'awebooking_sanitize_price',
			'show_on_cb' => '__return_false',
		]);

		$this->add_field([
			'name'      => esc_html__( 'Type', 'awebooking' ),
			'id'        => 'type',
			'type'      => 'select',
			'options'   => static::type_options(),
			'sanitization_cb' => array( $this, 'sanitize_type' ),
		]);
	}
}
