<?php

class Tax_Test extends WP_UnitTestCase {
	public function testApi() {
		$tax_id = abrs_insert_tax_rate( ['name' => 'VAT', 'rate' => 10 ] );

		$this->assertInternalType( 'integer', $tax_id );
		$this->assertTrue( $tax_id > 0 );

		$tax = abrs_get_tax_rate( $tax_id );
		$this->assertEquals( '10', $tax['rate'] );
		$this->assertEquals( 'VAT', $tax['name'] );
		$this->assertEquals( '0', $tax['priority'] );
		$this->assertEquals( '0', $tax['compound'] );

		abrs_delete_tax_rate( $tax_id );
		$this->assertNull( abrs_get_tax_rate( $tax_id ) );
	}
}
