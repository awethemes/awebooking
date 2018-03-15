<?php
namespace AweBooking\Model\Common;

class Currency {
	/**
	 * The currency code.
	 *
	 * @var string
	 */
	protected $code;

	/**
	 * The currency name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The currency symbol.
	 *
	 * @var string
	 */
	protected $symbol;

	/**
	 * Create new currency.
	 *
	 * @param string $code   The code name.
	 * @param string $name   The display name.
	 * @param string $symbol The symbol.
	 */
	public function __construct( $code, $name = '', $symbol = '' ) {
		$this->code   = $code;
		$this->name   = $name;
		$this->symbol = $symbol;
	}

	/**
	 * Get the alphabetic currency code.
	 *
	 * @return string
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * Get the currency name.
	 *
	 * @return string
	 */
	public function get_name() {
		return apply_filters( 'awebooking/currency_name', $this->name, $this->code );
	}

	/**
	 * Get the currency symbol.
	 *
	 * @return string
	 */
	public function get_symbol() {
		return apply_filters( 'awebooking/currency_symbol', $this->symbol, $this->code );
	}

	/**
	 * Get the object as array.
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
}
