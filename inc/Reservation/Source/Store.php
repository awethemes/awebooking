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
	 * @return mixed
	 */
	public function get( $key );

	/**
	 * Put a source in the store.
	 *
	 * @param  mixed $source The source data.
	 * @return mixed
	 */
	public function insert( $source );

	/**
	 * Put a source in the store.
	 *
	 * @param  string $key  The key representer for the source.
	 * @param  array  $data The update data.
	 * @return boolean
	 */
	public function update( $key, $data );

	/**
	 * Remove a source in the store.
	 *
	 * @param  string $key The key representer for the source.
	 * @return boolean
	 */
	public function delete( $key );
}
