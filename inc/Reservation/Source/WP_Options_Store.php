<?php
namespace AweBooking\Reservation\Source;

class WP_Options_Store implements Store {
	/* Constants */
	const OPTION_KEY = '_awebooking_reservation_sources';

	/**
	 * Database sources data.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->fetch();
	}

	/**
	 * {@inheritdoc}
	 */
	public function all() {
		return $this->data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $key ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function has( $key ) {
		return ! is_null( $this->get( $key ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove( $key ) {
		if ( ! $this->has( $key ) ) {
			return false;
		}

		unset( $this->data[ $key ] );

		// Store...
	}

	/**
	 * {@inheritdoc}
	 */
	public function put( $source ) {
	}

	/**
	 * Fetch all sources data.
	 *
	 * @return void
	 */
	public function fetch() {
		$this->data = (array) get_option( static::OPTION_KEY, [] );
	}

	/**
	 * Flush the sources data.
	 *
	 * @return void
	 */
	public function flush() {
		// Update new options.

		$this->fetch();
	}
}
