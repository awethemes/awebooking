<?php
namespace AweBooking\Model;

use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Support\Decimal;

class Rate extends WP_Object {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = Constants::PRICING_RATE;

	/**
	 * The attributes for this object.
	 *
	 * @var array
	 */
	protected $attributes = [
		'name'         => '',
		'group'        => '', // [ 'standard', 'room_rate' ].
		'order'        => 0,
		'parent_id'    => 0,

		'base_amount'  => 0,
		'min_los'      => 0,
		'max_los'      => 0,
	];

	/**
	 * An array of meta data mapped with attributes.
	 *
	 * @var array
	 */
	protected $maps = [
		'base_amount' => '_rate_base_amount',
		'min_los'     => '_minimum_los',
		'max_los'     => '_maximum_los',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'name'        => 'string',
		'order'       => 'integer',
		'min_los'     => 'integer',
		'max_los'     => 'integer',
		'base_amount' => 'float',
	];

	/**
	 * Constructor.
	 *
	 * @param integer $rate_id     Optional, rate ID.
	 * @param string  $object_type The object_type.
	 */
	public function __construct( $rate_id = 0, $object_type = Constants::PRICING_RATE ) {
		static::assert_object_type( $object_type );

		$this->object_type = $object_type;

		parent::__construct( $rate_id );
	}

	/**
	 * Assert the object_type.
	 *
	 * @param  string $object_type The object_type.
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	protected static function assert_object_type( $object_type ) {
		if ( ! in_array( $object_type, [ Constants::PRICING_RATE, Constants::ROOM_TYPE ] ) ) {
			throw new \InvalidArgumentException( 'Invalid object type' );
		}
	}

	/**
	 * Determines this is a standard rate.
	 *
	 * @return boolean
	 */
	public function is_standard_rate() {
		return $this->exists() && Constants::ROOM_TYPE == $this->object_type;
	}

	/**
	 * Get the rate name.
	 *
	 * @return string
	 */
	public function get_name() {
		return apply_filters( $this->prefix( 'get_name' ), $this['name'], $this );
	}

	/**
	 * Get the the rate order.
	 *
	 * @return string
	 */
	public function get_order() {
		return apply_filters( $this->prefix( 'get_order' ), $this['order'], $this );
	}

	/**
	 * Get the base price amount.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_base_amount() {
		return apply_filters( $this->prefix( 'get_base_amount' ), Decimal::create( $this['base_amount'] ), $this );
	}

	/**
	 * Get the minimum of stay night.
	 *
	 * @return int
	 */
	public function get_min_los() {
		return apply_filters( $this->prefix( 'get_min_los' ), $this['min_los'], $this );
	}

	/**
	 * Get the maximum of stay night.
	 *
	 * @return int
	 */
	public function get_max_los() {
		return apply_filters( $this->prefix( 'get_max_los' ), $this['max_los'], $this );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this['order'] = $this->instance->menu_order;
		$this['name']  = $this->instance->post_title;

		if ( $public_name = $this->get_meta( '_rate_label' ) ) {
			$this['name'] = $public_name;
		}

		if ( $this->is_standard_rate() ) {
			$this['order'] = 0;
			$this['group'] = 'standard';
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_insert() {
		// Prevent insert action if this is standard rate.
		if ( $this->is_standard_rate() ) {
			return;
		}

		$insert_id = wp_insert_post([
			'post_type'    => $this->object_type,
			'post_parent'  => $this['parent_id'],
			'menu_order'   => $this['order'],
			'post_title'   => $this['name'],
			'post_status'  => 'publish', // TODO: Maybe change to 'inherit'.
		], true );

		if ( ! is_wp_error( $insert_id ) ) {
			return $insert_id;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_update( array $dirty ) {
		if ( $this->is_dirty( 'order', 'name' ) ) {
			$updated = $this->update_the_post([
				'menu_order' => $this['order'],
				'post_title' => $this['name'],
			]);

			return ! is_wp_error( $updated );
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function perform_delete( $force ) {
		// Prevent delete action if this is standard rate.
		if ( $this->is_standard_rate() ) {
			return false;
		}

		parent::perform_delete( $force );
	}
}
