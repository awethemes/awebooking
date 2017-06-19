<?php
namespace AweBooking;

use AweBooking\Support\WP_Object;
use Roomify\Bat\Unit\UnitInterface;

class Room extends WP_Object implements UnitInterface {
	use BAT\Traits\Unit_Trait;

	/**
	 * Name of object type.
	 *
	 * NOTE: This's not standard WP object type.
	 *
	 * @var string
	 */
	protected $object_type = 'room';

	/**
	 * This object does not support metadata.
	 *
	 * @var string
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
		'name' => '',
		'room_type' => 0,
	];

	/**
	 * Create new room object.
	 *
	 * @param int $room_id The room ID.
	 */
	public function __construct( $room_id = 0 ) {
		parent::__construct( $room_id );

		// By default room state is "available".
		$this->setDefaultValue( Room_State::AVAILABLE );
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['name'] = $this->instance['name'];
		$this['room_type'] = absint( $this->instance['room_type'] );
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
	 * Get room type instance.
	 *
	 * @return Room_Type
	 */
	public function get_room_type() {
		return apply_filters( $this->prefix( 'get_room_type' ), new Room_Type( $this['room_type'] ), $this );
	}

	/**
	 * Set the room instance.
	 *
	 * @param  mixed $the_room An array of valid room.
	 */
	public function set_instance( $the_room ) {
		if ( ! empty( $the_room['id'] ) && ! empty( $the_room['room_type'] ) ) {
			$this->instance = $the_room;
		}
	}

	/**
	 * Setup WP Core Object based on ID and object-type.
	 *
	 * @return void
	 */
	protected function setup_instance() {
		global $wpdb;

		// Try get in the cache.
		$the_room = wp_cache_get( $this->get_id(), 'awebooking/room' );

		if ( false === $the_room ) {
			// Get the room in database.
			$the_room = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` = '%d' LIMIT 1", $this->get_id() ),
				ARRAY_A
			);

			// Do nothing if not found the room.
			if ( is_null( $the_room ) ) {
				return;
			}

			// Santize before cache this room.
			$the_room['id'] = (int) $the_room['id'];
			$the_room['room_type'] = (int) $the_room['room_type'];

			wp_cache_add( $the_room['id'], $the_room, 'awebooking/room' );
		}

		$this->set_instance( $the_room );
	}
}
