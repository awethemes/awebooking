<?php
namespace AweBooking\Money;

class Currency {
	/**
	 * Currency code.
	 *
	 * @var string
	 */
	protected $code;

	/**
	 * Currency array args.
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 * Create a currency object.
	 *
	 * @param string $code Currency code.
	 * @param array  $args Optional, array of currency args.
	 */
	public function __construct( $code, array $args = [] ) {
		$this->code = $code;
		$this->args = $this->parse_currency_args( $args );
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
		return $this->args['name'];
	}

	/**
	 * Gets the currency symbol.
	 *
	 * @return string
	 */
	public function get_symbol() {
		return $this->args['symbol'];
	}

	/**
	 * Parse currency args.
	 *
	 * @param  array|null $args Optional, currency args.
	 * @return array|null
	 */
	protected function parse_currency_args( $args ) {
		return wp_parse_args( $args, [
			'name'   => '',
			'symbol' => '',
		]);
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
