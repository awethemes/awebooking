<?php
namespace AweBooking\Currency;

use AweBooking\Interfaces\Config;
use AweBooking\Interfaces\Currency as Currency_Interface;

class Currency_Manager {
	/**
	 * AweBooking Config instance.
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * List all currency.
	 *
	 * @var array
	 */
	protected $currencies;

	/**
	 * Constructor.
	 *
	 * @param Config $config AweBooking Config instance.
	 */
	public function __construct( Config $config ) {
		$this->config = $config;

		$this->currencies = apply_filters( 'awebooking/currencies',
			include( trailingslashit( __DIR__ ) . '/currencies.php' )
		);
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
	 * Return current currency code.
	 *
	 * @return array
	 */
	public function get_current_currency() {
		return $this->config->get( 'currency' );
	}

	/**
	 * Get list position for dropdown.
	 *
	 * @return arrays
	 */
	public function get_positions() {
		$currency = $this->get_currency();

		return array(
			Currency_Interface::POS_LEFT        => sprintf( esc_html__( 'Left (%s99.99)', 'awebooking' ), $currency['symbol'] ),
			Currency_Interface::POS_RIGHT       => sprintf( esc_html__( 'Right (99.99%s)', 'awebooking' ), $currency['symbol'] ),
			Currency_Interface::POS_LEFT_SPACE  => sprintf( esc_html__( 'Left with space (%s 99.99)', 'awebooking' ), $currency['symbol'] ),
			Currency_Interface::POS_RIGHT_SPACE => sprintf( esc_html__( 'Right with space (99.99 %s)', 'awebooking' ), $currency['symbol'] ),
		);
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
				[ '%code', '%name', '%symbol', '%format' ],
				[ $code, $currency['name'], $currency['symbol'], $currency['format'] ],
				$format
			);
		});

		return $currencies;
	}
}
