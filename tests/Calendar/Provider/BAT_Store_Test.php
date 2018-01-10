<?php

use Roomify\Bat\Unit\Unit;
use Roomify\Bat\Event\Event;
use AweBooking\Calendar\Provider\Stores\BAT_Store;

class Calendar_Store_BAT_Store_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	/**
	 * @expectedException LogicException
	 */
	public function testQueryBuilderFaied2() {
		$store = new BAT_Store( 'awebooking_booking', 'room_id' );
		$store->build_the_query( new DateTime( '2017-10-10' ), new DateTime( '2017-10-09' ) );
	}

	/**
	 * @dataProvider getDataQueryBuilder
	 */
	public function testQueryBuilderOK( $expected, $params ) {
		$store = new BAT_Store( 'awebooking_booking', 'room_id' );
		$this->assertEquals($expected, $store->build_the_query( ...$params ) );
	}

	public function getDataQueryBuilder() {
		return [
			[
				"SELECT * FROM `wptests_awebooking_booking` WHERE `year` IN (2017) AND `month` IN (10,11) ORDER BY `room_id`, `year`, `month`",
				[ new DateTime( '2017-10-10' ), new DateTime( '2017-11-11' ) ]
			],
			[
				"SELECT * FROM `wptests_awebooking_booking` WHERE `year` IN (2017) AND `month` IN (10) ORDER BY `room_id`, `year`, `month`",
				[ new DateTime( '2017-10-10' ), new DateTime( '2017-10-10' ) ]
			],
			[
				"SELECT * FROM `wptests_awebooking_booking` WHERE `year` IN (2017) AND `month` IN (10) AND `room_id` IN (1,2,3) ORDER BY `room_id`, `year`, `month`",
				[ new DateTime( '2017-10-10' ), new DateTime( '2017-10-11' ), [1, 2, 3] ]
			],
			[
				"SELECT * FROM `wptests_awebooking_booking` WHERE `year` IN (2017) AND `month` IN (10,11,12) AND `room_id` IN (1,2) OR `year` IN (2018) AND `month` IN (1) AND `room_id` IN (1,2) ORDER BY `room_id`, `year`, `month`",
				[ new DateTime( '2017-10-10' ), new DateTime( '2018-01-11' ), [1, 1, 2] ]
			]
		];
	}

	public function testInstance() {
		$store = new BAT_Store( 'awebooking_booking', 'room_id' );
		$this->assertInstanceOf('Roomify\Bat\Store\Store', $store);
	}

	public function testGetEventData() {
		global $wpdb;
		$wpdb->query("INSERT INTO `{$wpdb->prefix}awebooking_booking` (`room_id`, `year`, `month`, `d25`, `d26`, `d27`, `d28`) VALUES ('11', '2017', '11', '5', '5', '5', '10');");

		$store = new BAT_Store( 'awebooking_booking', 'room_id' );
		$events1 = $store->getEventData( new DateTime( '2017-11-10' ), new DateTime( '2017-11-29' ), [ 100 ] );
		$events2 = $store->getEventData( new DateTime( '2017-11-10' ), new DateTime( '2017-11-29' ), [ 11 ] );

		$this->assertEmpty($events1);

		$this->assertEquals(5, $events2[11]['bat_day'][2017][11]['d25']);
		$this->assertEquals(5, $events2[11]['bat_day'][2017][11]['d26']);
		$this->assertEquals(5, $events2[11]['bat_day'][2017][11]['d27']);
		$this->assertEquals(10, $events2[11]['bat_day'][2017][11]['d28']);
	}

	public function testGetEventManyYears() {
		global $wpdb;
		$wpdb->query("INSERT INTO `{$wpdb->prefix}awebooking_booking` (`room_id`, `year`, `month`, `d25`, `d26`, `d27`, `d28`) VALUES ('11', '2017', '12', '5', '5', '5', '10');");
		$wpdb->query("INSERT INTO `{$wpdb->prefix}awebooking_booking` (`room_id`, `year`, `month`, `d25`, `d26`, `d27`) VALUES ('11', '2018', '1', '10', '10', '10');");

		$store = new BAT_Store( 'awebooking_booking', 'room_id' );
		$events = $store->getEventData( new DateTime( '2017-12-11' ), new DateTime( '2018-01-29' ), [ 11 ] );

		$this->assertEquals(5, $events[11]['bat_day'][2017][12]['d25']);
		$this->assertEquals(5, $events[11]['bat_day'][2017][12]['d26']);
		$this->assertEquals(5, $events[11]['bat_day'][2017][12]['d27']);
		$this->assertEquals(10, $events[11]['bat_day'][2017][12]['d28']);
		$this->assertEquals(10, $events[11]['bat_day'][2018][1]['d25']);
		$this->assertEquals(10, $events[11]['bat_day'][2018][1]['d26']);
		$this->assertEquals(10, $events[11]['bat_day'][2018][1]['d27']);
	}

	public function testStoreEvent() {
		$store = new BAT_Store( 'awebooking_booking', 'room_id' );
		$event1 = new Event( new DateTime( '2017-11-10' ), new DateTime( '2017-11-29' ), new Unit( 101, 0 ), 150 );
		$event2 = new Event( new DateTime( '2017-12-10' ), new DateTime( '2017-12-15' ), new Unit( 101, 0 ), 250 );
		$event3 = new Event( new DateTime( '2018-02-10' ), new DateTime( '2018-02-18' ), new Unit( 101, 0 ), 500 );

		$this->assertTrue($store->storeEvent($event1, 'bat_day'));
		$this->assertTrue($store->storeEvent($event2, 'bat_day'));
		$this->assertTrue($store->storeEvent($event3, 'bat_day'));

		$events1 = $store->getEventData( $event1->getStartDate(), $event1->getEndDate(), [ $event1->getUnitId() ] );
		$events2 = $store->getEventData( $event2->getStartDate(), $event2->getEndDate(), [ $event2->getUnitId() ] );
		$events3 = $store->getEventData( $event3->getStartDate(), $event3->getEndDate(), [ $event3->getUnitId() ] );

		$this->assertEquals(0, $events1[101]['bat_day'][2017][11]['d9']);
		$this->assertEquals(0, $events1[101]['bat_day'][2017][11]['d30']);
		for ( $i = 10; $i <= 29; $i++ ) {
			$this->assertEquals(150, $events1[101]['bat_day'][2017][11]['d'.$i]);
		}

		$this->assertEquals(0, $events2[101]['bat_day'][2017][12]['d9']);
		$this->assertEquals(0, $events2[101]['bat_day'][2017][12]['d16']);
		for ( $i = 10; $i <= 15; $i++ ) {
			$this->assertEquals(250, $events2[101]['bat_day'][2017][12]['d'.$i]);
		}

		$this->assertEquals(0, $events3[101]['bat_day'][2018][2]['d9']);
		$this->assertEquals(0, $events3[101]['bat_day'][2018][2]['d19']);
		for ( $i = 10; $i <= 18; $i++ ) {
			$this->assertEquals(500, $events3[101]['bat_day'][2018][2]['d'.$i]);
		}
	}
}
