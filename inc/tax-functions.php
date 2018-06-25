<?php

/**
 * Determines if taxes enable or not.
 *
 * @return bool
 */
function abrs_tax_enabled() {
	return apply_filters( 'abrs_tax_enabled', abrs_get_option( 'calc_taxes' ) );
}

/**
 * Determines if prices inclusive of tax or not.
 *
 * @return string
 */
function abrs_prices_include_tax() {
	return abrs_tax_enabled() && abrs_get_option( 'prices_include_tax' );
}

/**
 * Get tax rate model.
 *
 * @return string
 */
function abrs_get_tax_rate_model() {
	return apply_filters( 'abrs_tax_rate_model', abrs_get_option( 'tax_rate_model', 'single' ) );
}

/**
 * Gets tax rates.
 *
 * @return \AweBooking\Support\Collection
 */
function abrs_get_tax_rates() {
	global $wpdb;

	$rates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}awebooking_tax_rates ORDER BY priority LIMIT 1000", ARRAY_A );

	return abrs_collect(
		is_array( $rates ) ? array_map( '_abrs_prepare_tax_rate', $rates ) : []
	);
}

/**
 * Get a tax rate from the database.
 *
 * @param  int $tax_rate_id The tax rate ID.
 * @return array|null
 */
function abrs_get_tax_rate( $tax_rate_id ) {
	global $wpdb;

	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}awebooking_tax_rates WHERE id = %d LIMIT 1", $tax_rate_id ), ARRAY_A );

	return $row ? _abrs_prepare_tax_rate( $row ) : null;
}

/**
 * Perform insert a tax rate into the database.
 *
 * @param  array $tax_rate Tax rate args.
 * @return bool|int
 */
function abrs_insert_tax_rate( array $tax_rate ) {
	global $wpdb;

	$wpdb->insert( $wpdb->prefix . 'awebooking_tax_rates', _abrs_prepare_tax_rate( $tax_rate ) );

	do_action( 'abrs_tax_rate_added', $wpdb->insert_id, $tax_rate );

	return $wpdb->insert_id;
}

/**
 * Delete a tax rate from the database.
 *
 * @param  int $tax_rate_id Tax rate ID.
 * @return void
 */
function abrs_delete_tax_rate( $tax_rate_id ) {
	global $wpdb;

	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}awebooking_tax_rates WHERE id = %d;", $tax_rate_id ) );

	do_action( 'abrs_tax_rate_deleted', $tax_rate_id );
}

/**
 * Prepare and format tax rate.
 *
 * @param  array $tax_rate The tax rate raw array.
 * @return array
 */
function _abrs_prepare_tax_rate( $tax_rate ) {
	$tax_rate['name'] = $tax_rate['name'] ?: esc_html__( 'Tax', 'awebooking' );
	$tax_rate['rate'] = abrs_decimal( $tax_rate['rate'], 4 )->as_string();

	return $tax_rate;
}
