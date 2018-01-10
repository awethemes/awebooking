<?php
namespace AweBooking\Model;

use AweBooking\Factory;
use AweBooking\Constants;
use Skeleton\Support\Validator;

use Roomify\Bat\Unit\UnitInterface;
use AweBooking\Booking\BAT\Unit_Trait;
use AweBooking\Deprecated\Model\Room_Deprecated;

class Room extends WP_Object implements UnitInterface {
	use Unit_Trait, Room_Deprecated;

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
	 * Refresh the current instance with existing room data.
	 *
	 * @param  array $data The room database attributes data.
	 * @return $this
	 */
	public function with_instance( array $data ) {
		static::validate_raw_data( $data );

		$this->id = absint( $data['id'] );

		$this->set_instance( $data );
		$this->exists = true;

		$this->setup();
		$this->sync_original();

		return $this;
	}

	/**
	 * Validate the room raw data.
	 *
	 * @param  array $data The room data.
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	protected static function validate_raw_data( array $data ) {
		$validation = new Validator( $data, [
			'id'        => 'required|integer|min:1',
			'name'      => 'required',
			'order'     => 'required|integer',
			'room_type' => 'required|integer|min:1',
		]);

		if ( $validation->fails() ) {
			throw new \InvalidArgumentException( 'The room data is invalid.' );
		}
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
		$this->attributes['room_type'] = ( $room_type instanceof Room_Type )
			? $room_type->get_id()
			: absint( $room_type );
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
		wp_cache_delete( $this->get_id(), Constants::CACHE_ROOM_UNIT );
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

			wp_cache_add( (int) $the_room['id'], $the_room, Constants::CACHE_RAW_ROOM_UNIT );
		}

		$this->set_instance( $the_room );
	}
}
