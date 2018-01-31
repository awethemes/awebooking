<?php
namespace AweBooking\Reservation\Source;

use AweBooking\Model\Source;
use AweBooking\Support\Collection;

class Manager {
	/**
	 * Reservation sources implementation.
	 *
	 * @var array \AweBooking\Model\Source[]
	 */
	protected $sources = [];

	/**
	 * Constructor.
	 *
	 * @param array $sources The initial sources.
	 */
	public function __construct( array $sources = [] ) {
		foreach ( $sources as $source ) {
			$this->register( $source );
		}
	}

	/**
	 * Get a source by ID.
	 *
	 * @param  string $source The source ID.
	 * @return \AweBooking\Model\Source
	 */
	public function get( $source ) {
		return $this->registered( $source ) ? $this->sources[ $source ] : null;
	}

	/**
	 * Returns all sources.
	 *
	 * @return array \AweBooking\Model\Source[]
	 */
	public function all() {
		return $this->sources;
	}

	/**
	 * Add a new reservation source.
	 *
	 * @param  \AweBooking\Model\Source $source The source implementation.
	 * @return $this
	 */
	public function register( Source $source ) {
		$this->sources[ $source->get_uid() ] = $source;

		return $this;
	}

	/**
	 * Deregister a source.
	 *
	 * @param  string $source The source ID.
	 * @return void
	 */
	public function deregister( $source ) {
		$source = $this->parse_source_uid( $source );

		unset( $this->sources[ $source ] );
	}

	/**
	 * Determines a source has been registered.
	 *
	 * @param  string $source The source ID.
	 * @return bool
	 */
	public function registered( $source ) {
		$source = $this->parse_source_uid( $source );

		return array_key_exists( $source, $this->sources );
	}

	/**
	 * Parse the source_uid.
	 *
	 * @param  mixed $source The source.
	 * @return string
	 */
	protected function parse_source_uid( $source ) {
		if ( $source instanceof Source ) {
			return $source->get_uid();
		}

		return $source;
	}

	/**
	 * Get the sources as a Collection.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function to_collection() {
		return new Collection( $this->sources );
	}

	/**
	 * Get all direct sources.
	 *
	 * @return array \AweBooking\Model\Source[]
	 */
	public function get_direct_sources() {
		return $this->to_collection()->filter( function( $source ) {
			return $source instanceof Source;
		})->all();
	}

	/**
	 * Get all third party sources.
	 *
	 * @return array Third_Party_Source[]
	 */
	public function get_third_party_sources() {
		return $this->to_collection()->filter( function( $source ) {
			return $source instanceof Third_Party_Source;
		})->all();
	}
}
