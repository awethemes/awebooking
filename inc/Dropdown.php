<?php
namespace AweBooking;

use AweBooking\Support\Utils as U;
use AweBooking\Model\Booking;

class Dropdown {
	/**
	 * Create a callback to call a method.
	 *
	 * @param  string $method     The method name.
	 * @param  array  $parameters The parameters.
	 * @return Clousure
	 */
	public static function cb( $method, $parameters = [] ) {
		return function() use ( $method, $parameters ) {
			return call_user_func_array( [ Dropdown::class, $method ], $parameters );
		};
	}

	/**
	 * Get the  week_days sort by "start_of_week".
	 *
	 * @param  string $day_label The day_label, "abbrev", "initial", "full".
	 * @return array
	 */
	public static function get_week_days( $day_label = 'full' ) {
		global $wp_locale;

		$week_days = [];
		$week_begins = (int) get_option( 'start_of_week' );

		for ( $i = 0; $i <= 6; $i++ ) {
			$wd = (int) ( $i + $week_begins ) % 7;
			$wd_name = $wp_locale->get_weekday( $wd );

			if ( 'initial' === $day_label ) {
				$wd_name = $wp_locale->get_weekday_initial( $wd_name );
			} elseif ( 'abbrev' === $day_label ) {
				$wd_name = $wp_locale->get_weekday_abbrev( $wd_name );
			}

			$week_days[ $wd ] = $wd_name;
		}

		return $week_days;
	}

	/**
	 * Get the reservation sources.
	 *
	 * @return array
	 */
	public static function get_reservation_sources() {
		return U::collect( awebooking()->make( 'reservation_sources' )->all() )
			->pluck( 'label', 'uid' )
			->toArray();
	}

	/**
	 * Get list payment methods for dropdown.
	 *
	 * @return array
	 */
	public static function get_payment_methods() {
		$methods = apply_filters( 'awebooking/base_payment_methods', [
			''     => esc_html__( 'N/A', 'awebooking' ),
			'cash' => esc_html__( 'Cash', 'awebooking' ),
		]);

		$gateways = awebooking()->make( 'gateways' )->enabled()
			->map( function( $m ) {
				return $m->get_method_title();
			})->all();

		return array_merge( $methods, $gateways );
	}

	/**
	 * Get list position for dropdown.
	 *
	 * @return array
	 */
	public static function get_currency_positions() {
		$symbol = awebooking( 'currency' )->get_symbol();

		return [ // @codingStandardsIgnoreStart
			Constants::CURRENCY_POS_LEFT        => sprintf( esc_html__( 'Left (%s99.99)', 'awebooking' ), $symbol ),
			Constants::CURRENCY_POS_RIGHT       => sprintf( esc_html__( 'Right (99.99%s)', 'awebooking' ), $symbol ),
			Constants::CURRENCY_POS_LEFT_SPACE  => sprintf( esc_html__( 'Left with space (%s 99.99)', 'awebooking' ), $symbol ),
			Constants::CURRENCY_POS_RIGHT_SPACE => sprintf( esc_html__( 'Right with space (99.99 %s)', 'awebooking' ), $symbol ),
		]; // @codingStandardsIgnoreEnd
	}

	/**
	 * Return list room states.
	 *
	 * @return array
	 */
	public static function get_room_states() {
		return [
			Constants::STATE_AVAILABLE   => esc_html__( 'Available', 'awebooking' ),
			Constants::STATE_UNAVAILABLE => esc_html__( 'Unavailable', 'awebooking' ),
			Constants::STATE_PENDING     => esc_html__( 'Pending', 'awebooking' ),
			Constants::STATE_BOOKED      => esc_html__( 'Booked', 'awebooking' ),
		];
	}

	/**
	 * Get all order statuses.
	 *
	 * @return array
	 */
	public static function get_booking_statuses() {
		return apply_filters( 'awebooking/get_booking_statuses', [
			Booking::PENDING    => _x( 'Pending',    'Booking status', 'awebooking' ),
			Booking::PROCESSING => _x( 'Processing', 'Booking status', 'awebooking' ),
			Booking::COMPLETED  => _x( 'Completed',  'Booking status', 'awebooking' ),
			Booking::CANCELLED  => _x( 'Cancelled',  'Booking status', 'awebooking' ),
		]);
	}

	/**
	 * Get all service operations.
	 *
	 * @return array
	 */
	public static function get_service_operations() {
		return apply_filters( 'awebooking/service_operations', [
			Service::OP_ADD               => esc_html__( 'Add to price', 'awebooking' ),
			Service::OP_ADD_DAILY         => esc_html__( 'Add to price per night', 'awebooking' ),
			Service::OP_ADD_PERSON        => esc_html__( 'Add to price per person', 'awebooking' ),
			Service::OP_ADD_PERSON_DAILY  => esc_html__( 'Add to price per person per night', 'awebooking' ),
			Service::OP_SUB               => esc_html__( 'Subtract from price', 'awebooking' ),
			Service::OP_SUB_DAILY         => esc_html__( 'Subtract from price per night', 'awebooking' ),
			Service::OP_INCREASE          => esc_html__( 'Increase price by % amount', 'awebooking' ),
			Service::OP_DECREASE          => esc_html__( 'Decrease price by % amount', 'awebooking' ),
		]);
	}
}
