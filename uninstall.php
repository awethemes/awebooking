<?php
/**
 * AweBooking Uninstall.
 *
 * Deletes user roles, pages, tables, and options.
 *
 * @package AweBooking
 */

global $wpdb;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( defined( 'AWEBOOKING_REMOVE_ALL_DATA' ) && true === AWEBOOKING_REMOVE_ALL_DATA ) {
	// Tables.
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}awebooking_rooms" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}awebooking_booking" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}awebooking_pricing" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}awebooking_availability" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}awebooking_booking_items" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}awebooking_booking_itemmeta" );

	// Delete options.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'awebooking\_%';" );

	// Delete posts + data.
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'room_type', 'awebooking' );" );

	// Clear any cached data that has been removed.
	wp_cache_flush();
}
