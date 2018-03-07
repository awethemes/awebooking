<?php

use AweBooking\Support\Fluent;
use AweBooking\Support\Fluent_Container;

class Test_Fluent_Container_Class extends Fluent {
	use Fluent_Container;
}

class Support_Fluent_Container_Test extends WP_UnitTestCase {
	public function testBasicSetAndGet() {
		$context = new Test_Fluent_Container_Class;

		$context['name'] = 'Van Anh';
		$context['real_name'] = function( $a ) {
			return 'Nguyen ' . $a['name'];
		};

		$allkeys = $context->get_attributes();

		$this->assertInstanceOf( 'AweBooking\Support\Fluent', $context );
		$this->assertEquals( $context->name, 'Van Anh' );
		$this->assertEquals( $allkeys['name'], 'Van Anh' );

		$this->assertEquals( $context->real_name, 'Nguyen Van Anh' );
		$this->assertEquals( $allkeys['real_name'], 'Nguyen Van Anh' );

		$this->assertEquals( $context->toArray(), $allkeys );
		$this->assertEquals( $context->toJson(), json_encode( $allkeys ) );
	}

	public function testSameInstance() {
		$context = new Test_Fluent_Container_Class;

		$context['aaa'] = function() {
			return new stdClass;
		};

		$this->assertSame( $context['aaa'], $context->get( 'aaa' ) );
	}
}
