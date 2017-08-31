<?php
namespace AweBooking\Currency;

class Currency_Manager {
	/**
	 * List all currency.
	 *
	 * @var array
	 */
	protected $currencies;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->currencies = apply_filters( 'awebooking/currencies',
			include( trailingslashit( __DIR__ ) . '/currencies.php' )
		);
	}

	/**
	 * Get currency by code.
	 *
	 * @param  string $code Currency code.
	 * @return array|null
	 */
	public function get_currency( $code = null ) {
		$code = is_null( $code ) ? $this->get_current_currency() : $code;

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
		if ( $code instanceof Currency ) {
			$code = $code->get_code();
			$args = $code->to_array();
		}

		if ( empty( $args['name'] ) || empty( $args['symbol'] ) ) {
			return false;
		}

		$this->currencies[ $code ] = $args;

		return true;
	}

	/**
	 * Get all currencies.
	 *
	 * @return array
	 */
	public function get_currencies() {
		return $this->currencies;
	}

	/**
	 * Return current currency code.
	 *
	 * @return array
	 */
	public function get_current_currency() {
		return awebooking_option( 'currency' );
	}

	/**
	 * Get list currencies for dropdown.
	 *
	 * @param  string $format Optinal display format.
	 * @return array
	 */
	public function get_for_dropdown( $format = null ) {
		$currencies = $this->get_currencies();

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
}
