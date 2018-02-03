<?php

use AweBooking\Model\Tax;

class Model_Tax_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function testInsert() {
		$tax = new Tax;

		$tax['name'] = 'The VAT tax';
		$tax['type'] = 'tax';
		$tax['code'] = 'VAT';
		$tax['category'] = 'exclusive';
		$tax['amount_type'] = 'percentage';
		$tax['amount'] = 10;

		$saved = $tax->save();
		$this->assertTrue( $saved );
		$this->assertEquals('VAT', $tax->get_code());
		$this->assertEquals('tax', $tax->get_type());
		$this->assertEquals(10, $tax->get_amount());
	}

	public function testUpdate() {
		$t1 = $this->setupTax();

		$tax = new Tax( $t1 );
		$this->assertTrue( $tax->exists() );

		$tax['name'] = 'GTGT-Update';
		$tax['type'] = 'tax';
		$tax['code'] = 'VAT';
		$tax['category'] = 'inclusive';
		$tax['amount_type'] = 'percentage';
		$tax['amount'] = 10;
		$tax->save();

		// Assert both data correct.
		$dbroom = $this->getItemInDB( $tax->get_id() );
		foreach ( $tax->only( 'id', 'name', 'type', 'code', 'category', 'amount_type', 'amount' ) as $key => $value ) {
			$this->assertEquals( $dbroom[ $key ], $value );
		}
	}

	public function testDelete() {
		$t1 = $this->setupTax();

		$tax = new Tax( $t1 );
		$this->assertTrue( $tax->exists() );

		$tax->delete();

		$this->assertFalse( $tax->exists() );
		$this->assertNull( $this->getItemInDB( $t1 ) );
	}

	protected function setupTax() {
		global $wpdb;

		$wpdb->insert( $wpdb->prefix . 'awebooking_tax_rates', [
			'name' => 'GTGT',
			'type' => 'tax',
			'code' => 'VAT',
			'category' => 'exclusive',
			'amount_type' => 'percentage',
			'amount' => 10,
		]);

		return $wpdb->insert_id;
	}

	protected function getItemInDB( $id ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}awebooking_tax_rates` WHERE `id` = '%d' LIMIT 1", $id ),
			ARRAY_A
		);
	}
}
