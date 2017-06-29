<?php
namespace AweBooking\Currency;

use AweBooking\Interfaces\Currency as Currency_Interface;

class Currency implements Currency_Interface {
	/**
	 * Currency code.
	 *
	 * @var string
	 */
	protected $code;

	/**
	 * Store currency array data.
	 *
	 * @var array
	 */
	protected $currency;

	/**
	 * Create a currency object.
	 *
	 * @param string $code     Currency code.
	 * @param array  $currency Array of currency data.
	 */
	public function __construct( $code, array $currency = [] ) {
		if ( empty( $currency ) ) {
			$currency = awebooking( 'currency_manager' )->get_currency( $code );
		}

		$this->code = $code;
		$this->currency = $currency;
	}

	/**
	 * Gets the alphabetic currency code.
	 *
	 * @return string
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * Gets the currency name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->currency['name'];
	}

	/**
	 * Gets the currency symbol.
	 *
	 * @return string
	 */
	public function get_symbol() {
		return $this->currency['symbol'];
	}

	/**
	 * Get currency as array.
	 *
	 * @return array
	 */
	public function to_array() {
		return [
			'code'   => $this->get_code(),
			'name'   => $this->get_name(),
			'symbol' => $this->get_symbol(),
		];
	}

	/**
	 * Returns the string representation of the currency.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->get_code();
	}
}
