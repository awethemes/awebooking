<?php

namespace AweBooking\Calendar\Provider;

use Roomify\Bat\Unit\Unit as BATUnit;
use Roomify\Bat\Event\Event as BATEvent;
use Roomify\Bat\Calendar\Calendar as BATCalendar;
use AweBooking\Support\Carbonate;
use AweBooking\Calendar\Event\Event;
use AweBooking\Calendar\Event\Event_Interface;
use AweBooking\Calendar\Provider\Stores\BATStore;
use AweBooking\Calendar\Resource\Resources;
use AweBooking\Calendar\Resource\Resource_Interface;
use AweBooking\Calendar\Exceptions\UntrustedResourceException;

class DB_Provider implements Provider_Interface, Contracts\Storable {
	/**
	 * The collect of resource.
	 *
	 * @var \AweBooking\Calendar\Resource\Resources
	 */
	protected $resources;

	/**
	 * The BAT Store instance.
	 *
	 * @var \AweBooking\Calendar\Provider\Stores\BATStore
	 */
	protected $store;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Calendar\Resource\Resources|array $resources   The resources to get events.
	 * @param string                                        $table       The table name.
	 * @param string                                        $foreign_key The foreign key.
	 */
	public function __construct( $resources, $table, $foreign_key ) {
		$this->store     = new BATStore( $table, $foreign_key );
		$this->resources = new Resources( $resources instanceof Resource_Interface ? [ $resources ] : $resources );
	}

	/**
	 * Add one more resource to fetching.
	 *
	 * @param  Resource_Interface $resource The resource implementation.
	 *
	 * @return $this
	 */
	public function add( Resource_Interface $resource ) {
		$this->resources->push( $resource );

		return $this;
	}

	/**
	 * Get the BAT Store instance.
	 *
	 * @return \AweBooking\Calendar\Provider\Stores\BATStore
	 */
	public function get_store() {
		return $this->store;
	}

	/**
	 * Get the resources collection.
	 *
	 * @return \AweBooking\Calendar\Resource\Resources
	 */
	public function get_resources() {
		return $this->resources;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_events( Carbonate $start_date, Carbonate $end_date, array $options = [] ) {
		$units = $this->transform_resources_to_units();

		// If empty units, leave and return an empty array.
		if ( empty( $units ) ) {
			return [];
		}

		$original_timezone = date_default_timezone_get();
		date_default_timezone_set( abrs_get_wp_timezone() );

		$raw_events = abrs_rescue( function () use ( $units, $start_date, $end_date ) {
			return $this->get_calendar( $units )->getEvents( $start_date, $end_date, true );
		}, [] );

		$events = abrs_collect( $raw_events )
			->flatten( 1 )
			->map( function ( $raw_event ) {
				/* @var \Roomify\Bat\Event\Event $raw_event */
				$resource = $this->resources->first( function ( $r ) use ( $raw_event ) {
					/* @var \AweBooking\Calendar\Resource\Resource_Interface $r */
					return $r->get_id() === $raw_event->getUnitId();
				} );

				return $this->transform_calendar_event( $raw_event, $resource );
			} )->all();

		date_default_timezone_set( $original_timezone );

		return $events;
	}

	/**
	 * {@inheritdoc}
	 */
	public function store_event( Event_Interface $event ) {
		if ( $event->is_untrusted_resource() ) {
			throw new UntrustedResourceException( 'Cannot store an event have untrusted source' );
		}

		$original_timezone = date_default_timezone_get();
		date_default_timezone_set( abrs_get_wp_timezone() );

		// Transform resource to BATUnit.
		$resource  = $event->get_resource();
		$mockunit  = new BATUnit( $resource->get_id(), $resource->get_value() );
		$mockevent = new BATEvent( $event->get_start_date(), $event->get_end_date(), $mockunit, $event->get_value() );

		$only_days = null;
		if ( method_exists( $event, 'get_only_days' ) ) {
			$only_days = $event->get_only_days();
		}

		$stored = abrs_rescue( function () use ( $mockevent, $only_days ) {
			return $this->get_store()->storeEvent( $mockevent, $only_days );
		}, false );

		date_default_timezone_set( $original_timezone );

		return $stored;
	}

	/**
	 * Create the BAT calendar and return it.
	 *
	 * @param  array $units The array of BAT units.
	 *
	 * @return \Roomify\Bat\Calendar\Calendar
	 */
	protected function get_calendar( array $units ) {
		return new BATCalendar( $units, $this->store, 0 );
	}

	/**
	 * Transform the BATEvent to the AweBooking Calendar Event.
	 *
	 * @param  BATEvent           $raw_event The BAT event.
	 * @param  Resource_Interface $resource  The mapping resource.
	 *
	 * @return \AweBooking\Calendar\Event\Event_Interface
	 */
	protected function transform_calendar_event( BATEvent $raw_event, Resource_Interface $resource ) {
		return new Event( $resource, $raw_event->getStartDate(), $raw_event->getEndDate(), $raw_event->getValue() );
	}

	/**
	 * Transform resources to BAT units.
	 *
	 * @return array
	 */
	protected function transform_resources_to_units() {
		return abrs_collect( $this->resources )
			->map( function ( Resource_Interface $r ) {
				return new BATUnit( $r->get_id(), $r->get_value() );
			} )->unique( function ( BATUnit $u ) {
				return $u->getUnitId();
			} )->all();
	}
}
