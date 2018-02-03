<?php
namespace AweBooking;

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
}
