<?php

class Helpers_Test extends WP_UnitTestCase {
	public function test_sort_by_array() {
		$arr   = [ 'one', 'two', 'three', 'four', 'five' ];
		$sort  = [ 'two', 'one', 'three', 'five', 'four' ];
		$sort2 = [ 'two', 'three' ];
		$sort3 = [ 'two', 'five', 'one' ];

		$sorted = abrs_sort_by_keys( $arr, $sort );
		$this->assertEquals( $sorted, $sort );

		$sorted2 = abrs_sort_by_keys( $arr, $sort2 );
		$this->assertEquals( [ 'two', 'three', 'one', 'four', 'five' ], $sorted2 );

		$sorted3 = abrs_sort_by_keys( $arr, $sort3 );
		$this->assertEquals( [ 'two', 'five', 'one', 'three', 'four' ], $sorted3 );
	}

	public function test_sort_by_assoc_array() {
		$arr  = [ 'one' => '1', 'two' => '2', 'three' => '3', 'four' => '4', 'five' => '5' ];
		$sort = [ 'two', 'five', 'one' ];

		$sorted = abrs_sort_by_keys( $arr, $sort );
		// dump( $sorted );
	}
}
