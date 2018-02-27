<?php
namespace AweBooking\Support;

use AweBooking\Support\Debug\Dumper;
use Illuminate\Support\Collection as Base_Collection;

class Collection extends Base_Collection {
	/**
	 * Map the values into a new class.
	 *
	 * @param  string $class The class name.
	 * @return static
	 */
	public function map_into( $class ) {
		return $this->map( function ( $value, $key ) use ( $class ) {
			return new $class( $value, $key );
		});
	}

	/**
	 * Dump the collection and end the script.
	 *
	 * @return void
	 */
	public function dd() {
		http_response_code( 500 );

		call_user_func_array( [ $this, 'dump' ], func_get_args() );

		die( 1 );
	}

	/**
	 * Dump the collection.
	 *
	 * @return $this
	 */
	public function dump() {
		( new static( func_get_args() ) )
			->push( $this )
			->each( function ( $item ) {
				( new Dumper )->dump( $item );
			});

		return $this;
	}

	/**
	 * Alias of `toArray` method.
	 *
	 * @return array
	 */
	public function to_array() {
		return $this->toArray();
	}

	/**
	 * Alias of `toJson` method.
	 *
	 * @param  int $options JSON encode options.
	 * @return string
	 */
	public function to_json( $options = 0 ) {
		return $this->toJson( $options );
	}
}
