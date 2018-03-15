<?php

use AweBooking\Resources\Currencies;
use AweBooking\Model\Common\Currency;

class CurrencyTest extends WP_UnitTestCase {
	public function testSingletonInstance() {
		$currencies = Currencies::get_instance();

		$this->assertSame($currencies, Currencies::get_instance());
		$this->assertSame(Currencies::get_instance(), Currencies::get_instance());

		$this->assertInstanceOf('AweBooking\Resources\Repository', $currencies->get_repository());
	}

	public function testGetCurrency() {
		$currencies = Currencies::get_instance();

		$currency = $currencies->get( 'USD' );
		$this->assertInstanceOf( Currency::class, $currency);

		$this->assertEquals($currency->get_code(), 'USD');
		$this->assertEquals($currency->get_name(), 'US Dollar');
		$this->assertEquals($currency->get_symbol(), '$');
	}

	public function testGerForDropdown() {
		$currencies = Currencies::get_instance();

		$currency = $currencies->get_for_dropdown();
		$this->assertEquals($currency['USD'], 'US Dollar');

		$currency = $currencies->get_for_dropdown('%code');
		$this->assertEquals($currency['USD'], 'USD');

		$currency = $currencies->get_for_dropdown('%name');
		$this->assertEquals($currency['USD'], 'US Dollar');

		$currency = $currencies->get_for_dropdown('%symbol');
		$this->assertEquals($currency['USD'], '$');
	}
}
