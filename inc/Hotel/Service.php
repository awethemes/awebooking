<?php
namespace AweBooking\Hotel;

use AweBooking\AweBooking;
use AweBooking\Pricing\Price;
use AweBooking\Support\WP_Object;

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
	 * @var string
	 */
	protected $object_type = AweBooking::HOTEL_SERVICE;

	/**
	 * WordPress type for object.
	 *
	 * @var string
	 */
	protected $wp_type = 'term';

	/**
	 * Type of object metadata is for.
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
		'operation'   => 'add',
		'value'       => 0,
		'type'        => 'optional',
	];

	/**
	 * An array of attributes mapped with metadata.
	 *
	 * @var array
	 */
	protected $maps = [
		'operation' => '_service_operation',
		'value'     => '_service_value',
		'type'      => '_service_type',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'value' => 'float',
	];

	/**
	 * Get service by given a slug.
	 *
	 * @param  string $slug Service slug.
	 * @return static
	 */
	public static function get_by_slug( $slug ) {
		$service = get_term_by( 'slug', $slug, AweBooking::HOTEL_SERVICE );
		if ( $service instanceof \WP_Term ) {
			return new static( $service->term_id );
		}
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['name'] = $this->instance->name;
		$this['description'] = $this->instance->description;
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
	 * Returns value.
	 *
	 * @return float
	 */
	public function get_value() {
		return apply_filters( $this->prefix( 'get_value' ), $this['value'], $this );
	}

	/**
	 * Returns service operation.
	 *
	 * @return string
	 */
	public function get_operation() {
		return apply_filters( $this->prefix( 'get_operation' ), $this['operation'], $this );
	}

	/**
	 * Returns "optional" or "mandatory".
	 *
	 * @return string
	 */
	public function get_type() {
		return apply_filters( $this->prefix( 'get_type' ), $this['type'], $this );
	}

	/**
	 * If this is optional service.
	 *
	 * @return bool
	 */
	public function is_optional() {
		return static::OPTIONAL === $this->get_type();
	}

	/**
	 * If this is mandatory service.
	 *
	 * @return bool
	 */
	public function is_mandatory() {
		return static::MANDATORY === $this->get_type();
	}

	/**
	 * Return describe label for display.
	 *
	 * @param  string $before_value  String before value.
	 * @param  string $after_value   String after value.
	 * @return string
	 */
	public function get_describe( $before_value = '', $after_value = '' ) {
		$label = '';

		switch ( $this->get_operation() ) {
			case Service::OP_ADD:
				$label = sprintf( esc_html__( '%2$s + %1$s %3$s to price', 'awebooking' ), $this->get_price(), $before_value, $after_value );
				break;

			case Service::OP_ADD_DAILY:
				$label = sprintf( esc_html__( '%2$s + %1$s x night %3$s to price', 'awebooking' ), $this->get_price(), $before_value, $after_value );
				break;

			case Service::OP_ADD_PERSON:
				$label = sprintf( esc_html__( '%2$s + %1$s x person %3$s to price', 'awebooking' ), $this->get_price(), $before_value, $after_value );
				break;

			case Service::OP_ADD_PERSON_DAILY:
				$label = sprintf( esc_html__( '%2$s + %1$s x person x night %3$s to price', 'awebooking' ), $this->get_price(), $before_value, $after_value );
				break;

			case Service::OP_SUB:
				$label = sprintf( esc_html__( '%2$s - %1$s %3$s from price', 'awebooking' ), $this->get_price(), $before_value, $after_value );
				break;

			case Service::OP_SUB_DAILY:
				$label = sprintf( esc_html__( '%2$s - %1$s x night %3$s from price', 'awebooking' ), $this->get_price(), $before_value, $after_value );
				break;

			case Service::OP_INCREASE:
				$label = sprintf( esc_html__( '%2$s + %1$s%% %3$s to price', 'awebooking' ), $this->get_value(), $before_value, $after_value );
				break;

			case Service::OP_DECREASE:
				$label = sprintf( esc_html__( '%2$s - %1$s%% %3$s from price', 'awebooking' ), $this->get_value(), $before_value, $after_value );
				break;
		}

		return $label;
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

		return new Price( $this['value'] );
	}

	public function get_price_label( Request $booking_request, $before_value = '', $after_value = '' ) {
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

	/**
	 * Run perform insert object into database.
	 *
	 * @see wp_insert_term()
	 *
	 * @return int|void
	 */
	protected function perform_insert() {
		$inserted = wp_insert_term( $this->get_name(), $this->object_type, [
			'description' => $this->get_description(),
		]);

		if ( is_wp_error( $inserted ) ) {
			return;
		}

		return $inserted['term_id'];
	}

	/**
	 * Run perform update object.
	 *
	 * @see wp_update_term()
	 *
	 * @param  array $dirty The attributes has been modified.
	 * @return bool|void
	 */
	protected function perform_update( array $dirty ) {
		if ( ! $this->is_dirty( 'name', 'description' ) ) {
			return true;
		}

		$updated = wp_update_term( $this->get_id(), $this->object_type, [
			'name'        => $this->get_name(),
			'description' => $this->get_description(),
		]);

		return ! is_wp_error( $updated );
	}
}
