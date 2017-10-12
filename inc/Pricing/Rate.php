<?php
namespace AweBooking\Pricing;

use AweBooking\AweBooking;
use AweBooking\Hotel\Room_Type;
use AweBooking\Support\WP_Object;
use AweBooking\Booking\BAT\Unit_Trait;
use Roomify\Bat\Unit\UnitInterface as Unit_Interface;

class Rate extends WP_Object implements Unit_Interface {
	use Unit_Trait;

	/**
	 * The room-type parent.
	 *
	 * @var Room_Type
	 */
	protected $room_type;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = AweBooking::PRICING_RATE;

	/**
	 * The attributes for this object.
	 *
	 * @var array
	 */
	protected $attributes = [
		'name'       => '',
		'order'      => 0,
		'base_price' => 0,
	];

	/**
	 * An array of meta data mapped with attributes.
	 *
	 * @var array
	 */
	protected $maps = [
		'base_price' => '_base_price',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'name'       => 'string',
		'base_price' => 'float',
		'order'      => 'integer',
	];

	/**
	 * Create a rate price.
	 *
	 * @param integer   $rate_id   Optional, rate ID.
	 * @param Room_Type $room_type Room-type parent.
	 */
	public function __construct( $rate_id = 0, Room_Type $room_type = null ) {
		$this->room_type = $room_type;

		// If we pass an rate ID same as room-type, switch object_type.
		if ( $room_type && $rate_id && $rate_id === $room_type->get_id() ) {
			$this->object_type = AweBooking::ROOM_TYPE;
		}

		parent::__construct( $rate_id );
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		if ( $this->is_standard_rate() ) {
			$this['name']       = esc_html__( 'Standard', 'awebooking' );
			$this['base_price'] = $this->room_type['base_price'];
			$this['order']      = 0;
		} else {
			$this['name']  = $this->instance->post_title;
			$this['order'] = $this->instance->menu_order;
		}
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
	 * The the rate order number.
	 *
	 * @return string
	 */
	public function get_order() {
		return apply_filters( $this->prefix( 'get_order' ), $this['order'], $this );
	}

	/**
	 * Get the base price of rate.
	 *
	 * @return Price
	 */
	public function get_base_price() {
		return apply_filters( $this->prefix( 'get_base_price' ), new Price( $this['base_price'] ), $this );
	}

	/**
	 * Determines this is a standard rate.
	 *
	 * @return boolean
	 */
	public function is_standard_rate() {
		return $this->room_type && $this->get_id() === $this->room_type->get_id();
	}

	/**
	 * Get the Unit default value.
	 *
	 * @overwrite Unit_Trait::getDefaultValue()
	 *
	 * @return int
	 */
	public function getDefaultValue() {
		return $this->get_base_price()->to_integer();
	}

	/**
	 * Run perform insert object into database.
	 *
	 * @see wp_insert_post()
	 *
	 * @return bool
	 */
	protected function perform_insert() {
		if ( ! $this->room_type ) {
			return;
		}

		$insert_id = wp_insert_post([
			'post_type'    => $this->object_type,
			'post_parent'  => $this->room_type->get_id(),
			'menu_order'   => $this['order'],
			'post_title'   => $this['name'],
			'post_status'  => 'publish', // TODO: Maybe change to 'inherit'.
		], true );

		if ( ! is_wp_error( $insert_id ) ) {
			return $insert_id;
		}
	}

	/**
	 * Run perform update object.
	 *
	 * @see WP_Object::update_the_post()
	 *
	 * @param  array $dirty The attributes changed.
	 * @return bool
	 */
	protected function perform_update( array $dirty ) {
		if ( $this->is_standard_rate() ) {
			return false;
		}

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
	 * Perform delete object.
	 *
	 * @param  bool $force Force delete or not.
	 * @return bool
	 */
	protected function perform_delete( $force ) {
		if ( $this->is_standard_rate() ) {
			return false;
		}

		parent::perform_delete( $force );
	}
}
