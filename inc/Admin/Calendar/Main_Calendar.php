<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Support\Utils as U;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Collection;

use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Period\Period;
use AweBooking\Calendar\Event\State_Event;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resources;
use AweBooking\Calendar\Provider\State_Provider;
use AweBooking\Calendar\Provider\Booking_Provider;
use AweBooking\Calendar\Provider\Aggregate_Provider;
use AweBooking\Calendar\Provider\Cached_Provider;

class Main_Calendar extends Abstract_Scheduler {
	/**
	 * List of room-types to display.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $room_types;

	/**
	 * Cache all resources.
	 *
	 * @var \AweBooking\Calendar\Resource\Resources
	 */
	protected $all_resources;

	/**
	 * Cache the state provider.
	 *
	 * @var \AweBooking\Calendar\Provider\Provider_Interface
	 */
	protected $state_provider;

	/**
	 * Cache the booking provider.
	 *
	 * @var \AweBooking\Calendar\Provider\Provider_Interface
	 */
	protected $booking_provider;

	/**
	 * The availability calendar matrix.
	 *
	 * @var array
	 */
	protected $matrices = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->room_types = $this->fetch_room_types();
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		$admin_template = awebooking()->make( 'admin_template' );

		$this->scheduler = new Scheduler;
		foreach ( $this->room_types as $room_type ) {
			$this->scheduler->push( $this->create_scheduler_for( $room_type ) );
		}

		$this->datepoint = Carbonate::today();

		$this->period = new Period(
			Carbonate::today()->subDays( 2 ),
			Carbonate::today()->addDays( 30 )
		);

		/*foreach ( $this->scheduler as $_scheduler ) {
			foreach ( $_scheduler as $calendar ) {
				$this->matrices[ $_scheduler->get_uid() ][ $calendar->get_uid() ] = $calendar->get_itemized( $period );
			}
		}*/


		return $admin_template->partial( 'scheduler/nested-scheduler.php', [ 'calendar' => $this ] );
	}

	/**
	 * Get the scheduler.
	 *
	 * @return \AweBooking\Calendar\Scheduler
	 */
	protected function create_scheduler_for( Room_Type $room_type ) {
		$resources = $this->create_resources_for( $room_type->get_rooms() );

		$calendars = U::collect( $resources )->map(function( $resource ) {
			$calendar = new Calendar( $resource, $this->get_state_provider() );
			$calendar->set_name( $resource->get_title() );

			return $calendar;
		});

		$scheduler = new Scheduler( $calendars );

		$scheduler->set_uid( $room_type->get_id() );
		$scheduler->set_name( $room_type->get_title() );
		$scheduler->set_reference( $room_type );

		return $scheduler;
	}

	/**
	 * Create the calendar resources.
	 *
	 * @return \AweBooking\Calendar\Resource\Resources
	 */
	protected function get_all_resources() {
		if ( is_null( $this->all_resources ) ) {
			$rooms = $this->room_types->reduce( function( $all_rooms, $room_type ) {
				return array_merge( $all_rooms, $room_type->get_rooms()->all() );
			}, [] );

			$this->all_resources = $this->create_resources_for( $rooms );
		}

		return $this->all_resources;
	}

	/**
	 * Create resources for a room-type.
	 *
	 * @return \AweBooking\Calendar\Resource\Resources
	 */
	protected function create_resources_for( $rooms ) {
		$resources = Collection::make( $rooms )->map( function( $room ) {
			$resource = new Resource( $room->get_id(), Constants::STATE_AVAILABLE );
			$resource->set_title( $room->get_name() );

			return $resource;
		});

		return Resources::make( $resources );
	}

	/**
	 * Get the base calendar provider.
	 *
	 * @return \AweBooking\Calendar\Provider\Provider_Interface
	 */
	protected function get_state_provider() {
		if ( is_null( $this->state_provider ) ) {
			$provider = new Aggregate_Provider( [ new State_Provider( $this->get_all_resources() ) ] );
			$provider = apply_filters( 'awebooking/calendar/availability_state_provider', $provider );

			$this->state_provider = new Cached_Provider( $provider );
		}

		return $this->state_provider;
	}

	/**
	 * Get the booking calendar provider.
	 *
	 * @return \AweBooking\Calendar\Provider\Booking_Provider
	 */
	protected function get_booking_provider() {
		if ( is_null( $this->booking_provider ) ) {
			$provider = new Aggregate_Provider( [ new Booking_Provider( $this->get_all_resources() ) ] );
			$provider = apply_filters( 'awebooking/calendar/availability_booking_provider', $provider );

			$this->booking_provider = new Cached_Provider( $provider );
		}

		return $this->booking_provider;
	}
}
