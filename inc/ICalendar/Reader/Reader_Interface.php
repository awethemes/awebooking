<?php
namespace AweBooking\ICalendar\Reader;

use AweBooking\ICalendar\Reader\Adapter\Adapter_Interface;

interface Reader_Interface {
	/**
	 * Get the source.
	 *
	 * @return mixed
	 */
	public function get_source();

	/**
	 * Set the source.
	 *
	 * @param  mixed $source The source, URL, contents, resources, etc...
	 * @return $this
	 */
	public function set_source( $source );

	/**
	 * Get the adapter.
	 *
	 * @return mixed
	 */
	public function get_adapter();

	/**
	 * Set the adapter.
	 *
	 * @param  Adapter_Interface $adapter The adapter.
	 * @return $this
	 */
	public function set_adapter( Adapter_Interface $adapter );

	/**
	 * Read the source.
	 *
	 * @return Reader_Result
	 */
	public function read();
}
