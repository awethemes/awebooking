<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\Model\Stay;
use AweBooking\Model\Base_Rate_Item;
use AweBooking\Reservation\Pricing\Rate_Pricing;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Collection;

use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Period\Period;
use AweBooking\Calendar\Provider\Cached_Provider;
use AweBooking\Calendar\Provider\Pricing_Provider;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resource_Collection;

class Rate_Calendar extends Schedule_Calendar {
	/**
	 * List of room-types to display.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $room_types;

	/**
	 * Cache all resources.
	 *
	 * @var \AweBooking\Calendar\Resource\Resource_Collection
	 */
	protected $all_resources;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// ...
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		$room_types = $this->get_room_types();

		$scheduler = $this->create_scheduler();

		$cal = $this;

		if (isset($_REQUEST['date'])) {
			$date = Carbonate::create_date( $_REQUEST['date']  );
		} else {
			$date = Carbonate::today();
		}

		$period = new Period(
			$date->copy()->subDays( 2 ),
			$date->copy()->addDays( 120 )
		);

		$list_pricing = [];
		foreach ( $scheduler as $calendar ) {
			$list_pricing[ $calendar->get_uid() ] = $this->get_the_pricing( $calendar, $period );
		}

		// Enqueue the schedule-calendar.
		wp_enqueue_script( 'awebooking-schedule-calendar' );

		awebooking( 'admin_template' )->partial( 'rates/scheduler-layout.php',
			compact( 'cal', 'scheduler', 'period', 'list_pricing', 'date' )
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_the_pricing( Calendar $calendar, Period $period ) {
		$stay = new Stay( $period->get_start_date(), $period->get_end_date() );

		$pricing = new Rate_Pricing(
			$calendar->get_resource()->get_reference(), $stay
		);

		return [
			$pricing->get_rate()->get_amount(),
			$pricing->get_breakdown(),
		];
	}

	/**
	 * Get the scheduler.
	 *
	 * @return \AweBooking\Calendar\Scheduler
	 */
	protected function create_scheduler() {
		$resources = $this->create_resources();

		$provider = new Cached_Provider( new Pricing_Provider( $resources ) );

		$calendars = Collection::make( $resources )->map(function( $resource ) use ( $provider ) {
			$calendar = new Calendar( $resource, $provider );

			$calendar->set_name( $resource->get_title() );

			return $calendar;
		});

		return Scheduler::make( $calendars );
	}

	/**
	 * Create the calendar resources.
	 *
	 * @return \AweBooking\Calendar\Resource\Resource_Collection
	 */
	protected function create_resources() {
		return Resource_Collection::make(
			$this->get_room_types()->map(function( $room_type ) {
				$rate = new Base_Rate_Item( $room_type );
				$resource = new Resource( $rate->get_id(), $rate->get_amount()->as_raw_value() );

				$resource->set_title( $room_type->get_title() );
				$resource->set_reference( $rate );

				return $resource;
			})
		);
	}

	/**
	 * Get the list room_types.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	protected function get_room_types() {
		if ( is_null( $this->room_types ) ) {
			$this->room_types = $this->fetch_room_types();
		}

		return $this->room_types;
	}
}
