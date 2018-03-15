<?php

use AweBooking\Support\Carbonate;
use AweBooking\Calendar\Event\Event;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resources;
use AweBooking\Calendar\Provider\WP_Provider;
use AweBooking\Calendar\Provider\Provider_Interface;
use AweBooking\Calendar\Provider\Contracts\Storable;
use Roomify\Bat\Unit\Unit as BAT_Unit;
use Roomify\Bat\Event\Event as BAT_Event;

class Calendar_Store_WP_Store_Test extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function testInstance() {
		$resource = new Resource( 100 );
		$provider = new WP_Provider( [ $resource ], 'awebooking_booking', 'room_id' );

		$this->assertInstanceOf( Provider_Interface::class, $provider);
		$this->assertInstanceOf( Storable::class, $provider);
		$this->assertInstanceOf( Resources::class, $provider->get_resources());
		$this->assertInstanceOf( \AweBooking\Calendar\Provider\Stores\BAT_Store::class, $provider->get_store());
	}

	public function  testAddMoreResources() {
		$resource = new Resource( 100 );
		$resource2 = new Resource( 1001 );

		$provider = new WP_Provider( [ $resource ], 'awebooking_booking', 'room_id' );
		$this->assertCount( 1, $provider->get_resources() );

		$provider->add( $resource2 );
		$this->assertCount(2, $provider->get_resources() );
		$this->assertSame( $resource2, $provider->get_resources()->get( 1) );
	}

	public function testGetEvents() {
		$resource  = new Resource( 100 );
		$provider  = new WP_Provider( [ $resource ], 'awebooking_booking', 'room_id' );

		$store = $provider->get_store();
		$event1 = new BAT_Event( new DateTime( '2017-11-10' ), new DateTime( '2017-11-29' ), new BAT_Unit( 100, 0 ), 150 );
		$event2 = new BAT_Event( new DateTime( '2017-12-10' ), new DateTime( '2017-12-15' ), new BAT_Unit( 101, 0 ), 250 );
		$this->assertTrue($store->storeEvent($event1, 'bat_day'));
		$this->assertTrue($store->storeEvent($event2, 'bat_day'));

		$events = $provider->get_events(new Carbonate( '2017-11-10' ), new Carbonate( '2017-12-15' ));
		$this->assertInstanceOf( 'AweBooking\Calendar\Event\Event', $events[0] );

		$this->assertCount( 2, $events );
		$this->assertSame( '2017-11-10 00:00:00 ~ 2017-11-29 23:59:00', $this->format_event_date($events[0]) );
		$this->assertSame( '2017-11-30 00:00:00 ~ 2017-12-14 23:59:00', $this->format_event_date($events[1]) );

		// TODO: ...
		$provider->add( $resource2 = new Resource( 101 ) );
		$events2 = $provider->get_events(new Carbonate( '2017-11-10' ), new Carbonate( '2017-12-30' ));
		$this->assertCount( 5, $events2 );
	}

	protected function format_event_date($e) {
		return $e->get_start_date()->toDateTimeString() . ' ~ ' . $e->get_end_date()->toDateTimeString();
	}

	public function testGetEmptyResource() {
		$provider = new WP_Provider( [], 'awebooking_booking', 'room_id' );
		$this->assertCount( 0, $provider->get_resources() );

		$events = $provider->get_events(new Carbonate( '2017-11-10' ), new Carbonate( '2017-12-30' ));
		$this->assertEmpty($events);
	}

	public function testStoreEvent() {
		$resource  = new Resource( 100 );
		$resource2 = new Resource( 101 );
		$provider  = new WP_Provider( [ $resource, $resource2 ], 'awebooking_booking', 'room_id' );

		$event1 = new Event( $resource, new DateTime( '2017-11-10' ), new DateTime( '2017-11-29' ), 150 );
		$event2 = new Event( $resource2, new DateTime( '2017-12-10' ), new DateTime( '2017-12-15' ), 250 );

		$this->assertTrue( $provider->store_event( $event1 ) );
		$this->assertTrue( $provider->store_event( $event2 ) );

		$events = $provider->get_events(new Carbonate( '2017-11-01' ), new Carbonate( '2017-12-30' ));
		$this->assertCount(6, $events );
	}

	/**
	 * @expectedException AweBooking\Calendar\Provider\Exceptions\Untrusted_Resource_Exception
	 */
	public function testStoreUntrustedEvent1() {
		$resource  = new Resource( 0 );
		$provider  = new WP_Provider( [ $resource ], 'awebooking_booking', 'room_id' );

		$event1 = new Event( $resource, new DateTime( '2017-11-10' ), new DateTime( '2017-11-29' ), 150 );
		$provider->store_event( $event1 );
	}

	/**
	 * @expectedException AweBooking\Calendar\Provider\Exceptions\Untrusted_Resource_Exception
	 */
	public function testStoreUntrustedEvent2() {
		$resource  = new Resource( -10 );
		$provider  = new WP_Provider( [ $resource ], 'awebooking_booking', 'room_id' );

		$event1 = new Event( $resource, new DateTime( '2017-11-10' ), new DateTime( '2017-11-29' ), 150 );
		$provider->store_event( $event1 );
	}
}
