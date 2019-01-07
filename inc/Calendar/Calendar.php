<?php

namespace AweBooking\Calendar;

use AweBooking\Support\Period;
use AweBooking\Calendar\Event\Events;
use AweBooking\Calendar\Event\Itemizer;
use AweBooking\Calendar\Event\Event_Interface;
use AweBooking\Calendar\Resource\Resource_Interface;
use AweBooking\Calendar\Provider\Provider_Interface;
use AweBooking\Calendar\Provider\Contracts\Storable;

class Calendar {
	/**
	 * The Calendar resource.
	 *
	 * @var \AweBooking\Calendar\Resource\Resource_Interface
	 */
	protected $resource;

	/**
	 * The provider instance.
	 *
	 * @var \AweBooking\Calendar\Provider\Provider_Interface
	 */
	protected $provider;

	/**
	 * The scheduler ID.
	 *
	 * @var string
	 */
	protected $uid;

	/**
	 * Name of the calendar.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Description of the calendar.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Hex color to use as background color for events in this calendar.
	 *
	 * @var string
	 */
	protected $background_color;

	/**
	 * Hex color to use as foreground color for events in this calendar.
	 *
	 * @var string
	 */
	protected $foreground_color;

	/**
	 * Create a Calendar.
	 *
	 * @param Resource_Interface $resource The resource implementation.
	 * @param Provider_Interface $provider The calendar provider implementation.
	 */
	public function __construct( Resource_Interface $resource, Provider_Interface $provider ) {
		$this->provider = $provider;
		$this->resource = $resource;
	}

	/**
	 * Store an event into the Calendar.
	 *
	 * @param  Event_Interface $event The event implementation.
	 * @return bool
	 *
	 * @throws Exceptions\StoreNotSupportedException
	 */
	public function store( Event_Interface $event ) {
		if ( ! $this->provider instanceof Storable ) {
			throw new Exceptions\StoreNotSupportedException( 'The provider `' . get_class( $this->provider ) . '` not support store event.' );
		}

		return $this->provider->store_event( $event );
	}

	/**
	 * Get events available in a period.
	 *
	 * @param  Period $period  The period.
	 * @param  array  $options Optional, something pass to provider to get events.
	 * @return \AweBooking\Calendar\Event\Events
	 */
	public function get_events( Period $period, array $options = [] ) {
		return Events::make( $this->get_provider_events( $period, $options ) )
			->filter(function( Event_Interface $e ) {
				return $this->get_resource()->get_id() == $e->get_resource()->get_id();
			})
			->each(function( Event_Interface $e ) {
				if ( $e->is_untrusted_resource() ) {
					$e->set_resource( $this->get_resource() );
				}
			})->values();
	}

	/**
	 * Get itemized in a period.
	 *
	 * @param  Period $period  The period.
	 * @param  array  $options Optional, something pass to provider to get events.
	 * @return \AweBooking\Calendar\Event\Itemized
	 */
	public function get_itemized( Period $period, array $options = [] ) {
		$events = $this->get_events( $period, $options );

		return ( new Itemizer( $events ) )->itemize();
	}

	/**
	 * Get events available in a period from provider.
	 *
	 * @param  Period $period  The period.
	 * @param  array  $options Optional, something pass to provider to get events.
	 * @return array
	 */
	protected function get_provider_events( Period $period, array $options = [] ) {
		return $this->provider->get_events(
			$period->get_start_date(), $period->get_end_date(), $options
		);
	}

	/**
	 * The resource of the Calendar.
	 *
	 * @return \AweBooking\Calendar\Resource\Resource_Interface
	 */
	public function get_resource() {
		return $this->resource;
	}

	/**
	 * The provider of the Calendar.
	 *
	 * @return \AweBooking\Calendar\Provider\Provider_Interface
	 */
	public function get_provider() {
		return $this->provider;
	}

	/**
	 * Get unique ID for this calendar.
	 *
	 * @return string
	 */
	public function get_uid() {
		return $this->uid ?: $this->resource->get_id();
	}

	/**
	 * Set the calendar UID.
	 *
	 * @param  int $uid The calendar UID.
	 * @return $this
	 */
	public function set_uid( $uid ) {
		$this->uid = $uid;

		return $this;
	}

	/**
	 * Get the Calendar name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set the Calendar name.
	 *
	 * @param  string $name The Calendar name.
	 * @return $this
	 */
	public function set_name( $name ) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get the Calendar description.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set the Calendar description.
	 *
	 * @param  string $description The Calendar description.
	 * @return $this
	 */
	public function set_description( $description ) {
		$this->description = $description;

		return $this;
	}

	/**
	 * Get the background color of the Calendar.
	 *
	 * @return string
	 */
	public function get_background_color() {
		return $this->background_color;
	}

	/**
	 * Set the background color of the Calendar.
	 *
	 * @param  string $background_color Valid hex color.
	 * @return $this
	 */
	public function set_background_color( $background_color ) {
		$this->background_color = sanitize_hex_color( $background_color );

		return $this;
	}

	/**
	 * Get the foreground color of the Calendar.
	 *
	 * @return string
	 */
	public function get_foreground_color() {
		return $this->foreground_color;
	}

	/**
	 * Set the foreground color of the Calendar.
	 *
	 * @param  string $foreground_color Valid hex color.
	 * @return $this
	 */
	public function set_foreground_color( $foreground_color ) {
		$this->foreground_color = sanitize_hex_color( $foreground_color );

		return $this;
	}
}
