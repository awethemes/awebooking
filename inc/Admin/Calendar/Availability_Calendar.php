<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Period\Month;
use AweBooking\Calendar\Period\Period;
use AweBooking\Calendar\Resource\Resource;
use AweBooking\Calendar\Resource\Resource_Collection;
use AweBooking\Calendar\Provider\State_Provider;
use AweBooking\Calendar\Provider\Booking_Provider;
use AweBooking\Calendar\Provider\Aggregate_Provider;
use AweBooking\Calendar\Event\State_Event;
use AweBooking\Support\Utils as U;

class Availability_Calendar extends Schedule_Calendar {
	/**
	 * The room-type instance.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * List of resource mapped with room-units.
	 *
	 * @var \AweBooking\Calendar\Resource\Resource_Collection
	 */
	protected $resources;

	protected $calendar_provider;
	protected $bookings_provider;

	/**
	 * Constructor.
	 *
	 * @param Room_Type $room_type The room-type instance.
	 * @param array     $options   The calendar options.
	 */
	public function __construct( Room_Type $room_type, array $options = [] ) {
		$this->options = array_merge( $this->options, $options );
		$this->room_type = $room_type;

		$this->resources = $this->create_calendar_resources();
		$this->calendar_provider = $this->create_calendar_provider();
		$this->bookings_provider = $this->create_bookings_provider();
	}

	/**
	 * Get the room-type instance.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Get Calendar resources from room_type.
	 *
	 * @return \AweBooking\Calendar\Resource\Resource_Collection
	 */
	public function get_resources() {
		return $this->resources;
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		$scheduler = $this->get_scheduler()
			->set_name( $this->room_type->get_title() );

		$month = new Month( 2017, 12 );
		echo $this->generate( $scheduler, $month );
	}

	/**
	 * Get the scheduler.
	 *
	 * @return \AweBooking\Calendar\Scheduler
	 */
	protected function get_scheduler() {
		$calendars = U::collect( $this->resources )
			->map(function( $resource ) {
				$calendar = new Calendar( $resource, $this->calendar_provider );

				$calendar->set_name( $resource->get_title() );

				return $calendar;
			});

		return new Scheduler( $calendars );
	}

	/**
	 * Create the calendar resources.
	 *
	 * @return \AweBooking\Calendar\Resource\Resource_Collection
	 */
	protected function create_calendar_resources() {
		$resources = U::collect( $this->room_type->get_rooms() )
			->map(function( $room ) {
				$resource = new Resource( $room->get_id(), Constants::STATE_AVAILABLE );

				$resource->set_title( $room->get_name() );

				return $resource;
			});

		return Resource_Collection::make( $resources );
	}

	/**
	 * Create the base calendar provider.
	 *
	 * @return \AweBooking\Calendar\Provider\Provider_Interface
	 */
	protected function create_calendar_provider() {
		$provider = new Aggregate_Provider( [ new State_Provider( $this->resources ) ] );

		return apply_filters( 'awebooking/availability_calendar/calendar_provider', $provider );
	}

	/**
	 * Create the booking calendar provider.
	 *
	 * @return \AweBooking\Calendar\Provider\Booking_Provider
	 */
	protected function create_bookings_provider() {
		return new Booking_Provider( $this->resources );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_calendar_events( Calendar $calendar, Period $period ) {
		$events = parent::get_calendar_events( $calendar, $period )
			->reject(function( $e ) {
				return ! $e instanceof State_Event;
			});

		$booking_events = ( new Calendar( $calendar->get_resource(), $this->bookings_provider ) )
			->get_events( $period )
			->reject( function( $e ) {
				return ! $e->get_value();
			});

		foreach ( $events as $key => $event ) {
			if ( ! $event->is_pending_state() && ! $event->is_booked_state() ) {
				continue;
			}

			$found_booking_event = $booking_events->first(function( $e ) use ( $event ) {
				return $e->contains_period( $event->get_period() );
			});

			// TODO: Todo something when missing booking.
			if ( ! $found_booking_event ) {
				continue;
			}

			$booking = $found_booking_event->get_booking();
			$event->set_summary( 'Booking #' . $booking->get_id() );
		}

		return $events->indexes();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_cell_event_contents( $events, $date, $calendar ) {
		$html_events = [];
		if ( $events->has( $date->toDateString() ) ) {
			$_events = $events->get( $date->toDateString() );

			foreach ( $_events as $event ) {
				$classes = [];
				$width   = $event->get_period()->getDateInterval()->format( '%r%a' ) + 1;

				$html_events[] = '<i class="awebooking-schedule__event ' . esc_attr( implode( ' ', $classes ) ) . '" style="left: 30px; width:' . esc_attr( $width * 60 ) . 'px">' . $event->get_summary() . '</i>';
			}
		}

		return implode( ' ', $html_events );
	}
}
