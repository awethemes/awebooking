<?php

use AweBooking\Support\Debug\Dumper;

if ( ! function_exists( 'dd' ) ) {
	/**
	 * Dump the passed variables and end the script.
	 *
	 * @param  mixed ...$args The dump arguments.
	 * @return void
	 */
	function dd( ...$args ) {
		foreach ( $args as $x ) {
			( new Dumper )->dump( $x );
		}

		die( 1 );
	}
}
