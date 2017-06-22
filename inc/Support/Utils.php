<?php
namespace AweBooking\Support;

use AweBooking\Booking;
use AweBooking\AweBooking;
use AweBooking\Room_State;
use AweBooking\Interfaces\Availability;

class Utils {
	/**
	 * Get all order statuses.
	 *
	 * @return array
	 */
	public static function get_booking_statuses() {
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
	public static function get_booking_status_name( $status ) {
		$statuses = static::get_booking_statuses();

		$status = 'awebooking-' === substr( $status, 0, 11 ) ? substr( $status, 11 ) : $status;

		return isset( $statuses[ 'awebooking-' . $status ] ) ? $statuses[ 'awebooking-' . $status ] : $status;
	}

	public static function get_common_titles() {
		return apply_filters( 'awebooking/customer_titles', array(
			'mr'   => __( 'Mr.', 'awebooking' ),
			'ms'   => __( 'Ms.', 'awebooking' ),
			'mrs'  => __( 'Mrs.', 'awebooking' ),
			'miss' => __( 'Miss.', 'awebooking' ),
			'dr'   => __( 'Dr.', 'awebooking' ),
			'prof' => __( 'Prof.', 'awebooking' ),
		));
	}

	/**
	 * Set a cookie - wrapper for setcookie using WP constants.
	 *
	 * @param  string  $name   Name of the cookie being set.
	 * @param  string  $value  Value of the cookie.
	 * @param  integer $expire Expiry of the cookie.
	 * @param  string  $secure Whether the cookie should be served only over https.
	 */
	public static function setcookie( $name, $value, $expire = 0, $secure = null ) {
		$secure = is_null( $secure ) ? is_ssl() : $secure;

		if ( ! headers_sent() ) {
			setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, $secure );
		} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			headers_sent( $file, $line );
			trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
		}
	}

	/**
	 * Return list room states.
	 *
	 * @return array
	 */
	public static function get_room_states() {
		return [
			Room_State::AVAILABLE   => esc_html__( 'Available', 'awebooking' ),
			Room_State::UNAVAILABLE => esc_html__( 'Unavailable', 'awebooking' ),
			Room_State::PENDING     => esc_html__( 'Pending', 'awebooking' ),
			Room_State::BOOKED      => esc_html__( 'Booked', 'awebooking' ),
		];
	}

	public static function get_booking_request_query( $extra_args = array() ) {
		$raw = [ 'start-date', 'end-date', 'children', 'adults' ];
		$clean = [];
		foreach ( $raw as $key ) {
			$clean[ $key ] = isset( $_REQUEST[ $key ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) ) : '';
		}

		return array_merge( $clean, $extra_args );
	}

	public static function get_booking_url( Availability $availability ) {

		$booking_link = get_the_permalink( intval( abkng_config( 'page_booking' ) ) );

		$args = apply_filters( 'awebooking/get_booking_url', [
			'start-date' => Formatting::standard_date_format( $availability->get_check_in() ),
			'end-date' => Formatting::standard_date_format( $availability->get_check_out() ),
			'children' => $availability->get_children(),
			'adults' => $availability->get_adults(),
			'room-type' => $availability->get_room_type()->get_id(),
		] );

		return add_query_arg( $args, $booking_link );
	}

	public static function get_first_term( $taxonomy, $args ) {
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

	public static function get_hotel_location_default() {

		if ( abkng_config( 'location_default' ) ) {
			$term_default = get_term( intval( abkng_config( 'location_default' ) ), AweBooking::HOTEL_LOCATION );
		} else {
			$term_default = static::get_first_term( AweBooking::HOTEL_LOCATION, array(
				'hide_empty' => false,
			) );
		}

		return $term_default;
	}

	public static function get_admin_notify_emails() {
		$admin_emails = [];

		if ( abkng_config( 'email_admin_notify' ) ) {
			$admin_emails[] = get_option( 'admin_email' );
		}

		$another_emails = abkng_config( 'email_notify_another_emails' );
		if ( ! empty( $another_emails ) ) {
			$another_emails = array_map( 'trim', explode( ',', $another_emails ) );
			$admin_emails   = array_merge( $admin_emails, $another_emails );
		}

		return $admin_emails;
	}
}
