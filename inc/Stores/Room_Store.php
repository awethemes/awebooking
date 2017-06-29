<?php
namespace AweBooking\Stores;

use AweBooking\Room_Type;

class Room_Store {
	/**
	 * Returns the count of records in the database.
	 *
	 * @return int
	 */
	public function count( $room_type = null ) {
		global $wpdb;

		$where_clauses = '';
		if ( ! empty( $room_type ) ) {
			$where_clauses .= ' WHERE `room`.`room_type` = ' . esc_sql( $room_type );
		}

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->prefix}awebooking_rooms` AS `room` INNER JOIN `{$wpdb->posts}` AS `post` ON (`post`.`ID` = `room`.`room_type` AND `post`.`post_status` = 'publish' AND `post`.`post_type` = 'room_type') {$where_clauses}" );

		return $count ? absint( $count ) : 0;
	}

	/**
	 * Check a room ID have in the database.
	 *
	 * @param  int $room_id   Room ID to check.
	 * @param  int $room_type Optional, room-type ID.
	 * @return boolean
	 */
	public function has( $room_id, $room_type = null ) {
		global $wpdb;

		if ( is_null( $room_type ) ) {
			$query = "SELECT COUNT(*) FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` = '%d' LIMIT 1";
		} else {
			$query = "SELECT COUNT(*) FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` = '%d' AND `room_type` = '%d' LIMIT 1";
		}

		$count = $wpdb->get_var(
			// @codingStandardsIgnoreLine
			$wpdb->prepare( $query, $room_id, $room_type )
		);

		return absint( $count ) > 0;
	}

	/**
	 * Get a room by ID.
	 *
	 * @param  int $room_id   Room ID to check.
	 * @param  int $room_type Optional, room-type ID.
	 * @return array|null
	 */
	public function get( $room_id, $room_type = null ) {
		global $wpdb;

		if ( is_null( $room_type ) ) {
			$query = "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` = '%d' LIMIT 1";
		} else {
			$query = "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` = '%d' AND `room_type` = '%d' LIMIT 1";
		}

		// @codingStandardsIgnoreLine
		return $wpdb->get_row( $wpdb->prepare( $query, $room_id, $room_type ), ARRAY_A );
	}

	/**
	 * Get list room by room-type ID(s).
	 *
	 * @param  int|array $ids The room-type ID(s).
	 * @return array|null
	 */
	public function list_by_room_type( $ids ) {
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
	 * Insert new room.
	 *
	 * @param  int    $room_type Room type ID.
	 * @param  string $name      Optional room name.
	 * @return int|false
	 */
	public function insert( $room_type, $name = '' ) {
		global $wpdb;

		return $wpdb->insert(
			$wpdb->prefix . 'awebooking_rooms',
			[ 'name' => $name, 'room_type' => $room_type ],
			[ '%s', '%s' ]
		);
	}

	/**
	 * Update a room.
	 *
	 * TODO: Change $room_args.
	 *
	 * @param  int   $room_id   Room ID.
	 * @param  array $room_args Optional, room arguments.
	 * @return int|false
	 */
	public function update( $room_id, array $room_args ) {
		global $wpdb;

		$update_data = [];
		$where_clauses = [ 'id' => $room_id ]; // @codingStandardsIgnoreLine

		if ( isset( $room_args['room_type'] ) && ! empty( $room_args['room_type'] ) ) {
			$where_clauses['room_type'] = absint( $room_args['room_type'] );
		}

		if ( isset( $room_args['name'] ) && ! empty( $room_args['name'] ) ) {
			$update_data['name'] = $room_args['name'];
		}

		// If empty data for update, nothing to update
		// we just return current room ID.
		if ( empty( $update_data ) ) {
			return $room_id;
		}

		// Update in the database.
		$updated = $wpdb->update( $wpdb->prefix . 'awebooking_rooms', $update_data, $where_clauses, '%s', '%d' );

		return false !== $updated ? $room_id : false;
	}

	/**
	 * Delete room(s) by IDs.
	 *
	 * @param  int|array $ids Room id or ids.
	 * @return boolean
	 */
	public function delete( $ids ) {
		global $wpdb;

		if ( empty( $ids ) ) {
			return false;
		}

		if ( is_array( $ids ) ) {
			$ids = implode( "', '", array_map( 'esc_sql', $ids ) );

			// @codingStandardsIgnoreLine
			$deleted = $wpdb->query( "DELETE FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` IN ('{$ids}')" );
		} else {
			// @codingStandardsIgnoreLine
			$deleted = $wpdb->delete( $wpdb->prefix . 'awebooking_rooms', [ 'id' => $ids ], '%d' );
		}

		return false !== $deleted;
	}

	/**
	 * Create new room or update if room exists.
	 *
	 * @param  int   $room_type Room type ID.
	 * @param  array $room_args Room arguments.
	 * @return int|false
	 */
	public function create_or_update( $room_type, array $room_args ) {
		if ( isset( $room_args['id'] ) && $room_args['id'] > 0 && $this->has( $room_args['id'], $room_type ) ) {
			// Mapping room_type ID to room_args for update.
			$room_args['room_type'] = $room_type;

			return $this->update( (int) $room_args['id'], $room_args );
		}

		return $this->insert( (int) $room_type, $room_args );
	}

	/**
	 * To many thing TODO in this method.
	 *
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function query( $args ) {
		global $wpdb;

		$args = wp_parse_args( $args, [
			'room_type'   => 0,
			'per_page'    => 5,
			'page_number' => 1,
		]);

		$query = "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` AS `room` INNER JOIN `{$wpdb->posts}` AS `post` ON (`post`.`ID` = `room`.`room_type` AND `post`.`post_status` = 'publish' AND `post`.`post_type` = 'room_type')";

		if ( ! empty( $args['room_type'] ) ) {
			$query .= ' WHERE `room`.`room_type` = ' . esc_sql( $args['room_type'] );
		}

		$offset = ( $args['page_number'] - 1 ) * $args['per_page'];
		$query .= ' ORDER  BY `room`.`name` ASC';
		$query .= ' LIMIT ' . esc_sql( $args['per_page'] ) . ' OFFSET ' . esc_sql( $offset );

		// @codingStandardsIgnoreLine
		return $wpdb->get_results( $query, ARRAY_A );
	}
}
