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

function awebooking_update_300_beta12_change_settings() {
	try {
		$settings = awebooking( 'setting' )->all();
	} catch ( \Exception $e ) {
		$settings = get_option( 'awebooking_settings', [] );
	}

	if ( empty( $settings ) ) {
		return;
	}

	if ( isset( $settings['children_bookable']['enable'] ) ) {
		$settings['children_bookable'] = (bool) $settings['children_bookable']['enable'];
	}

	if ( isset( $settings['children_bookable']['description'] ) ) {
		$settings['children_bookable_description'] = (string) $settings['children_bookable']['description'];
	}

	if ( isset( $settings['infants_bookable']['enable'] ) ) {
		$settings['infants_bookable'] = (bool) $settings['infants_bookable']['enable'];
	}

	if ( isset( $settings['infants_bookable']['description'] ) ) {
		$settings['infants_bookable_description'] = (string) $settings['infants_bookable']['description'];
	}

	try {
		$settings = awebooking( 'setting' )->save( $settings );
	} catch ( \Exception $e ) {
		$settings = update_option( 'awebooking_settings', $settings );
	}
}
