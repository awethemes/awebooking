<?php
namespace AweBooking;

use AweBooking\Pricing\Price;
use AweBooking\Support\WP_Object;
use AweBooking\Interfaces\Booking_Request;

class Service extends WP_Object {
	/* Constants */
	const OPTIONAL = 'optional';
	const MANDATORY = 'mandatory';

	/* Operation constants */
	const OP_ADD       = 'add';
	const OP_ADD_DAILY = 'add-daily';
	const OP_SUB       = 'sub';
	const OP_SUB_DAILY = 'sub-daily';
	const OP_INCREASE  = 'increase';
	const OP_DECREASE  = 'decrease';
	const OP_ADD_PERSON       = 'add-person';
	const OP_ADD_PERSON_DAILY = 'add-person-daily';

	/**
	 * This is the name of this object type.
	 *
	 * Normally is name of custom-post-type or custom-taxonomy.
	 *
	 * @var string
	 */
	protected $object_type = AweBooking::HOTEL_SERVICE;

	/**
	 * Type of object metadata is for (e.g., term, post).
	 *
	 * @var string
	 */
	protected $meta_type = 'term';

	/**
	 * The attributes for this object.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $attributes = [
		'name'        => '',
		'description' => '',
		'price'       => 0.0,
		'operation'   => 'add',
		'type'        => 'optional',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'price' => 'float',
	];

	/**
	 * An array of meta data mapped with attributes.
	 *
	 * @var array
	 */
	protected $maps = [
		'price',
		'operation',
		'type',
	];

	/**
	 * Create new Extra Service.
	 *
	 * @param integer $service Extra service ID or instance of WP_Term.
	 */
	public function __construct( $service = 0 ) {
		parent::__construct( $service );
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this->set_attr( 'name', $this->instance->name );
		$this->set_attr( 'description', $this->instance->description );
	}

	/**
	 * If this is optional service.
	 *
	 * @return boolean
	 */
	public function is_optional() {
		return static::OPTIONAL === $this->get_type();
	}

	/**
	 * If this is mandatory service.
	 *
	 * @return boolean
	 */
	public function is_mandatory() {
		return static::MANDATORY === $this->get_type();
	}

	/**
	 * Get service name.
	 *
	 * @return string
	 */
	public function get_name() {
		return apply_filters( $this->prefix( 'get_name' ), $this['name'], $this );
	}

	/**
	 * Get service description.
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( $this->prefix( 'get_description' ), $this['description'], $this );
	}

	/**
	 * The room-type title.
	 *
	 * @return Price
	 */
	public function get_price() {
		if ( in_array( $this->get_operation(), [ static::OP_INCREASE, static::OP_DECREASE ] ) ) {
			return new Price( 0 );
		}

		return new Price( $this['price'] );
	}

	public function get_operation() {
		return $this->get_attr( 'operation' );
	}

	public function get_value() {
		return $this->get_attr( 'price' );
	}

	public function get_type() {
		return $this->get_attr( 'type' );
	}

	public function get_price_label( Booking_Request $booking_request, $before_value = '', $after_value = '' ) {
		$label = '';

		switch ( $this->get_operation() ) {
			case self::OP_ADD:
				$label = sprintf( esc_html__( '%2$s + %1$s %3$s to price', 'awebooking' ), $this->get_price(), $before_value, $after_value );
				break;

			case self::OP_ADD_DAILY:
				$label = sprintf( esc_html__( '%2$s + %1$s x %4$s night(s) %3$s to price', 'awebooking' ), $this->get_price(), $before_value, $after_value, $booking_request->get_nights() );
				break;

			case self::OP_ADD_PERSON:
				$label = sprintf( esc_html__( '%2$s + %1$s x %4$s person %3$s to price', 'awebooking' ), $this->get_price(), $before_value, $after_value, $booking_request->get_people() );
				break;

			case self::OP_ADD_PERSON_DAILY:
				$label = sprintf( esc_html__( '%2$s + %1$s x %4$s person x %5$s night(s) %3$s to price', 'awebooking' ), $this->get_price(), $before_value, $after_value, $booking_request->get_people(), $booking_request->get_nights() );
				break;

			case self::OP_SUB:
				$label = sprintf( esc_html__( '%2$s - %1$s %3$s from price', 'awebooking' ), $this->get_price(), $before_value, $after_value );
				break;

			case self::OP_SUB_DAILY:
				$label = sprintf( esc_html__( '%2$s - %1$s x %4$s night(s) %3$s from price', 'awebooking' ), $this->get_price(), $before_value, $after_value, $booking_request->get_nights() );
				break;

			case self::OP_INCREASE:
				$label = sprintf( esc_html__( '%2$s + %1$s%% %3$s to price', 'awebooking' ), $this->get_value(), $before_value, $after_value );
				break;

			case self::OP_DECREASE:
				$label = sprintf( esc_html__( '%2$s - %1$s%% %3$s from price', 'awebooking' ), $this->get_value(), $before_value, $after_value );
				break;
		}

		return $label;
	}
}
