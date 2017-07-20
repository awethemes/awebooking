<?php
/**
 * Class RoomTest
 *
 * @package AweBooking
 */

use AweBooking\Config;
use AweBooking\Currency;

/**
 * Room test case.
 */
class CurrencyTest extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	function test_working() {
		$currency = new Currency( awebooking('config') );

		$this->assertEquals('USD', $currency->current_currency());
		$this->assertArrayHasKey('USD', $currency->get_currencies());
		$this->assertArrayHasKey('VND', $currency->get_currencies());

		$this->assertArrayHasKey('name', $currency->get_currency('USD'));
		$this->assertArrayHasKey('symbol', $currency->get_currency('USD'));

		$this->assertEquals($currency->get_currency('USD')['name'], 'US Dollar');
		$this->assertEquals($currency->get_currency('USD')['symbol'], '$');
	}

	function test_for_dropdown() {
		$currency = new Currency( awebooking('config') );

		$currencies = $currency->get_for_dropdown();
		$this->assertEquals($currencies['USD'], 'US Dollar');

		$currencies = $currency->get_for_dropdown('%code');
		$this->assertEquals($currencies['USD'], 'USD');

		$currencies = $currency->get_for_dropdown('%name');
		$this->assertEquals($currencies['USD'], 'US Dollar');

		$currencies = $currency->get_for_dropdown('%symbol');
		$this->assertEquals($currencies['USD'], '$');

		$currencies = $currency->get_for_dropdown('%format');
		$this->assertEquals($currencies['USD'], '$1,0.00');
	}
}
