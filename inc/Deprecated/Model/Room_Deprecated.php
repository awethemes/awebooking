<?php
namespace AweBooking\Deprecated\Model;

trait Room_Deprecated {
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
}
