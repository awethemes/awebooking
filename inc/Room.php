<?php
namespace AweBooking;

use AweBooking\BAT\Calendar;
use AweBooking\Support\WP_Object;
use AweBooking\Support\Date_Period;
use AweBooking\Support\Traits\BAT_Unit;
use Roomify\Bat\Unit\UnitInterface as Unit_Interface;

class Room extends WP_Object implements Unit_Interface {
	use BAT_Unit;

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
	 * Store room-type this room unit belong to.
	 *
	 * @var Room_Type
	 */
	protected $room_type;

	/**
	 * The attributes for this object.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $attributes = [
		'name' => '',
		'room_type_id' => 0,
	];

	/**
	 * Create new room object.
	 *
	 * @param int $room_id The room ID.
	 */
	public function __construct( $room_id = 0 ) {
		parent::__construct( $room_id );

		// By default, room state is alway available.
		$this->setDefaultValue( Room_State::AVAILABLE );
	}

	/**
	 * Setup the object attributes.
	 *
	 * @return void
	 */
	protected function setup() {
		$this['name'] = $this->instance['name'];
		$this['room_type_id'] = absint( $this->instance['room_type'] );

		$this->room_type = new Room_Type( $this['room_type_id'] );
	}

	/**
	 * Get list room by room-type ID(s).
	 *
	 * @param  int|array $ids The room-type ID(s).
	 * @return array|null
	 */
	public static function get_by_room_type( $ids ) {
		global $wpdb;

		if ( is_array( $ids ) ) {
			$ids = implode( "', '", array_map( 'esc_sql', $ids ) );
			$query = "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` IN ('{$ids}')";
		} else {
			$query = $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` = '%d'", $ids );
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
	 * Get room type instance.
	 *
	 * @return Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Check this room-unit is free in a period (could be available for booking).
	 *
	 * This method check only available state, no constraints apply.
	 *
	 * @param  Date_Period $period  The date period.
	 * @return bool|null
	 */
	public function is_free( Date_Period $period ) {
		// Make sure we have an exists room unit,
		// if not just leave and return.
		if ( ! $this->exists() ) {
			return;
		}

		// Create the availability calendar.
		$calendar = new Calendar( [ $this ],
			awebooking()->make( 'store.availability' )
		);

		// Here that's why we subtract a minute from end date:
		// We have period from: 2017-10-10 to 2017-10-13, so have 3 nights (10-11, 11-12, 12-13).
		// The BAT system calculate by nightly, so if we past from "2017-10-10" to "2017-10-13"
		// by default we'll receive result of `4` nights (10, 11, 12, 13).
		$response = $calendar->getMatchingUnits(
			$period->get_start_date(),
			$period->get_end_date()->subMinute(),
			[ Room_State::AVAILABLE ]
		);

		return array_key_exists( $this->get_id(),
			$response->getIncluded()
		);
	}

	/**
	 * Clean object cache after saved.
	 *
	 * @return void
	 */
	protected function clean_cache() {
		wp_cache_delete( $this->get_id(), 'awebooking_cache_room' );
	}

	/**
	 * Run perform insert object into database.
	 *
	 * @return int|void
	 */
	protected function perform_insert() {
		global $wpdb;

		// We need a room-type present.
		if ( ! $this['room_type_id'] ) {
			return;
		}

		$wpdb->insert( $wpdb->prefix . 'awebooking_rooms',
			[ 'name' => $this['name'], 'room_type' => $this['room_type_id'] ],
			[ '%s', '%d' ]
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
		if ( ! $this['room_type_id'] ) {
			return;
		}

		$updated = $wpdb->update( $wpdb->prefix . 'awebooking_rooms',
			[ 'name' => $this['name'], 'room_type' => $this['room_type_id'] ],
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
		$the_room = wp_cache_get( $this->get_id(), 'awebooking_cache_room' );

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

			wp_cache_add( $the_room['id'], $the_room, 'awebooking_cache_room' );
		}

		$this->set_instance( $the_room );
	}
}
