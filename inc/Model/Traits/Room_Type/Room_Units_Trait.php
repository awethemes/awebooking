<?php
namespace AweBooking\Model\Traits\Room_Type;

use AweBooking\Factory;
use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Support\Collection;

trait Room_Units_Trait {
	/**
	 * The list rooms.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $rooms;

	/**
	 * Get rooms of this room type.
	 *
	 * @return array \AweBooking\Support\Collection
	 */
	public function get_rooms() {
		$this->maybe_setup_rooms();

		return apply_filters( $this->prefix( 'get_rooms' ), $this->rooms, $this );
	}

	/**
	 * Get list IDs of rooms.
	 *
	 * @return array
	 */
	public function get_room_ids() {
		return $this->get_rooms()->pluck( 'id' )->all();
	}

	/**
	 * Get the total of rooms.
	 *
	 * @return int
	 */
	public function get_total_rooms() {
		return $this->get_rooms()->count();
	}

	/**
	 * Determines an item ID have in items.
	 *
	 * @param  Room|int $room_unit Booking item ID.
	 * @return boolean
	 */
	public function has_room( $room_unit ) {
		return ! is_null( $this->get_room( $room_unit ) );
	}

	/**
	 * Returns item instance by ID.
	 *
	 * @param  Room|int $room_unit The room unit ID.
	 * @return Room|null
	 */
	public function get_room( $room_unit ) {
		return $this->get_rooms()
			->first( function( $room ) use ( $room_unit ) {
				return $room->get_id() === $this->filter_room_id( $room_unit );
			});
	}

	/**
	 * Remove a room_unit.
	 *
	 * @param  Room|int $room_unit The room unit ID.
	 * @return boolean|null
	 */
	public function remove_room( $room_unit ) {
		$room_unit = $this->get_room( $room_unit );

		if ( is_null( $room_unit ) ) {
			return;
		}

		$deleted = $room_unit->delete( true );
		if ( ! $deleted ) {
			return false;
		}

		$this->rooms = $this->get_rooms()
			->reject( function( $room ) use ( $room_unit ) {
				return $room->get_id() === $room_unit->get_id();
			});

		return true;
	}

	/**
	 * Adds a room in to the room_type.
	 *
	 * @param  Room|array $room_data The data to insert.
	 * @return Room|null
	 */
	public function add_room( array $room_data ) {
		$this->maybe_setup_rooms();

		$room_data = wp_parse_args( $room_data, [
			'name'  => '',
			'order' => 0,
		]);

		$room_unit = ( new Room )->fill( $room_data );
		$room_unit['room_type'] = $this->get_id();

		if ( $room_unit->save() ) {
			do_action( $this->prefix( 'added_room' ), $room_unit, $this );

			$this->rooms->push( $room_unit );

			return $room_unit;
		}
	}

	/**
	 * Filter the room_unit.
	 *
	 * @param  Room|int $room_unit The Room instance of room unit ID.
	 * @return int
	 */
	protected function filter_room_id( $room_unit ) {
		return $room_unit instanceof Room ? $room_unit->get_id() : absint( $room_unit );
	}

	/**
	 * Maybe setup room units.
	 *
	 * @return void
	 */
	protected function maybe_setup_rooms() {
		if ( is_null( $this->rooms ) ) {
			$this->setup_room_units();
		}
	}

	/**
	 * Setup room units from database.
	 *
	 * @return void
	 */
	protected function setup_room_units() {
		$room_type_id = $this->get_id();

		// If current site running on multilanguage, we will get room-units
		// from original room_type. To avoid the "replication" have own room-units.
		if ( awebooking()->is_running_multilanguage() ) {
			$room_type_id = awebooking( 'multilingual' )->get_original_post( $room_type_id );
		}

		// First, try get the rooms in cache.
		$the_rooms = wp_cache_get( $room_type_id, Constants::CACHE_ROOMS_IN_ROOM_TYPE );

		// If not found we will fetch from database.
		if ( false === $the_rooms ) {
			global $wpdb;

			$the_rooms = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` = %d ORDER BY `name` ASC LIMIT 1000", $room_type_id ),
				ARRAY_A
			);

			if ( is_null( $the_rooms ) ) {
				return;
			}

			// Add this results to cache.
			wp_cache_add( $room_type_id, $the_rooms, Constants::CACHE_ROOMS_IN_ROOM_TYPE );
		}

		// Attach founded into the rooms, cache each room_unit before that.
		$room_units = [];

		foreach ( $the_rooms as $r ) {
			wp_cache_delete( (int) $r['id'], Constants::CACHE_RAW_ROOM_UNIT );

			wp_cache_add( (int) $r['id'], $r, Constants::CACHE_RAW_ROOM_UNIT );

			$room_units[] = ( new Room )->with_instance( $r );
		}

		$this->rooms = Collection::make( $room_units );
	}
}
