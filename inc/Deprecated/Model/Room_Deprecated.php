<?php
namespace AweBooking\Deprecated\Model;

trait Room_Deprecated {
	public static function get_by_room_type( $ids ) {
		global $wpdb;

		$ids = is_int( $ids ) ? [ $ids ] : $ids;

		if ( awebooking()->is_running_multilanguage() ) {
			$ids = array_map( function( $id ) {
				return awebooking()->get_multilingual()->get_original_post( $id );
			}, $ids );
		}

		if ( is_array( $ids ) ) {
			$ids = implode( "', '", array_map( 'esc_sql', $ids ) );
			$query = "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` WHERE `room_type` IN ('{$ids}')";
		}

		// @codingStandardsIgnoreLine
		return $wpdb->get_results( $query, ARRAY_A );
	}
}
