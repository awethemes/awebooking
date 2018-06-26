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

		trigger_error( sprintf( "Unknown getter '%s'", esc_html( $name ) ), E_USER_WARNING );
	}
}
