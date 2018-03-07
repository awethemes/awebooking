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
use AweBooking\Calendar\Resource\Resource_Collection;
use AweBooking\Calendar\Provider\State_Provider;
use AweBooking\Calendar\Provider\Booking_Provider;
use AweBooking\Calendar\Provider\Aggregate_Provider;
use AweBooking\Calendar\Provider\Cached_Provider;

class Main_Calendar extends Schedule_Calendar {
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
	 * Constructor.
	 *
	 * @param array $options The calendar options.
	 */
	public function __construct( $options = [] ) {
		$this->options = array_merge( $this->options, $options );

		$this->room_types = $this->fetch_room_types();
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		$admin_template = awebooking()->make( 'admin_template' );

		$scheduler = new Scheduler;
		foreach ( $this->room_types as $room_type ) {
			$scheduler->push( $this->create_scheduler_for( $room_type ) );
		}

		$cal = $this;
		$actions_menu = $this->generate_actions_menu();

		$period = new Period(
			Carbonate::today()->subDays( 2 ),
			Carbonate::today()->addDays( 30 )
		);

		// Enqueue the schedule-calendar.
		wp_enqueue_script( 'awebooking-schedule-calendar' );

		return $admin_template->partial( 'calendar/html-layout.php',
			compact( 'cal', 'scheduler', 'period', 'actions_menu' )
		);
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
	 * {@inheritdoc}
	 */
	public function get_calendar_events( Calendar $calendar, Period $period ) {
		$events = parent::get_calendar_events( $calendar, $period )
			->reject(function( $e ) {
				return ! $e instanceof State_Event;
			})->each( function ( $e ) use ( $period ) {
				if ( $e->get_start_date()->lt( $period->get_start_date() ) ) {
					dump( 1 );
				}

				if ( $e->get_end_date()->gt( $period->get_end_date()->subDay() ) ) {
					$e->set_end_date( $period->get_end_date()->subDay() );

					// dump( $e );
					// dump( $period->get_end_date() );
				}
			});

		$booking_events = ( new Calendar( $calendar->get_resource(), $this->get_booking_provider() ) )
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
	public function cell_event_contents( $events, $date, $calendar ) {
		if ( ! $events->has( $date->format( 'Y-m-d' ) ) ) {
			return;
		}

		$html_events = [];
		$day_events = $events->get( $date->format( 'Y-m-d' ) );

		foreach ( $day_events as $event ) {
			$width = $event->get_period()->getDateInterval()->format( '%r%a' ) + 1;

			$html  = '<div class="awebooking-schedule__event ' . esc_attr( implode( ' ', $this->get_event_classes( $event ) ) ) . '" style="width: ' .  ( $width * 100 ) . '%">';

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

		echo implode( ' ', $html_events );
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

		$scheduler->set_name( $room_type->get_title() );
		$scheduler->set_reference( $room_type );

		return $scheduler;
	}

	/**
	 * Create resources for a room-type.
	 *
	 * @return \AweBooking\Calendar\Resource\Resource_Collection
	 */
	protected function create_resources_for( $rooms ) {
		$resources = Collection::make( $rooms )->map( function( $room ) {
			$resource = new Resource( $room->get_id(), Constants::STATE_AVAILABLE );

			$resource->set_title( $room->get_name() );

			return $resource;
		});

		return Resource_Collection::make( $resources );
	}

	/**
	 * Create the calendar resources.
	 *
	 * @return \AweBooking\Calendar\Resource\Resource_Collection
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
