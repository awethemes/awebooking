<?php

use AweBooking\AweBooking;
use AweBooking\Pricing\Price;
use AweBooking\Support\Period;
use AweBooking\Support\Template;
use AweBooking\Support\Formatting;

/**
 * Get the available container instance.
 *
 * @param  null|string $make Optional, get special binding in the container.
 *                           if null AweBooking instance will be return.
 * @return AweBooking|mixed
 */
function awebooking( $make = null ) {
	if ( is_null( $make ) ) {
		return AweBooking::get_instance();
	}

	return AweBooking::get_instance()->make( $make );
}

/**
 * Get config instance or special setting by key ID.
 *
 * @param  string $key     Special key ID.
 * @param  mixed  $default Default value will be return if key not set,
 *                         if null pass, default setting value will be return.
 * @return mixed
 */
function awebooking_option( $key, $default = null ) {
	return awebooking( 'config' )->get( $key, $default );
}

/**
 * ------------------------------------------------------
 * AweBooking sanitize functions
 * ------------------------------------------------------
 */

/**
 * Sanitize price number.
 *
 * @param  Price|numeric $number Raw numeric.
 * @return float
 */
function awebooking_sanitize_price( $number ) {
	if ( $number instanceof Price ) {
		$number = $number->get_amount();
	}

	return Formatting::format_decimal( $number, true );
}

/**
 * Sanitize period.
 *
 * @param  array|mixed $value  Raw date period.
 * @param  bool        $strict Strict validation.
 * @return array
 */
function awebooking_sanitize_period( $value, $strict = false ) {
	$value = (array) $value;
	if ( empty( $value[0] ) || empty( $value[1] ) ) {
		return [];
	}

	try {
		$period = new Period( $value[0], $value[1], $strict );
	} catch ( Exception $e ) {
		return [];
	}

	if ( $period->nights() < 1 ) {
		return [];
	}

	return [
		$period->get_start_date()->toDateString(),
		$period->get_end_date()->toDateString(),
	];
}


/**
 * ------------------------------------------------------
 * Helper functions
 * ------------------------------------------------------
 */

/**
 * Generate a random string.
 *
 * @param  integer $length Random string length.
 * @return string
 */
function awebooking_random_string( $length = 16 ) {
	require_once ABSPATH . 'wp-includes/class-phpass.php';

	$bytes = (new PasswordHash( 8, false ))->get_random_bytes( $length * 2 );

	return substr( str_replace( [ '/', '+', '=' ], '', base64_encode( $bytes ) ), 0, $length );
}

/**
 * Create an array instance.
 *
 * @param  array           $ids   An array IDs.
 * @param  string|callable $class Class instance or a callable.
 * @return array
 */
function awebooking_map_instance( array $ids, $class ) {
	return array_filter(
		array_map( function( $id ) use ( $class ) {
			if ( is_string( $class ) && class_exists( $class ) ) {
				return new $class( $id );
			} elseif ( is_callable( $class ) ) {
				return call_user_func( $class, $id );
			}
		}, $ids )
	);
}

/**
 * Make a list sort by priority.
 *
 * @param  array $values An array values.
 * @return Skeleton\Support\Priority_List
 */
function awebooking_priority_list( array $values ) {
	$stack = new Skeleton\Support\Priority_List;

	foreach ( $values as $key => $value ) {
		$priority = is_object( $value ) ? $value->priority : $value['priority'];
		$stack->insert( $key, $value, $priority );
	}

	return $stack;
}

if ( ! function_exists( 'wp_data_callback' ) ) :
	/**
	 * Get Wordpress specific data from the DB and return in a usable array.
	 *
	 * @param  string $type Data type.
	 * @param  mixed  $args Optional, data query args or something else.
	 * @return array
	 */
	function wp_data_callback( $type, $args = array() ) {
		return function() use ( $type, $args ) {
			return Skeleton\Support\WP_Data::get( $type, $args );
		};
	}
endif;

/**
 * Run a MySQL transaction query, if supported.
 *
 * @param  string $type Transaction type, start (default), commit, rollback.
 * @return void
 */
function awebooking_wpdb_transaction( $type = 'start' ) {
	global $wpdb;

	$wpdb->hide_errors();

	if ( ! defined( 'AWEBOOKING_USE_TRANSACTIONS' ) ) {
		define( 'AWEBOOKING_USE_TRANSACTIONS', true );
	}

	if ( AWEBOOKING_USE_TRANSACTIONS ) {
		switch ( $type ) {
			case 'commit':
				$wpdb->query( 'COMMIT' );
				break;
			case 'rollback':
				$wpdb->query( 'ROLLBACK' );
				break;
			default:
				$wpdb->query( 'START TRANSACTION' );
				break;
		}
	}
}

/**
 * Set a cookie - Wrapper for setcookie using WP constants.
 *
 * @param  string  $name   Name of the cookie being set.
 * @param  string  $value  Value of the cookie.
 * @param  integer $expire Expiry of the cookie.
 * @param  string  $secure Whether the cookie should be served only over https.
 */
function awebooking_setcookie( $name, $value, $expire = 0, $secure = null ) {
	$secure = is_null( $secure ) ? is_ssl() : $secure;

	if ( ! headers_sent() ) {
		setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, $secure );
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		headers_sent( $file, $line );
		trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
	}
}

/**
 * Return list of common titles.
 *
 * @return string
 */
function awebooking_get_common_titles() {
	return apply_filters( 'awebooking/customer_titles', array(
		'mr'   => esc_html__( 'Mr.', 'awebooking' ),
		'ms'   => esc_html__( 'Ms.', 'awebooking' ),
		'mrs'  => esc_html__( 'Mrs.', 'awebooking' ),
		'miss' => esc_html__( 'Miss.', 'awebooking' ),
		'dr'   => esc_html__( 'Dr.', 'awebooking' ),
		'prof' => esc_html__( 'Prof.', 'awebooking' ),
	));
}

function awebooking_get_booking_request_query( $extra_args = array() ) {
	$raw = [ 'start-date', 'end-date', 'children', 'adults' ];
	$clean = [];
	foreach ( $raw as $key ) {
		$clean[ $key ] = isset( $_REQUEST[ $key ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) ) : '';
	}

	return array_merge( $clean, $extra_args );
}

/**
 * ------------------------------------------------------
 * Templates and frontend functions
 * ------------------------------------------------------
 */

/**
 * Locate a template and return the path for inclusion.
 *
 * @param  string $template_name Template name.
 * @return string
 */
function awebooking_locate_template( $template_name ) {
	return Template::locate_template( $template_name );
}

/**
 * Include a template by given a template name.
 *
 * @param string $template_name Template name.
 * @param array  $args          Template arguments.
 */
function awebooking_get_template( $template_name, $args = array() ) {
	Template::get_template( $template_name, $args );
}

/**
 * Load a template part into a template.
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 */
function awebooking_get_template_part( $slug, $name = '' ) {
	Template::get_template_part( $slug, $name );
}

/**
 * Retrieve page ids.
 *
 * Used for "check_availability", "booking", "checkout".
 *
 * @param  string $page The retrieve page.
 * @return int          Returns -1 if no page is found.
 */
function awebooking_get_page_id( $page ) {
	$page = apply_filters( 'awebooking/get_' . $page . '_page_id', awebooking_option( 'page_' . $page ) );

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
function awebooking_get_page_permalink( $page ) {
	$page_id   = awebooking_get_page_id( $page );
	$permalink = 0 < $page_id ? get_permalink( $page_id ) : get_home_url();

	return apply_filters( 'awebooking/get_' . $page . '_page_permalink', $permalink );
}

if ( ! function_exists( 'is_awebooking' ) ) :
	/**
	 * Returns true if on a page which uses AweBooking templates.
	 *
	 * @return bool
	 */
	function is_awebooking() {
		$is_awebooking = (
			is_room_type_archive() || is_room_type() ||
			is_check_availability_page() || is_booking_info_page() || is_booking_checkout_page()
		) ? true : false;

		return apply_filters( 'is_awebooking', $is_awebooking );
	}
endif;

if ( ! function_exists( 'is_room_type_archive' ) ) :
	/**
	 * Is current page is archive of "room_type".
	 *
	 * @return bool
	 */
	function is_room_type_archive() {
		return is_post_type_archive( AweBooking::ROOM_TYPE );
	}
endif;

if ( ! function_exists( 'is_room_type' ) ) :
	/**
	 * Returns true when viewing a single room-type.
	 *
	 * @return bool
	 */
	function is_room_type() {
		return is_singular( AweBooking::ROOM_TYPE );
	}
endif;

if ( ! function_exists( 'is_check_availability_page' ) ) :
	/**
	 * Returns true when viewing a "search availability results " page.
	 *
	 * @return bool
	 */
	function is_check_availability_page() {
		global $wp_query;

		$current_id = $wp_query->get_queried_object_id();
		$page_id = (int) awebooking( 'config' )->get( 'page_check_availability' );

		return ( is_page() && $current_id === $page_id );
	}
endif;

if ( ! function_exists( 'is_booking_info_page' ) ) :
	/**
	 * Returns true when viewing a "booking review" page.
	 *
	 * @return bool
	 */
	function is_booking_info_page() {
		global $wp_query;

		$current_id = $wp_query->get_queried_object_id();
		$page_id = (int) awebooking( 'config' )->get( 'page_booking' );

		return ( is_page() && $current_id === $page_id );
	}
endif;

if ( ! function_exists( 'is_booking_checkout_page' ) ) :
	/**
	 * Returns true when viewing a "booking checkout" page.
	 *
	 * @return bool
	 */
	function is_booking_checkout_page() {
		global $wp_query;

		$current_id = $wp_query->get_queried_object_id();
		$page_id = (int) awebooking( 'config' )->get( 'page_checkout' );

		return ( is_page() && $current_id === $page_id );
	}
endif;
