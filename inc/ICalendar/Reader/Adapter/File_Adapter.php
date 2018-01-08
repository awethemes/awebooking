<?php
namespace AweBooking\ICalendar\Reader\Adapter;

class File_Adapter implements Adapter_Interface {
	/**
	 * Get the data from file.
	 *
	 * @param  string $input The input file path.
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	public function get( $input ) {
		if ( is_file( $input ) && is_readable( $input ) ) {
			return @file_get_contents( $input );
		}

		throw new \InvalidArgumentException( 'File not found or unreadable.' );
	}
}
