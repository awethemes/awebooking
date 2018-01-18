<?php
namespace AweBooking\Reservation\Source;

interface Store {
	/**
	 * Get all sources.
	 *
	 * @return array
	 */
	public function all();

	/**
	 * Get reservation source by key.
	 *
	 * @param  string $key The key representer for the source.
	 * @return \AweBooking\Reservation\Source\Source
	 */
	public function get( $key );

	/**
	 * Determines if a source available in the store.
	 *
	 * @param  string $key The key representer for the source.
	 * @return boolean
	 */
	public function has( $key );

	/**
	 * Remove a source in the store.
	 *
	 * @param  string $key The key representer for the source.
	 * @return boolean
	 */
	public function remove( $key );

	/**
	 * Put a source in the store.
	 *
	 * @param  \AweBooking\Reservation\Source\Source $source The source implementation.
	 * @return \AweBooking\Reservation\Source\Source
	 */
	public function put( Source $source );
}
