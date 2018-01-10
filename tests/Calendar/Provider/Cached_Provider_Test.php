<?php

use AweBooking\Support\Carbonate;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Provider\WP_Provider;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Calendar\Provider\Provider_Interface;

class Cached_Provider_Test extends WP_UnitTestCase {
	public function test_constructor() {
		$mock_provider = Mockery::mock(Provider_Interface::class);
		$cache_provider = new Cached_Provider( $mock_provider );

		$this->assertInstanceOf(Provider_Interface::class, $cache_provider);
		$this->assertInstanceOf('AweBooking\Calendar\Provider\Contracts\Storable', $cache_provider);
	}

	public function test_get_events() {
		$wp_provider = new WP_Provider( [ new Resource( 100 ) ], 'awebooking_booking', 'room_id' );
		$cache_provider = new Cached_Provider( $wp_provider );

		$events1 = $cache_provider->get_events(new Carbonate( '2017-11-01' ), new Carbonate( '2017-11-30' ));
		$events2 = $cache_provider->get_events(new Carbonate( '2017-11-01' ), new Carbonate( '2017-11-30' ));

		$events3 = $cache_provider->get_events(new Carbonate( '2017-12-01' ), new Carbonate( '2017-12-30' ));
		$events4 = $cache_provider->get_events(new Carbonate( '2017-12-01' ), new Carbonate( '2017-12-30' ));

		$this->assertSame($events1, $events2);
		$this->assertSame($events3, $events4);
		$this->assertNotSame($events2, $events3);
		$this->assertNotSame($events1, $events4);

		$cache_provider->flush();
		$events12 = $cache_provider->get_events(new Carbonate( '2017-11-01' ), new Carbonate( '2017-11-30' ));
		$events34 = $cache_provider->get_events(new Carbonate( '2017-12-01' ), new Carbonate( '2017-12-30' ));

		$this->assertNotSame($events12, $events34);
		$this->assertNotSame($events1, $events12);
		$this->assertNotSame($events2, $events12);
		$this->assertNotSame($events3, $events34);
		$this->assertNotSame($events3, $events34);
	}
}
