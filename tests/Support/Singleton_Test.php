<?php

use AweBooking\Support\Traits\Singleton;

class Support_Singleton_Trait_Test extends WP_UnitTestCase {
	public function testCorrectClassInstance() {
		$instanceClass2 = Test_Singleton_Trait2::get_instance();
		$this->assertInstanceOf( 'Test_Singleton_Trait2', $instanceClass2 );

		$instanceClass1 = Test_Singleton_Trait1::get_instance();
		$this->assertInstanceOf( 'Test_Singleton_Trait1', $instanceClass1 );
	}

	public function testGetInstance() {
		$instance = Test_Singleton_Trait1::get_instance();

		$this->assertInstanceOf( 'Test_Singleton_Trait1', $instance );
		$this->assertSame( $instance, Test_Singleton_Trait1::get_instance() );
		$this->assertSame( Test_Singleton_Trait1::get_instance(), Test_Singleton_Trait1::get_instance() );
	}
}

class Test_Singleton_Trait1 {
	use Singleton;

	private function __construct() {}
}

class Test_Singleton_Trait2 {
	use Singleton;

	private function __construct() {}
}
