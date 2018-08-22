<?php
namespace AweBooking\Component\Currency;

use AweBooking\Support\Traits\Singleton;

final class ISO4217 implements \Countable, \IteratorAggregate {
	use Singleton;

	/* Constants */
	const KEY_NAME   = 'name';
	const KEY_CODE   = 'alpha3';
	const KEY_ALPHA3 = 'alpha3';

	/**
	 * The currencies dataset.
	 *
	 * @var array
	 */
	private $currencies;

	/**
	 * Constructor.
	 *
	 * @param array[] $currencies Replace default dataset with given array.
	 */
	public function __construct( array $currencies = [] ) {
		static::$instance = $this;

		if ( empty( $currencies ) ) {
			$currencies = apply_filters( 'awebooking_currencies_dataset', include __DIR__ . '/dataset.php' );
		}

		$this->currencies = $currencies;
	}

	/**
	 * Returns all currencies.
	 *
	 * @return array[]
	 */
	public function all() {
		return $this->currencies;
	}

	/**
	 * Lookup ISO4217 data by name or code.
	 *
	 * @throws \OutOfBoundsException If key does not exist in dataset.
	 *
	 * @param  string $currency The currency name or alpha3 code.
	 * @return array
	 */
	public function find( $currency ) {
		if ( preg_match( '/^[a-zA-Z]{3}$/', $currency ) ) {
			return $this->alpha3( $currency );
		}

		return $this->name( $currency );
	}

	/**
	 * Lookup ISO4217 data by name identifier.
	 *
	 * @throws \OutOfBoundsException If key does not exist in dataset.
	 *
	 * @param  string $name The currency name.
	 * @return array
	 */
	public function name( $name ) {
		return $this->lookup( static::KEY_NAME, $name );
	}

	/**
	 * Lookup ISO4217 data by alpha3 identifier.
	 *
	 * @throws \DomainException
	 * @throws \OutOfBoundsException If key does not exist in dataset.
	 *
	 * @param  string $code The code name.
	 * @return array
	 */
	public function code( $code ) {
		return $this->alpha3( $code );
	}

	/**
	 * Lookup ISO4217 data by alpha3 identifier.
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
		return count( $this->currencies );
	}

	/**
	 * Returns the Iterator.
	 *
	 * @see \IteratorAggregate
	 *
	 * @return \Generator
	 */
	public function getIterator() {
		foreach ( $this->currencies as $currency ) {
			yield $currency;
		}
	}

	/**
	 * Lookup ISO4217 data by given identifier.
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
		foreach ( $this->currencies as $currency ) {
			if ( 0 === strcasecmp( $value, $currency[ $key ] ) ) {
				return $currency;
			}
		}

		throw new \OutOfBoundsException( sprintf( 'No "%s" key found matching: %s', $key, $value ) );
	}
}
