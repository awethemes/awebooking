<?php
namespace AweBooking;

use AweBooking\Support\Utils as U;

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
		$methods = [
			''     => esc_html__( 'N/A', 'awebooking' ),
			'cash' => esc_html__( 'Cash', 'awebooking' ),
		];

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
}
