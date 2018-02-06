<?php
namespace AweBooking\Money;

use AweBooking\Support\Utils as U;

class Currencies {
	/**
	 * List all currency.
	 *
	 * @var array
	 */
	protected $currencies;

	protected $current;

	/**
	 * The class singleton instance.
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * Get the class instance.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		static::$instance = $this;

		$this->currencies = $this->get_from_source();

		$this->set_current( 'USD' );
	}

	/**
	 * Get all currencies.
	 *
	 * @return array
	 */
	public function raw() {
		return $this->currencies;
	}

	public function all() {
	}

	public function get( $currency ) {
		return new Currency( $currency, $this->get_args( $currency ) );
	}

	public function get_current() {
		return $this->current;
	}

	public function set_current( $currency ) {
		$this->current = $this->get( $currency );
	}

	/**
	 * Get currency by code.
	 *
	 * @param  string $code Currency code.
	 * @return array|null
	 */
	public function get_args( $code ) {
		return isset( $this->currencies[ $code ] ) ? $this->currencies[ $code ] : null;
	}

	/**
	 * Add a currency in to the manager.
	 *
	 * @param  string|Currency $code Unique currency code or Currency instance.
	 * @param  array|null      $args Currency args.
	 * @return bool
	 */
	public function add_currency( $code, array $args = null ) {
		if ( empty( $args['name'] ) || empty( $args['symbol'] ) ) {
			return false;
		}

		$this->currencies[ $code ] = $args;

		return true;
	}

	/**
	 * Get list currencies for dropdown.
	 *
	 * @param  string $format Optinal display format.
	 * @return array
	 */
	public function get_for_dropdown( $format = null ) {
		$currencies = $this->raw();

		// Walk through currencies array and modify the display value.
		array_walk( $currencies, function( &$currency, $code ) use ( $format ) {
			if ( ! $format ) {
				$currency = $currency['name'];
				return;
			}

			$currency = str_replace(
				[ '%code', '%name', '%symbol' ],
				[ $code, $currency['name'], $currency['symbol'] ],
				$format
			);
		});

		return $currencies;
	}

	/**
	 * Get currencies from source.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	protected function get_from_source() {
		$currencies = include trailingslashit( __DIR__ ) . 'resources/currencies.php';

		return apply_filters( 'awebooking/currencies', U::collect( $currencies ) );
	}
}
