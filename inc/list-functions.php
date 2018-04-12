<?php

/**
 * Returns list of countries indexed by alpha2 code.
 *
 * @return array[]
 */
function abrs_list_countries() {
	return abrs_collect( awebooking( 'countries' )->all() )
		->pluck( 'name', 'alpha2' )
		->all();
}

/**
 * Returns list of currencies.
 *
 * @return array[]
 */
function abrs_list_currencies() {
	return abrs_collect( awebooking( 'currencies' )->all() )
		->pluck( 'name', 'alpha3' )
		->all();
}

/**
 * Returns list dropdown of currencies.
 *
 * @return array[]
 */
function abrs_list_dropdown_currencies() {
	return abrs_collect( abrs_list_currencies() )
		->transform( function( $name, $code ) {
			return $name . ' (' . abrs_currency_symbol( $code ) . ')';
		})->all();
}

/**
 * Get list payment methods.
 *
 * @return array
 */
function abrs_list_payment_methods() {
	$methods = apply_filters( 'awebooking/base_payment_methods', [
		'cash' => esc_html__( 'Cash', 'awebooking' ),
	]);

	$gateways = awebooking()->make( 'gateways' )->enabled()
		->map( function( $m ) {
			return $m->get_method_title();
		})->all();

	return array_merge( $methods, $gateways );
}

/**
 * Returns a list of booking statuses.
 *
 * @return array
 */
function abrs_list_booking_statuses() {
	return apply_filters( 'awebooking/list_booking_statuses', [
		'awebooking-pending'     => _x( 'Pending', 'Booking status', 'awebooking' ),
		'awebooking-inprocess'   => _x( 'Processing', 'Booking status', 'awebooking' ),
		'awebooking-on-hold'     => _x( 'Reserved', 'Booking status', 'awebooking' ),
		'awebooking-deposit'     => _x( 'Deposit', 'Booking status', 'awebooking' ),
		'awebooking-completed'   => _x( 'Paid', 'Booking status', 'awebooking' ),
		'checked-in'             => _x( 'Checked In', 'Booking status', 'awebooking' ),
		'checked-out'            => _x( 'Checked Out', 'Booking status', 'awebooking' ),
		'awebooking-cancelled'   => _x( 'Cancelled', 'Booking status', 'awebooking' ),
	]);
}

/**
 * Return a list of common titles.
 *
 * @return string
 */
function abrs_list_common_titles() {
	return apply_filters( 'awebooking/list_customer_titles', [
		'mr'   => esc_html__( 'Mr.', 'awebooking' ),
		'ms'   => esc_html__( 'Ms.', 'awebooking' ),
		'mrs'  => esc_html__( 'Mrs.', 'awebooking' ),
		'miss' => esc_html__( 'Miss.', 'awebooking' ),
		'dr'   => esc_html__( 'Dr.', 'awebooking' ),
		'prof' => esc_html__( 'Prof.', 'awebooking' ),
	]);
}
