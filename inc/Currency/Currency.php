<?php
namespace AweBooking\Currency;

class Currency {
	/* Position constants */
	const POS_LEFT = 'left';
	const POS_RIGHT = 'right';
	const POS_LEFT_SPACE = 'left_space';
	const POS_RIGHT_SPACE = 'right_space';

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
	protected $args;

	/**
	 * Create a currency object.
	 *
	 * TODO: Maybe we need trigger an error when nothing currency found.
	 *
	 * @param string $code Currency code.
	 * @param array  $args Optional, an array of currency args.
	 *                     If null or empty, `use currency_manager` to fetch args.
	 */
	public function __construct( $code, array $args = null ) {
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
	 * Checks whether this currency is the same as an other.
	 *
	 * @param Currency $other Other currency.
	 * @return bool
	 */
	public function equals( Currency $other ) {
		return $this->code === $other->code;
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

	/**
	 * Parse currency args.
	 *
	 * @param  array|null $args Optional, currency args.
	 * @return array|null
	 */
	protected function parse_currency_args( $args ) {
		if ( empty( $args ) ) {
			return awebooking( 'currency_manager' )->get_currency( $this->code );
		}

		return wp_parse_args( $args, [
			'name'   => '',
			'symbol' => '',
		]);
	}
}
