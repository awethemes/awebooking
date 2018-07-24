<?php
namespace AweBooking\Component\Country;

use AweBooking\Support\Traits\Singleton;

final class ISO3166 implements \Countable, \IteratorAggregate {
	use Singleton;

	/* Constants */
	const KEY_NAME    = 'name';
	const KEY_ALPHA2  = 'alpha2';
	const KEY_ALPHA3  = 'alpha3';

	/**
	 * The countries dataset.
	 *
	 * @var array
	 */
	private $countries;

	/**
	 * Constructor.
	 *
	 * @param array[] $countries Replace default dataset with given array.
	 */
	public function __construct( array $countries = [] ) {
		static::$instance = $this;

		if ( empty( $countries ) ) {
			$countries = apply_filters( 'abrs_countries_dataset', include __DIR__ . '/dataset.php' );
		}

		$this->countries = $countries;
	}

	/**
	 * Returns all countries.
	 *
	 * @return array[]
	 */
	public function all() {
		return $this->countries;
	}

	/**
	 * Lookup ISO3166-1 data by name or code.
	 *
	 * @throws \OutOfBoundsException If key does not exist in dataset.
	 *
	 * @param  string $country The country name or alpha2 or alpha3 code.
	 * @return array
	 */
	public function find( $country ) {
		if ( preg_match( '/^[a-zA-Z]{2}$/', $country ) ) {
			return $this->alpha2( $country );
		}

		if ( preg_match( '/^[a-zA-Z]{3}$/', $country ) ) {
			return $this->alpha3( $country );
		}

		return $this->name( $country );
	}

	/**
	 * Lookup ISO3166-1 data by name identifier.
	 *
	 * @throws \OutOfBoundsException If key does not exist in dataset.
	 *
	 * @param  string $name The country name.
	 * @return array
	 */
	public function name( $name ) {
		return $this->lookup( static::KEY_NAME, $name );
	}

	/**
	 * Lookup ISO3166-1 data by alpha2 identifier.
	 *
	 * @throws \DomainException
	 * @throws \OutOfBoundsException If key does not exist in dataset.
	 *
	 * @param  string $alpha2 The alpha2 code (2 characters).
	 * @return array
	 */
	public function alpha2( $alpha2 ) {
		if ( ! preg_match( '/^[a-zA-Z]{2}$/', $alpha2 ) ) {
			throw new \DomainException( sprintf( 'Not a valid alpha2 key: %s', $alpha2 ) );
		}

		return $this->lookup( static::KEY_ALPHA2, $alpha2 );
	}

	/**
	 * Lookup ISO3166-1 data by alpha3 identifier.
	 *
	 * @throws \DomainException
	 * @throws \OutOfBoundsException If key does not exist in dataset.
	 *
	 * @param  string $alpha3 The alpha3 code (3 characters).
	 * @return array
	 */
	public function alpha3( $alpha3 ) {
		if ( ! preg_match( '/^[a-zA-Z]{3}$/', $alpha3 ) ) {
			throw new \DomainException( sprintf( 'Not a valid alpha3 key: %s', $alpha3 ) );
		}

		return $this->lookup( static::KEY_ALPHA3, $alpha3 );
	}

	/**
	 * Returns the dataset count.
	 *
	 * @see \Countable
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->countries );
	}

	/**
	 * Returns the Iterator.
	 *
	 * @see \IteratorAggregate
	 *
	 * @return \Generator
	 */
	public function getIterator() {
		foreach ( $this->countries as $country ) {
			yield $country;
		}
	}

	/**
	 * Lookup ISO3166-1 data by given identifier.
	 *
	 * Looks for a match against the given key for each entry in the dataset.
	 *
	 * @throws \OutOfBoundsException If key does not exist in dataset.
	 *
	 * @param  string $key   The search key.
	 * @param  string $value The search value.
	 * @return array
	 */
	protected function lookup( $key, $value ) {
		foreach ( $this->countries as $country ) {
			if ( 0 === strcasecmp( $value, $country[ $key ] ) ) {
				return $country;
			}
		}

		throw new \OutOfBoundsException( sprintf( 'No "%s" key found matching: %s', $key, $value ) );
	}
}
