<?php
namespace AweBooking\ICalendar\Reader\Adapter;

class Remote_Adapter implements Adapter_Interface {
	/**
	 * Get the data from a URL.
	 *
	 * @param  string $input The input file URL.
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	public function get( $input ) {
		$request = wp_remote_get( $input, [
			'timeout' => 30,
		]);

		if ( is_wp_error( $request ) ) {
			throw new \RuntimeException( $request->get_error_message() );
		}

		return wp_remote_retrieve_body( $request );
	}
}
