<?php

namespace AweBooking\Support\Traits;

trait Fluent_Getter {
	/**
	 * Magic check isset a method.
	 *
	 * @param  string $name The isset name.
	 * @return bool
	 */
	public function __isset( $name ) {
		return method_exists( $this, "get_{$name}" );
	}

	/**
	 * Magic getter a method.
	 *
	 * @param  string $name The getter name.
	 * @return mixed
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __get( $name ) {
		if ( method_exists( $this, $method = "get_{$name}" ) ) {
			return $this->{$method}();
		}

		_doing_it_wrong(
			get_class( $this ) . '::$' . $name, // @codingStandardsIgnoreLine
			sprintf( "Unknown getter '%s'", esc_html( $name ) ),
			'3.1.0'
		);
	}
}
