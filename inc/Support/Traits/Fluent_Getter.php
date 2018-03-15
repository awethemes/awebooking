<?php
namespace AweBooking\Support\Traits;

trait Fluent_Getter {
	/**
	 * Magic isset method.
	 *
	 * @param  string $property The property name.
	 * @return bool
	 */
	public function __isset( $property ) {
		return null !== $this->__get( $property );
	}

	/**
	 * Magic getter method.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __get( $property ) {
		if ( method_exists( $this, $method = "get_{$property}" ) ) {
			return $this->{$method}();
		}
	}
}
