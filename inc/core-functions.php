<?php

use Awethemes\Http\Request;
use AweBooking\Multilingual;
use AweBooking\Component\Currency\Symbol;
use AweBooking\Component\Routing\Url_Generator;

// Requires other core functions.
require trailingslashit( __DIR__ ) . 'sanitizer.php';
require trailingslashit( __DIR__ ) . 'formatting.php';
require trailingslashit( __DIR__ ) . 'list-functions.php';
require trailingslashit( __DIR__ ) . 'date-functions.php';
require trailingslashit( __DIR__ ) . 'query-functions.php';
require trailingslashit( __DIR__ ) . 'model-functions.php';
require trailingslashit( __DIR__ ) . 'concierge.php';

/**
 * Gets the plugin URL.
 *
 * @param  string $path Optional, extra path to added.
 * @return string
 */
function abrs_plugin_url( $path = null ) {
	return awebooking()->plugin_url( $path );
}

/**
 * Report an exception.
 *
 * @param  Exception $e Report the exception.
 * @return void
 *
 * @throws Exception
 */
function abrs_report( $e ) {
	try {
		$logger = awebooking()->make( 'logger' );
	} catch ( \Exception $ex ) {
		throw $e; // Throw the original exception.
	}

	$logger->error( $e->getMessage(), [ 'exception' => $e ] );
}

/**
 * Returns the Http request.
 *
 * @return \Awethemes\Http\Request
 */
function abrs_request() {
	return awebooking()->make( Request::class );
}

/**
 * Returns the Url_Generator.
 *
 * @return \AweBooking\Http\Routing\Url_Generator
 */
function abrs_url() {
	return awebooking()->make( Url_Generator::class );
}

/**
 * Retrieves the admin route URL.
 *
 * @param  string $path       Optional, the admin route.
 * @param  array  $parameters The additional parameters.
 * @return string
 */
function abrs_admin_route( $path = '/', $parameters = [] ) {
	return abrs_url()->admin_route( $path, $parameters );
}

/**
 * Retrieves an option by key-name.
 *
 * @param  string $key     The key name.
 * @param  mixed  $default The default value.
 * @return mixed
 */
function abrs_option( $key, $default = null ) {
	return awebooking()->get_option( $key, $default );
}

/**
 * Is current WordPress is running in multi-languages.
 *
 * @return bool
 */
function abrs_is_running_multilanguage() {
	return apply_filters( 'awebooking/is_running_multilanguage', Multilingual::is_polylang() || Multilingual::is_wpml() );
}

/**
 * Determines if plugin enable multiple hotels.
 *
 * @return bool
 */
function abrs_is_multiple_hotels() {
	return apply_filters( 'awebooking/is_multiple_hotels', abrs_option( 'enable_location', false ) );
}

/**
 * Determines if plugin allow children in reservation.
 *
 * @return bool
 */
function abrs_is_children_bookable() {
	return apply_filters( 'awebooking/is_children_bookable', abrs_option( 'children_bookable', true ) );
}

/**
 * Determines if plugin allow infants in reservation.
 *
 * @return bool
 */
function abrs_is_infants_bookable() {
	return apply_filters( 'awebooking/is_infants_bookable', abrs_option( 'infants_bookable', true ) );
}

/**
 * Returns the maximum rooms allowed in the scaffold.
 *
 * @return int
 */
function abrs_maximum_scaffold_rooms() {
	return (int) apply_filters( 'awebooking/maximum_scaffold_rooms', 25 );
}

/**
 * Returns plugin current currency.
 *
 * @return string
 */
function abrs_current_currency() {
	return abrs_option( 'currency', 'USD' );
}

/**
 * Get the currency symbol by code.
 *
 * @param  string $currency The currency code.
 * @return string
 */
function abrs_currency_symbol( $currency = null ) {
	if ( is_null( $currency ) ) {
		$currency = abrs_current_currency();
	}

	$symbols = apply_filters( 'awebooking/currency_symbols', Symbol::$symbols );

	$symbol = array_key_exists( $currency, $symbols )
		? $symbols[ $currency ]
		: '';

	return apply_filters( 'awebooking/currency_symbol', $symbol, $currency );
}

/**
 * Get the currency name by code.
 *
 * @param  string $currency The currency code.
 * @return string
 */
function abrs_currency_name( $currency = null ) {
	if ( is_null( $currency ) ) {
		$currency = abrs_current_currency();
	}

	$name = abrs_rescue( function() use ( $currency ) {
		return awebooking( 'currencies' )->find( $currency )['name'];
	});

	return apply_filters( 'awebooking/currency_name', (string) $name, $currency );
}

/**
 * Retrieve the page ID.
 *
 * @param  string $page The page slug: check_availability, booking, checkout.
 * @return int
 */
function abrs_get_page_id( $page ) {
	$page = sanitize_key( $page );

	$page = apply_filters( 'awebooking/get_' . $page . '_page_id', abrs_option( 'page_' . $page ) );

	return $page ? absint( $page ) : -1;
}

/**
 * Retrieve page permalink.
 *
 * @see awebooking_get_page_id()
 *
 * @param  string $page The retrieve page.
 * @return string
 */
function arbs_get_page_permalink( $page ) {
	$page_id   = abrs_get_page_id( $page );

	$permalink = 0 < $page_id ? get_permalink( $page_id ) : get_home_url();

	return apply_filters( 'awebooking/get_' . $page . '_page_permalink', $permalink );
}
