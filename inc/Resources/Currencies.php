<?php
namespace AweBooking\Resources;

use AweBooking\Model\Common\Currency;
use AweBooking\Support\Traits\Singleton;

class Currencies {
	use Singleton;

	/**
	 * The currencies repository.
	 *
	 * @var array
	 */
	protected $repository;

	/**
	 * Cache the resolved.
	 *
	 * @var array
	 */
	protected $resolved = [];

	/**
	 * Private constructor.
	 */
	private function __construct() {
		$this->repository = $this->setup_repository();
	}

	/**
	 * Get all currencies as array.
	 *
	 * @return array
	 */
	public function all() {
		return $this->repository->all();
	}

	/**
	 * Get a currency object.
	 *
	 * @param  string $code The currency code.
	 * @return \AweBooking\Model\Common\Currency
	 */
	public function get( $code ) {
		// Found in the resolved, return it.
		if ( array_key_exists( $code, $this->resolved ) ) {
			return $this->resolved[ $code ];
		}

		// No currency found, leave and return a placeholder.
		if ( ! $this->repository->has( $code ) ) {
			return new Currency( $code );
		}

		$args = $this->repository->get( $code );
		$currency = new Currency( $code, $args['name'], $args['symbol'] );

		// Cache this currency object.
		$this->resolved[ $code ] = $currency;

		return $currency;
	}

	/**
	 * Get currencies for the dropdown.
	 *
	 * @param  string $format Optinal display format.
	 * @return array
	 */
	public function get_for_dropdown( $format = null ) {
		return $this->repository->map( function( $currency, $code ) use ( $format ) {
			if ( ! $format ) {
				return $currency['name'];
			}

			return str_replace(
				[ '%code', '%name', '%symbol' ],
				[ $code, $currency['name'], $currency['symbol'] ],
				$format
			);
		})->all();
	}

	/**
	 * Get the repository.
	 *
	 * @return \AweBooking\Resources\Repository
	 */
	public function get_repository() {
		return $this->repository;
	}

	/**
	 * Setup the repository.
	 *
	 * @return \AweBooking\Resources\Repository
	 */
	protected function setup_repository() {
		$currencies = include trailingslashit( __DIR__ ) . 'data/currencies.php';

		return apply_filters( 'awebooking/currencies', new Repository( $currencies ) );
	}
}
