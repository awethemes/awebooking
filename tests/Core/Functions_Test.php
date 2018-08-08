<?php

class Core_Functions_Test extends WP_UnitTestCase {
	/**
	 * @dataProvider get_recursive_sanitizer_data
	 */
	public function test_recursive_sanitizer( $input, $output ) {
		$this->assertEquals( $output, abrs_recursive_sanitizer( $input, '_test_sanitize' ) );
	}

	public function get_recursive_sanitizer_data() {
		return [
			[
				['a' => ['1', '2'], 'b' => '2'],
				['a' => ['11', '12'], 'b' => '12'],
			]
		];
	}

	/**
	 * @dataProvider get_sanitize_decimal_data
	 */
	function test_sanitize_decimal($input, $output) {
		$this->assertEquals( ',', abrs_get_option( 'price_decimal_separator' ) );
		$this->assertSame( $output, abrs_sanitize_decimal( $input ) );
	}

	public function get_sanitize_decimal_data() {
		return [
			[0, '0'],
			[1, '1'],
			[1.0, '1'],
			['$100', '100'],
			['100,1', '100.1'],
			['-$100.1', '-100.1'],
			['1000', '1000'],
			['1000.00', '1000'],
		];
	}

	/**
	 * @dataProvider get_sanitize_decimal_data
	 */
	public function  test_sanitize_amount( $input, $output ) {
		$this->assertSame( $output, abrs_sanitize_amount( $input ) );
	}

	public function get_sanitize_amount_data() {
		return [
			[0, '0'],
			['0%', '0%'],
			[13.55, '13.55'],
			['13.55', '13.55'],
			['13.55%', '13.55%'],
		];
	}
}

function _test_sanitize( $input ) {
	return '1' . $input;
}
