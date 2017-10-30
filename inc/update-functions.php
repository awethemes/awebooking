<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function awebooking_update_300_beta10_fix_db_types() {
	global $wpdb;

	$modify_columns = '';
	for ( $i = 1; $i <= 30; $i++ ) {
		$modify_columns .= 'MODIFY `d' . $i . '` BIGINT, ';
	}

	$modify_columns .= 'MODIFY `d31` BIGINT;';

	$wpdb->query( "ALTER TABLE {$wpdb->prefix}awebooking_pricing {$modify_columns}" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}awebooking_availability {$modify_columns}" );
}
