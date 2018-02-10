<?php
namespace AweBooking\Reservation\Pricing;

use DateInterval;
use AweBooking\Model\Stay;
use AweBooking\Model\Rate;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Event\Pricing_Event;
use AweBooking\Calendar\Provider\Pricing_Provider;
use AweBooking\Support\Decimal;
use AweBooking\Support\Utils as U;
use Roomify\Bat\Event\EventInterval;
use Illuminate\Support\Arr;

class Pricing {
	/**
	 * The Rate instance.
	 *
	 * @var \AweBooking\Model\Rate
	 */
	protected $rate;

	/**
	 * The Stay instance.
	 *
	 * @var \AweBooking\Model\Stay
	 */
	protected $stay;

	/**
	 * The custom calendar provider.
	 *
	 * @var \AweBooking\Calendar\Provider\Provider_Interface
	 */
	protected $calendar_provider;

	/**
	 * Cache of pricing events.
	 *
	 * @var \AweBooking\Calendar\Event\Event_Collection
	 */
	protected $pricing_events;

	/**
	 * Cache of pricing events itemized.
	 *
	 * @var array
	 */
	protected $events_itemized;

	/**
	 * Constructor.
	 *
	 * @param Rate $rate The Rate instance.
	 * @param Stay $stay The Stay instance.
	 */
	public function __construct( Rate $rate, Stay $stay ) {
		$this->rate = $rate;
		$this->stay = $stay;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_amount() {
		return $this->interval_valuator( 'P1D' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_amount( $amount ) {
		$event = new Pricing_Event(
			$this->get_calendar_resource(), $this->stay->get_check_in(), $this->stay->get_check_out(), $amount
		);

		return $this->get_calendar()->store( $event );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_breakdown() {
		$breakdown = new Breakdown;

		foreach ( $this->stay->to_period() as $night ) {
			$breakdown->put( $night->format( 'Y-m-d' ),
				new Night( $night, $this->get_night_amount( $night ) )
			);
		}

		return $breakdown;
	}

	/**
	 * Get the amount of a night.
	 *
	 * @param  \DateTime|Night $night The night.
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_night_amount( $night ) {
		$itemized = $this->get_events_itemized();

		$amount = Arr::get( $itemized, $night->format( 'Y.n.\dj' ), 0 );

		return Decimal::from_raw_value( $amount );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_rate() {
		return $this->rate;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_rate( Rate $rate ) {
		$this->rate = $rate;

		$this->flush();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_stay() {
		return $this->stay;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_stay( Stay $stay ) {
		$this->stay = $stay;

		$this->flush();
	}

	/**
	 * Get the calendar provider.
	 *
	 * @param \AweBooking\Calendar\Provider\Pricing_Provider $provider The provider.
	 */
	public function use_calendar_provider( Pricing_Provider $provider ) {
		$this->calendar_provider = $provider;

		$this->flush();
	}

	/**
	 * Flush the cache events data,
	 *
	 * @return void
	 */
	public function flush() {
		$this->pricing_events = null;
		$this->events_itemized = null;
	}

	/**
	 * Get the pricing events as itemized from the Provider.
	 *
	 * @return \AweBooking\Calendar\Event\Event_Collection
	 */
	public function get_events_itemized() {
		if ( ! $this->events_itemized ) {
			$index = $this->rate->get_id();

			$itemized = $this->get_calendar_provider()->get_events_itemized(
				$this->stay->get_check_in(),
				$this->stay->get_check_out()
			);

			$this->events_itemized = array_key_exists( $index, $itemized )
				? $itemized[ $index ]
				: [];
		}

		return $this->events_itemized;
	}

	/**
	 * Get the pricing events from the Calendar.
	 *
	 * @return \AweBooking\Calendar\Event\Event_Collection
	 */
	public function get_pricing_events() {
		if ( ! $this->pricing_events ) {
			$this->pricing_events = $this->get_calendar()
				->get_events( $this->stay->to_period() );
		}

		return $this->pricing_events;
	}

	/**
	 * Get the Calendar.
	 *
	 * @return \AweBooking\Calendar\Calendar
	 */
	protected function get_calendar() {
		return new Calendar( $this->get_calendar_resource(), $this->get_calendar_provider() );
	}

	/**
	 * Get the calendar resource.
	 *
	 * @return \AweBooking\Calendar\Resource\Resource
	 */
	protected function get_calendar_resource() {
		$amount = Decimal::create( $this->rate->get_base_amount() );

		$resource = new Resource( $this->rate->get_id(), $amount->as_raw_value() );
		$resource->set_title( $this->rate->get_name() );

		return $resource;
	}

	/**
	 * Get the calendar provider.
	 *
	 * @return \AweBooking\Calendar\Provider\Pricing_Provider
	 */
	protected function get_calendar_provider() {
		return $this->calendar_provider ?: new Pricing_Provider( $this->get_calendar_resource() );
	}

	/**
	 * Calculate sum of events value based on a duration.
	 *
	 * @see \Roomify\Bat\Valuator\IntervalValuator
	 *
	 * @param  string $duration The DateInterval duration.
	 * @return \AweBooking\Support\Decimal
	 */
	protected function interval_valuator( $duration = 'P1D' ) {
		return $this->get_pricing_events()->reduce( function( $total, $e ) use ( $duration ) {
			$percentage = EventInterval::divide(
				$e->get_start_date(), $e->get_end_date(), new DateInterval( $duration )
			);

			return $total->add( $e->get_amount()->mul( $percentage ) );
		}, Decimal::zero() );
	}
}
