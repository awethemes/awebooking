<?php
namespace AweBooking\ICalendar\Reader\Adapter;

class Stream_Adapter implements Adapter_Interface {
	/**
	 * Get the data resource from a file.
	 *
	 * @param  string $input The input file path.
	 * @return resource
	 *
	 * @throws \InvalidArgumentException
	 */
	public function get( $input ) {
		if ( is_resource( $input ) ) {
			return $input;
		}

		$resource = fopen( $input, 'r' );
		if ( ! is_resource( $resource ) ) {
			throw new \InvalidArgumentException( 'Invalid resource or unreadable.' );
		}

		return $resource;
	}
}
