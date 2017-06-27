<?php
namespace AweBooking\Stores;

use WP_Query;
use AweBooking\Room_Type;
use AweBooking\AweBooking;

class Room_Type_Store {
	/**
	 *  Room_Store instance.
	 *
	 * @var Room_Store
	 */
	protected $room_store;

	/**
	 * Room_Type_Store constructor.
	 *
	 * @param Room_Store $room_store Room_Store instance.
	 */
	public function __construct( Room_Store $room_store ) {
		$this->room_store = $room_store;
	}

	/**
	 * Bulk sync rooms.
	 *
	 * @param  int   $room_type     The room-type ID.
	 * @param  array $request_rooms The request rooms.
	 * @return void
	 */
	public function bulk_sync_rooms( $room_type, array $request_rooms ) {
		// Current list room of room-type.
		$db_rooms_ids = array_map( 'absint',
			wp_list_pluck( $this->room_store->list_by_room_type( $room_type ), 'id' )
		);

		$touch_ids = [];
		foreach ( $request_rooms as $raw_room ) {
			// Ignore in-valid rooms from request.
			if ( ! isset( $raw_room['id'] ) || ! isset( $raw_room['name'] ) ) {
				continue;
			}

			// Sanitize data before working with database.
			$room_args = array_map( 'sanitize_text_field', $raw_room );
			$room_args['room_type'] = $room_type;

			if ( $room_args['id'] > 0 && in_array( (int) $room_args['id'], $db_rooms_ids ) ) {
				$working_id = $this->room_store->update( (int) $room_args['id'], $room_args );
			} else {
				$working_id = $this->room_store->insert( $room_type, $room_args['name'] );
			}

			// We'll map current working ID in $touch_ids...
			if ( $working_id ) {
				$touch_ids[] = $working_id;
			}
		}

		// Fimally, delete invisible rooms.
		$delete_ids = array_diff( $db_rooms_ids, $touch_ids );

		if ( ! empty( $delete_ids ) ) {
			$this->room_store->delete( $delete_ids );
		}
	}

	/**
	 * Returns an array of room types.
	 *
	 * @param  array $args //
	 * @return WP_Query
	 */
	public function query_room_types( $args = array() ) {
		/**
		 * Generate WP_Query args.
		 */
		$wp_query_args = array(
			'post_type'        => AweBooking::ROOM_TYPE,
			'tax_query'        => [],
			'booking_adults'   => -1,
			'booking_children' => -1,
			'booking_nights'   => -1,
			// 'hotel_location'   => '',
			'posts_per_page'   => -1,
		);

		if ( awebooking()->is_multi_location() && ! empty( $args['hotel_location'] ) ) {
			/*$wp_query_args['tax_query'][] = array(
				'taxonomy' => AweBooking::HOTEL_LOCATION,
				'terms'    => sanitize_text_field( $args['hotel_location'] ),
				'field'    => 'slug',
			);*/
		}

		$args = wp_parse_args( $args, $wp_query_args );

		return new WP_Query( $args );
	}
}
