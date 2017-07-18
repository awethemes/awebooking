<?php
/**
 * A collect of functions for anyone scare OOP.
 *
 * @package AweBooking
 */

use AweBooking\Room_State;
use AweBooking\Support\Template;

/**
 * Get the available container instance.
 *
 * @param  string $make //.
 * @return mixed|AweBooking
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
 * @param  string $key     Optional, special key ID if need.
 * @param  mixed  $default Default value will be return if key not set,
 *                         if null pass, default setting value will be return.
 * @return mixed
 */
function awebooking_config( $key = null, $default = null ) {
	if ( is_null( $key ) ) {
		return awebooking()->make( 'config' );
	}

	return awebooking( 'config' )->get( $key, $default );
}

/**
 * Deprecated function, use `awebooking_config` instead.
 *
 * @param  string $key Optional, special key ID if need.
 * @return mixed
 */
function abkng_config( $key = null ) {
	_deprecated_function( __FUNCTION__, '3.0.0-beta', 'awebooking_config' );
	return awebooking_config( $key );
}

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
 * Get template part.
 * TODO: ...
 *
 * @param mixed  $slug slug.
 * @param string $name (default: '').
 */
function awebooking_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/awebooking/slug-name.php .
	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", AweBooking()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php .
	if ( ! $template && $name && file_exists( AweBooking()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = AweBooking()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/awebooking/slug.php .
	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", AweBooking()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'awebooking_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Returns true if on a page which uses AweBooking templates.
 *
 * @return boolean
 */
function is_awebooking() {
	$is_awebooking = (
		is_room_type_archive() || is_room_type() ||
		is_check_availability_page() || is_booking_info_page() || is_booking_checkout_page()
	) ? true : false;

	return apply_filters( 'is_awebooking', $is_awebooking );
}

if ( ! function_exists( 'is_room_type_archive' ) ) :
	/**
	 * Is current page is archive of "room_type".
	 *
	 * @return boolean
	 */
	function is_room_type_archive() {
		return is_post_type_archive( AweBooking::ROOM_TYPE );
	}
endif;

if ( ! function_exists( 'is_room_type' ) ) :
	/**
	 * Returns true when viewing a single room-type.
	 *
	 * @return boolean
	 */
	function is_room_type() {
		return is_singular( AweBooking::ROOM_TYPE );
	}
endif;

if ( ! function_exists( 'is_check_availability_page' ) ) :
	/**
	 * Returns true when viewing a "search availability results " page.
	 *
	 * @return boolean
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
	 * @return boolean
	 */
	function is_booking_info_page() {
		global $wp_query;

		$current_id = $wp_query->get_queried_object_id();
		$page_id = (int) awebooking( 'config' )->get( 'page_booking' );

		return ( is_page() && $current_id === $page_id );
	}
endif;

if ( ! function_exists( 'is_booking_checkout_page' ) ) {
	/**
	 * Returns true when viewing a "booking checkout" page.
	 *
	 * @return boolean
	 */
	function is_booking_checkout_page() {
		global $wp_query;

		$current_id = $wp_query->get_queried_object_id();
		$page_id = (int) awebooking( 'config' )->get( 'page_checkout' );

		return ( is_page() && $current_id === $page_id );
	}
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

/**
 * Sanitize price number.
 *
 * @param  string|numeric $number Raw numeric.
 * @return float
 */
function awebooking_sanitize_price( $number ) {
	return Formatting::format_decimal( $number, true );
}

/**
 * Get all order statuses.
 *
 * @return array
 */
function awebooking_get_booking_statuses() {
	return apply_filters( 'awebooking/order_statuses', [
		Booking::PENDING    => _x( 'Pending', 'Booking status', 'awebooking' ),
		Booking::PROCESSING => _x( 'Processing', 'Booking status', 'awebooking' ),
		Booking::COMPLETED  => _x( 'Completed', 'Booking status', 'awebooking' ),
		Booking::CANCELLED  => _x( 'Cancelled', 'Booking status', 'awebooking' ),
	]);
}

/**
 * Get the nice name for an order status.
 *
 * @param  string $status // ...
 * @return string
 */
function awebooking_get_booking_status_name( $status ) {
	$statuses = awebooking_get_booking_statuses();

	$status = 'awebooking-' === substr( $status, 0, 11 ) ? substr( $status, 11 ) : $status;

	return isset( $statuses[ 'awebooking-' . $status ] ) ? $statuses[ 'awebooking-' . $status ] : $status;
}



/**
 * Return list room states.
 *
 * @return array
 */
function awebooking_get_room_states() {
	return [
		Room_State::AVAILABLE   => esc_html__( 'Available', 'awebooking' ),
		Room_State::UNAVAILABLE => esc_html__( 'Unavailable', 'awebooking' ),
		Room_State::PENDING     => esc_html__( 'Pending', 'awebooking' ),
		Room_State::BOOKED      => esc_html__( 'Booked', 'awebooking' ),
	];
}

function awebooking_get_booking_request_query( $extra_args = array() ) {
	$raw = [ 'start-date', 'end-date', 'children', 'adults' ];
	$clean = [];
	foreach ( $raw as $key ) {
		$clean[ $key ] = isset( $_REQUEST[ $key ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) ) : '';
	}

	return array_merge( $clean, $extra_args );
}

function awebooking_get_common_titles() {
	return apply_filters( 'awebooking/customer_titles', array(
		'mr'   => __( 'Mr.', 'awebooking' ),
		'ms'   => __( 'Ms.', 'awebooking' ),
		'mrs'  => __( 'Mrs.', 'awebooking' ),
		'miss' => __( 'Miss.', 'awebooking' ),
		'dr'   => __( 'Dr.', 'awebooking' ),
		'prof' => __( 'Prof.', 'awebooking' ),
	));
}

function awebooking_get_extra_service_label( Service $extra_service, $before_value = '', $after_value = '' ) {
	$label = '';

	switch ( $extra_service->get_operation() ) {
		case Service::OP_ADD:
			$label = sprintf( esc_html__( '%2$s + %1$s %3$s to price', 'awebooking' ), $extra_service->get_price(), $before_value, $after_value );
			break;

		case Service::OP_ADD_DAILY:
			$label = sprintf( esc_html__( '%2$s + %1$s x night %3$s to price', 'awebooking' ), $extra_service->get_price(), $before_value, $after_value );
			break;

		case Service::OP_ADD_PERSON:
			$label = sprintf( esc_html__( '%2$s + %1$s x person %3$s to price', 'awebooking' ), $extra_service->get_price(), $before_value, $after_value );
			break;

		case Service::OP_ADD_PERSON_DAILY:
			$label = sprintf( esc_html__( '%2$s + %1$s x person x night %3$s to price', 'awebooking' ), $extra_service->get_price(), $before_value, $after_value );
			break;

		case Service::OP_SUB:
			$label = sprintf( esc_html__( '%2$s - %1$s %3$s from price', 'awebooking' ), $extra_service->get_price(), $before_value, $after_value );
			break;

		case Service::OP_SUB_DAILY:
			$label = sprintf( esc_html__( '%2$s - %1$s x night %3$s from price', 'awebooking' ), $extra_service->get_price(), $before_value, $after_value );
			break;

		case Service::OP_INCREASE:
			$label = sprintf( esc_html__( '%2$s + %1$s%% %3$s to price', 'awebooking' ), $extra_service->get_value(), $before_value, $after_value );
			break;

		case Service::OP_DECREASE:
			$label = sprintf( esc_html__( '%2$s - %1$s%% %3$s from price', 'awebooking' ), $extra_service->get_value(), $before_value, $after_value );
			break;
	}

	return $label;
}

/**
 * Set a cookie - wrapper for setcookie using WP constants.
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

function awebooking_get_booking_url( Availability $availability ) {

	$booking_link = get_the_permalink( intval( awebooking_config( 'page_booking' ) ) );

	$args = apply_filters( 'awebooking/get_booking_url', [
		'start-date' => Formatting::standard_date_format( $availability->get_check_in() ),
		'end-date' => Formatting::standard_date_format( $availability->get_check_out() ),
		'children' => $availability->get_children(),
		'adults' => $availability->get_adults(),
		'room-type' => $availability->get_room_type()->get_id(),
	] );

	return add_query_arg( $args, $booking_link );
}

function awebooking_get_first_term( $taxonomy, $args ) {
	$term = get_terms( $taxonomy, $args );

	if ( is_wp_error( $term ) ) {
		return;
	}

	if ( is_array( $term ) && isset( $term[0] ) ) {
		return $term[0];
	}

	// TODO: ...
	return $term;
}
