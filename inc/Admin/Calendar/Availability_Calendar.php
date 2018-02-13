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
use AweBooking\Calendar\Provider\Cached_Provider;
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
	protected $booking_provider;

	/**
	 * Constructor.
	 *
	 * @param Room_Type $room_type The room-type instance.
	 * @param array     $options   The calendar options.
	 */
	public function __construct( Room_Type $room_type, array $options = [] ) {
		$this->room_type = $room_type;
		$this->options = array_merge( $this->options, $options );
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		$this->resources = $this->create_calendar_resources();
		$this->calendar_provider = $this->create_calendar_provider();
		$this->booking_provider  = $this->create_booking_provider();

		$scheduler = $this->get_scheduler()
			->set_name( $this->room_type->get_title() );

		$month = new Month( 2018, 02 );

		echo $this->generate( $scheduler, $month );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_actions_menu() {
		return [
			[ 'id' => 'awebooking-set-price', 'icon' => 'dashicons dashicons-edit', 'href' => '#awebooking-add-line-item-popup', 'name' => 'Adjust Price' ],
			[ 'icon' => 'dashicons dashicons-update', 'name' => 'Clear Adjust' ],
		];
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

		$provider = apply_filters( 'awebooking/availability_calendar/calendar_provider', $provider );

		return new Cached_Provider( $provider );
	}

	/**
	 * Create the booking calendar provider.
	 *
	 * @return \AweBooking\Calendar\Provider\Booking_Provider
	 */
	protected function create_booking_provider() {
		$provider = new Booking_Provider( $this->resources );

		return new Cached_Provider( $provider );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_calendar_events( Calendar $calendar, Period $period ) {
		$events = parent::get_calendar_events( $calendar, $period )
			->reject(function( $e ) {
				return ! $e instanceof State_Event;
			});

		$booking_events = ( new Calendar( $calendar->get_resource(), $this->booking_provider ) )
			->get_events( $period )
			->reject( function( $e ) {
				return ! $e->get_value();
			});

		foreach ( $events as $key => $event ) {
			if ( ! $event->is_pending_state() && ! $event->is_booked_state() ) {
				continue;
			}

			$booking_event = $booking_events->first(function( $e ) use ( $event ) {
				return $e->contains_period( $event->get_period() );
			});

			if ( ! is_null( $booking_event ) ) {
				$this->setup_event_with_booking( $event, $booking_event->get_booking() );
			}
		}

		return $events->indexes();
	}

	protected function setup_event_with_booking( $event, $booking ) {
		$event->set_summary( 'Booking #' . $booking->get_id() );

		ob_start();
		include trailingslashit( __DIR__ ) . 'html-booking-summary.php';
		$booking_summary = trim( ob_get_clean() );

		$event->set_description( $booking_summary );

		$event->set_url( $booking->get_edit_url() );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_cell_event_contents( $events, $date, $calendar ) {
		if ( ! $events->has( $date->toDateString() ) ) {
			return;
		}

		$html_events = [];
		$day_events = $events->get( $date->toDateString() );

		foreach ( $day_events as $event ) {
			$width = $event->get_period()->getDateInterval()->format( '%r%a' ) + 1;


			$html  = '<div class="awebooking-schedule__event ' . esc_attr( implode( ' ', $this->get_event_classes( $event ) ) ) . '" style="left: 30px; width:' . esc_attr( $width * 60 ) . 'px">';

			if ( $event_url = $event->get_url() ) {
				$html .= '<a href="' . esc_url( $event_url ) . '" style="width: 100%; height: 100%; display: block;">';
			}

			$html .= '<span class="screen-reader-text">' . $event->get_summary() . '</span>';

			if ( $event_url = $event->get_url() ) {
				$html .= '</a>';
			}

			$html .= '<div class="popper" style="display: none;"><div class="popper__arrow" x-arrow></div>' . $event->get_description() . '</div>';
			$html .= '</div>';

			$html_events[] = $html;
		}

		return implode( ' ', $html_events );
	}

	protected function get_event_classes( $event ) {
		$classes = [];

		switch ( true ) {
			case $event->is_unavailable_state():
				$classes[] = 'unavailable';
				$event->set_description( esc_html__( 'Period is blocked.', 'awebooking' ) );
				break;

			case $event->is_pending_state():
				$classes[] = 'pending';
				break;

			case $event->is_booked_state():
				$classes[] = 'booked';
				break;
		}

		return $classes;
	}
}
