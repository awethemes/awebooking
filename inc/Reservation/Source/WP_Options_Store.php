<?php
namespace AweBooking\Reservation\Source;

use Illuminate\Support\Arr;

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
		return Arr::except( $this->data, [ 'direct_website', 'direct_walk_in', 'direct_phone', 'direct_email' ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $key ) {
		return array_key_exists( $key, $this->data ) ? $this->data[ $key ] : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function insert( $source ) {
		$source = $this->get_default_args( $source );

		if ( ! $this->is_valid_source( $source ) ) {
			return false;
		}

		// Prevent insert a exists source.
		if ( array_key_exists( $source['uid'], $this->data ) ) {
			return false;
		}

		$this->data[ $source['uid'] ] = $source;

		return $this->perform_update_option();
	}

	/**
	 * {@inheritdoc}
	 */
	public function update( $key, $data ) {
		if ( ! array_key_exists( $key, $this->data ) ) {
			return false;
		}

		unset( $data['uid'] );
		unset( $data['type'] );

		$this->data[ $key ] = array_merge( $this->data[ $key ], $data );

		return $this->perform_update_option( true );
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete( $key ) {
		if ( ! array_key_exists( $key, $this->data ) ) {
			return false;
		}

		unset( $this->data[ $key ] );

		return $this->perform_update_option( true );
	}

	/**
	 * Fetch all sources data.
	 *
	 * @return void
	 */
	public function fetch() {
		$data = (array) get_option( static::OPTION_KEY, [] );

		if ( empty( $data ) ) {
			$this->data = [];
			return;
		}

		$this->data = array_filter( $data, function( $source ) {
			return $this->is_valid_source( $source );
		});
	}

	/**
	 * Flush the sources data.
	 *
	 * @return void
	 */
	public function flush() {
		$this->data = null;

		$this->fetch();
	}

	/**
	 * Determines a source type is valid to store.
	 *
	 * @param  array $source The source data.
	 * @return boolean
	 */
	protected function is_valid_source( $source ) {
		if ( empty( $source['type'] ) || empty( $source['uid'] ) ) {
			return false;
		}

		if ( ! in_array( $source['type'], [ 'direct', 'third_party' ] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the source default args in the store.
	 *
	 * @param  array $source The raw source.
	 * @return array
	 */
	protected function get_default_args( $source ) {
		$source = wp_parse_args( $source, [
			'type'       => 'direct',
			'uid'        => '',
			'name'       => '',
			'surcharge'  => [],
			'enabled'    => true,
		]);

		// For the third_party sources.
		if ( 'third_party' === $source['type'] ) {
			$source = wp_parse_args( $source, [
				'group'      => '',
				'commission' => [],
			]);
		}

		return $source;
	}

	/**
	 * Perform update the sources data.
	 *
	 * @param  boolean $refresh Refresh after updated.
	 * @return boolean
	 */
	public function perform_update_option( $refresh = false ) {
		$updated = update_option( static::OPTION_KEY, $this->data, true );

		if ( $updated && $refresh ) {
			$this->flush();
		}

		return $updated;
	}
}
