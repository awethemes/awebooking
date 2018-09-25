<?php

use AweBooking\Constants;

/**
 * Update services from taxonomy to post type.
 *
 * @param \AweBooking\Installer $installer The installer instance.
 */
function abrs_update_310_migrate_services( $installer ) {
	abrs_set_time_limit( 0 );

	register_taxonomy( 'hotel_extra_service', 'room_type' );

	$terms = get_terms([
		'hide_empty' => false,
		'taxonomy'   => 'hotel_extra_service',
	]);

	foreach ( $terms as $term ) {
		$name = $term->name;
		$term_id = $term->term_id;

		if ( get_page_by_title( $name, OBJECT, 'hotel_service' ) ) {
			continue;
		}

		$post_id = wp_insert_post([
			'post_title'   => $name,
			'post_content' => $term->description,
			'post_status'  => 'publish',
			'post_type'    => 'hotel_service',
		]);

		if ( $value = get_term_meta( $term_id, '_service_operation', true ) ) {
			update_post_meta( $post_id, '_operation', $value );
		}

		if ( $value = get_term_meta( $term_id, '_service_value', true ) ) {
			update_post_meta( $post_id, '_amount', $value );
		}

		if ( $value = get_term_meta( $term_id, '_icon', true ) ) {
			update_post_meta( $post_id, '_icon', $value );
		}
	}

	unregister_taxonomy( 'hotel_extra_service' );
}

/**
 * Perform update room types caches.
 *
 * @return void
 */
function abrs_update_310_room_types() {
	$room_types = get_posts([
		'numberposts' => -1,
		'post_type' => Constants::ROOM_TYPE,
	]);

	foreach ( $room_types as $room_type ) {
		abrs_rescue( function () use ( $room_type ) {
			abrs_get_room_type( $room_type )->save();
		});
	}
}

/**
 * Perform update bookings caches.
 *
 * @return void
 */
function abrs_update_310_bookings() {
	$bookings = get_posts([
		'numberposts' => -1,
		'post_type' => Constants::BOOKING,
	]);

	foreach ( $bookings as $booking ) {
		abrs_rescue( function () use ( $booking ) {
			abrs_get_booking( $booking )->calculate_totals();
		});
	}
}

/**
 * Update DB Version.
 *
 * @param \AweBooking\Installer $installer The installer instance.
 */
function abrs_update_310_db_version( $installer ) {
	$installer->update_db_version( '3.1.0' );
}

/**
 * Remove awebooking_relationships table.
 *
 * @return void
 */
function abrs_update_3110_remove_table_relationship() {
	global $wpdb;

	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}awebooking_relationships';" ) ) {
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}awebooking_relationships" );
	}

	if ( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}awebooking_relationships';" ) ) {
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}awebooking_relationships" );
	}
}

/**
 * Update DB Version for 3.1.10.
 *
 * @param \AweBooking\Installer $installer The installer instance.
 */
function abrs_update_3110_db_version( $installer ) {
	$installer->update_db_version( '3.1.10' );
}
