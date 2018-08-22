<?php

/**
 * Determines if taxes enable or not.
 *
 * @return bool
 */
function abrs_tax_enabled() {
	return apply_filters( 'abrs_tax_enabled', 'on' === abrs_get_option( 'calc_taxes', 'on' ) );
}

/**
 * Determines if prices inclusive of tax or not.
 *
 * @return bool
 */
function abrs_prices_includes_tax() {
	return abrs_tax_enabled() && 'yes' === abrs_get_option( 'prices_include_tax', 'no' );
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
 * Round to precision.
 *
 * @param  int|float $in Input number.
 * @return int|float
 */
function abrs_round_tax( $in ) {
	return apply_filters( 'abrs_round_tax', round( $in, abrs_get_rounding_precision(), PHP_ROUND_HALF_UP ), $in );
}

/**
 * Calculate tax for a price.
 *
 * @param  float   $price              Price to calc tax on.
 * @param  array   $rates              Rates to apply.
 * @param  boolean $price_includes_tax Whether the passed price has taxes included.
 * @return array                       Array of rates + prices after tax.
 */
function abrs_calc_tax( $price, array $rates, $price_includes_tax = false ) {
	if ( $price_includes_tax ) {
		$taxes = abrs_calc_inclusive_tax( $price, $rates );
	} else {
		$taxes = abrs_calc_exclusive_tax( $price, $rates );
	}

	return apply_filters( 'abrs_calc_tax', $taxes, $price, $rates, $price_includes_tax );
}

/**
 * Calc tax from inclusive price.
 *
 * @param  float $price Price to calculate tax for.
 * @param  array $rates Array of tax rates.
 * @return array
 */
function abrs_calc_inclusive_tax( $price, array $rates ) {
	$taxes          = [];
	$compound_rates = [];
	$regular_rates  = [];

	// Index array so taxes are output in correct order
	// and see what compound/regular rates we have to calculate.
	foreach ( $rates as $key => $rate ) {
		$taxes[ $key ] = 0;

		if ( $rate['compound'] ) {
			$compound_rates[ $key ] = $rate['rate'];
		} else {
			$regular_rates[ $key ] = $rate['rate'];
		}
	}

	// Working backwards.
	$compound_rates = array_reverse( $compound_rates, true );

	$non_compound_price = $price;

	foreach ( $compound_rates as $key => $compound_rate ) {
		$tax_amount         = apply_filters( 'abrs_price_inc_tax_amount', $non_compound_price - ( $non_compound_price / ( 1 + ( $compound_rate / 100 ) ) ), $key, $rates[ $key ], $price );
		$taxes[ $key ]      += $tax_amount;
		$non_compound_price -= $tax_amount;
	}

	// Regular taxes.
	$regular_tax_rate = 1 + ( array_sum( $regular_rates ) / 100 );

	foreach ( $regular_rates as $key => $regular_rate ) {
		$the_rate       = ( $regular_rate / 100 ) / $regular_tax_rate;
		$net_price      = $price - ( $the_rate * $non_compound_price );
		$tax_amount     = apply_filters( 'abrs_price_inc_tax_amount', $price - $net_price, $key, $rates[ $key ], $price );
		$taxes[ $key ] += $tax_amount;
	}

	return $taxes;
}

/**
 * Calc tax from exclusive price.
 *
 * @param  float $price Price to calculate tax for.
 * @param  array $rates Array of tax rates.
 * @return array
 */
function abrs_calc_exclusive_tax( $price, array $rates ) {
	$taxes = [];

	if ( ! empty( $rates ) ) {
		foreach ( $rates as $key => $rate ) {
			if ( $rate['compound'] ) {
				continue;
			}

			$tax_amount = $price * ( $rate['rate'] / 100 );
			$tax_amount = apply_filters( 'abrs_price_ex_tax_amount', $tax_amount, $key, $rate, $price );

			if ( ! isset( $taxes[ $key ] ) ) {
				$taxes[ $key ] = $tax_amount;
			} else {
				$taxes[ $key ] += $tax_amount;
			}
		}

		$pre_compound_total = array_sum( $taxes );

		// Compound taxes.
		foreach ( $rates as $key => $rate ) {
			if ( ! $rate['compound'] ) {
				continue;
			}

			$the_price_inc_tax = $price + $pre_compound_total;
			$tax_amount        = $the_price_inc_tax * ( $rate['rate'] / 100 );
			$tax_amount        = apply_filters( 'abrs_price_ex_tax_amount', $tax_amount, $key, $rate, $price, $the_price_inc_tax, $pre_compound_total );

			if ( ! isset( $taxes[ $key ] ) ) {
				$taxes[ $key ] = $tax_amount;
			} else {
				$taxes[ $key ] += $tax_amount;
			}

			$pre_compound_total = array_sum( $taxes );
		}
	}

	return $taxes;
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
 * Gets all tax rates for dropdown.
 *
 * @return array
 */
function abrs_get_tax_rates_for_dropdown() {
	return abrs_get_tax_rates()
		->keyBy( 'id' )
		->map( function ( $rate ) {
			return $rate['name'] . ' (' . (float) $rate['rate'] . '%)';
		})->all();
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
 * Perform update a tax rate into the database.
 *
 * @param  int   $tax_rate_id The tax rate ID.
 * @param  array $tax_rate    Tax rate args.
 * @return bool
 */
function abrs_update_tax_rate( $tax_rate_id, array $tax_rate ) {
	global $wpdb;

	$updated = $wpdb->update( $wpdb->prefix . 'awebooking_tax_rates', _abrs_prepare_tax_rate( $tax_rate ), [ 'id' => $tax_rate_id ] );

	do_action( 'abrs_tax_rate_updated', $tax_rate_id, $tax_rate );

	return false !== $updated && $updated > 0;
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
	$tax_rate['rate'] = abrs_decimal( $tax_rate['rate'], 4 )->as_numeric();

	$tax_rate['priority'] = isset( $tax_rate['priority'] ) ? absint( $tax_rate['priority'] ) : 0;
	$tax_rate['compound'] = isset( $tax_rate['compound'] ) && $tax_rate['compound'] ? 1 : 0;

	return $tax_rate;
}
