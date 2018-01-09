<?php
namespace AweBooking\Model;

use AweBooking\Factory;
use AweBooking\Constants;
use Roomify\Bat\Unit\UnitInterface;
use AweBooking\Booking\BAT\Unit_Trait;

class Room extends WP_Object implements UnitInterface {
	use Unit_Trait;

	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'awebooking_rooms';

	/**
	 * WordPress type for object.
	 *
	 * @var string
	 */
	protected $wp_type = 'awebooking_rooms';

	/**
	 * This object does not support metadata.
	 *
	 * @var false
	 */
	protected $meta_type = false;

	/**
	 * The attributes for this object.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $attributes = [
		'name'      => '',
		'room_type' => 0,
		'order'     => 0,
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'name'      => 'string',
		'room_type' => 'integer',
		'order'     => 'integer',
	];

	/**
	 * Constructor.
	 *
	 * @param int $room_id The room ID.
	 */
	public function __construct( $room_id = 0 ) {
		parent::__construct( $room_id );

		// By default, room state is alway available.
		$this->setDefaultValue( Constants::STATE_AVAILABLE );
	}

	/**
	 * Get list room by room-type ID(s).
	 *
	 * @param  int|array $ids The room-type ID(s).
	 * @return array|null
	 */
	public static function get_by_room_type( $ids ) {
		global $wpdb;

		$ids = is_int( $ids ) ? [ $ids ] : $ids;
		$ids = apply_filters( 'awebooking/rooms/get_by_room_type', $ids );

		if ( is_array( $ids ) ) {
			$ids = implode( "', '", array_map( 'esc_sql', $ids ) );
			$query = "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` IN ('{$ids}')";
		}

		// @codingStandardsIgnoreLine
		return $wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * Get room name.
	 *
	 * @return string
	 */
	public function get_name() {
		return apply_filters( $this->prefix( 'get_name' ), $this['name'], $this );
	}

	/**
	 * The the room order.
	 *
	 * @return string
	 */
	public function get_order() {
		return apply_filters( $this->prefix( 'get_order' ), $this['order'], $this );
	}

	/**
	 * Get room type instance.
	 *
	 * @return Room_Type
	 */
	public function get_room_type() {
		return Factory::get_room_type( $this['room_type'] );
	}

	/**
	 * Set the room_type ID.
	 *
	 * @param int $room_type The room_type ID.
	 */
	public function set_room_type( $room_type ) {
		$room_type = $room_type instanceof Room_Type ? $room_type->get_id() : $room_type;

		$this->attributes['room_type'] = $room_type;
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['name']      = $this->instance['name'];
		$this['order']     = isset( $this->instance['order'] ) ? absint( $this->instance['order'] ) : 0;
		$this['room_type'] = absint( $this->instance['room_type'] );
	}

	/**
	 * Clean object cache after saved.
	 *
	 * @return void
	 */
	protected function clean_cache() {
		wp_cache_delete( $this->get_id(), Constants::CACHE_RAW_ROOM_UNIT );
		wp_cache_delete( $this['room_type'], Constants::CACHE_ROOMS_IN_ROOM_TYPE );
	}

	/**
	 * Run perform insert object into database.
	 *
	 * @return int|void
	 */
	protected function perform_insert() {
		global $wpdb;

		// We need a room-type present.
		if ( empty( $this->attributes['room_type'] ) ) {
			return;
		}

		$wpdb->insert( $wpdb->prefix . 'awebooking_rooms',
			$this->only( 'name', 'room_type', 'order' ),
			[ '%s', '%d', '%d' ]
		);

		return absint( $wpdb->insert_id );
	}

	/**
	 * Run perform update object.
	 *
	 * @param  array $dirty The attributes has been modified.
	 * @return bool|void
	 */
	protected function perform_update( array $dirty ) {
		global $wpdb;

		// We need a room-type present for the update.
		if ( empty( $this->attributes['room_type'] ) ) {
			return;
		}

		$updated = $wpdb->update( $wpdb->prefix . 'awebooking_rooms',
			$this->only( 'name', 'room_type', 'order' ),
			[ 'id' => $this->get_id() ]
		);

		return false !== $updated;
	}

	/**
	 * Perform delete object.
	 *
	 * @param  bool $force Force delete or not.
	 * @return bool
	 */
	protected function perform_delete( $force ) {
		global $wpdb;

		$deleted = $wpdb->delete( $wpdb->prefix . 'awebooking_rooms', [ 'id' => $this->get_id() ], '%d' );

		return false !== $deleted;
	}

	/**
	 * Refresh the current instance with existing room data.
	 *
	 * @param  array $data The room database attributes data.
	 * @return $this
	 */
	public function with_instance( array $data ) {
		$this->id = absint( $data['id'] );
		$this->exists = true;

		$this->set_instance( $data );

		$this->setup();
		$this->sync_original();

		return $this;
	}

	/**
	 * Setup WP Core Object based on ID and object-type.
	 *
	 * @return void
	 */
	protected function setup_instance() {
		global $wpdb;

		// Try get in the cache.
		$the_room = wp_cache_get( $this->get_id(), Constants::CACHE_RAW_ROOM_UNIT );

		if ( false === $the_room ) {
			// Get the room in database.
			$the_room = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` = %d LIMIT 1", $this->get_id() ),
				ARRAY_A
			);

			// Do nothing if not found the room.
			if ( is_null( $the_room ) ) {
				return;
			}

			wp_cache_add( $the_room['id'], $the_room, Constants::CACHE_RAW_ROOM_UNIT );
		}

		$this->set_instance( $the_room );
	}
}
