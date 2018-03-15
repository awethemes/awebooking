<?php
namespace AweBooking\Reservation\Pricing;

use DateInterval;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Pricing\Rate;
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
	 * @var \AweBooking\Model\Pricing\Rate
	 */
	protected $rate;

	/**
	 * The Timespan instance.
	 *
	 * @var \AweBooking\Model\Common\Timespan
	 */
	protected $timespan;

	/**
	 * The custom calendar provider.
	 *
	 * @var \AweBooking\Calendar\Provider\Provider_Interface
	 */
	protected $calendar_provider;

	/**
	 * Cache of pricing events.
	 *
	 * @var \AweBooking\Calendar\Event\Events
	 */
	protected $pricing_events;

	/**
	 * Cache of pricing events itemized.
	 *
	 * @var array
	 */
	protected $events_itemized;

	/**
	 * Cache the amount.
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $cache_amount;

	/**
	 * Constructor.
	 *
	 * @param Rate     $rate     The Rate instance.
	 * @param Timespan $timespan The Timespan instance.
	 */
	public function __construct( Rate $rate, Timespan $timespan ) {
		$this->rate = $rate;
		$this->timespan = $timespan;
	}

	/**
	 * Get the stay.
	 *
	 * @return \AweBooking\Model\Common\Timespan
	 */
	public function get_timespan() {
		return $this->timespan;
	}

	/**
	 * Set the stay.
	 *
	 * @param  \AweBooking\Model\Common\Timespan $timespan The stay.
	 * @return void
	 */
	public function set_timespan( Timespan $timespan ) {
		$this->timespan = $timespan;

		$this->flush();
	}

	/**
	 * Get the total amount.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_amount() {
		if ( is_null( $this->cache_amount ) ) {
			$this->cache_amount = $this->interval_valuator( 'P1D' );
		}

		return $this->cache_amount;
	}

	/**
	 * Set the amount.
	 *
	 * @param  float|int $amount The amount.
	 * @return true
	 */
	public function set_amount( $amount ) {
		$event = new Pricing_Event(
			$this->get_calendar_resource(), $this->timespan->get_start_date(), $this->timespan->get_end_date(), $amount
		);

		$stored = $this->get_calendar()->store( $event );

		$this->flush();

		return $stored;
	}

	/**
	 * Get the breakdown.
	 *
	 * @return \AweBooking\Reservation\Pricing\Breakdown
	 */
	public function get_breakdown() {
		$breakdown = new Breakdown;

		foreach ( $this->timespan->to_period() as $night ) {
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
	 * @return \AweBooking\Calendar\Event\Events
	 */
	public function get_events_itemized() {
		if ( ! $this->events_itemized ) {
			$index = $this->rate->get_id();

			$itemized = $this->get_calendar_provider()->get_events_itemized(
				$this->timespan->get_start_date(),
				$this->timespan->get_end_date()
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
	 * @return \AweBooking\Calendar\Event\Events
	 */
	public function get_pricing_events() {
		if ( ! $this->pricing_events ) {
			$this->pricing_events = $this->get_calendar()
				->get_events( $this->timespan->to_period() );
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
		$amount = Decimal::create( $this->rate->get_amount() );

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
