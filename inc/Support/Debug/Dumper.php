<?php
namespace AweBooking\Support\Debug;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
 * This source is take from laravel/framework
 * under MIT license, copyright (c) Taylor Otwell.
 *
 * @link https://github.com/laravel/framework/blob/5.5/src/Illuminate/Support/Debug/Dumper.php
 */
class Dumper {
	/**
	 * Dump a value with elegance.
	 *
	 * @param  mixed $value The dump value.
	 * @return void
	 */
	public function dump( $value ) {
		if ( class_exists( CliDumper::class ) ) {
			$dumper = in_array( PHP_SAPI, [ 'cli', 'phpdbg' ] ) ? new CliDumper : new Html_Dumper;
			$dumper->dump( (new VarCloner())->cloneVar( $value ) );
		} else {
			var_dump( $value );
		}
	}
}
