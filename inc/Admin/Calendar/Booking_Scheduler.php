<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\Constants;
use Awethemes\Http\Request;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Event\Core\State_Event;
use AweBooking\Calendar\Event\Core\Booking_Event;
use AweBooking\Calendar\Provider\Aggregate_Provider;

class Booking_Scheduler extends Abstract_Scheduler {
	/**
	 * Cache results of room types.
	 *
	 * @var string
	 */
	protected $room_types;

	/**
	 * Store the booking data matrix.
	 *
	 * @var array
	 */
	protected $booking_data;

	/**
	 * The main HTML layout.
	 *
	 * @var string
	 */
	protected $main_layout = 'nested-scheduler.php';

	/**
	 * {@inheritdoc}
	 */
	public function prepare( Request $request ) {
		$rooms_only = null;
		if ( $request->filled( 'only' ) ) {
			$rooms_only = wp_parse_id_list( $request['only'] );
		}

		// Query the list of room type to display.
		$this->room_types = $this->query_room_types([
			'post__in' => $rooms_only,
		]);

		parent::prepare( $request );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setup() {
		$this->events = $this->setup_events( $this->scheduler );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function filter_events( $events ) {
		return $events->reject( function ( $e ) {
			if ( ( ! $e instanceof State_Event && ! $e instanceof Booking_Event ) || 0 === (int) $e->get_value() ) {
				return true;
			}

			// Only get the "UNAVAILABLE" in state events.
			if ( $e instanceof State_Event && $e->get_state() !== Constants::STATE_UNAVAILABLE ) {
				return true;
			}

			return false;
		})->each( function( $e ) {
			$end_date = $e->get_end_date();

			if ( '23:59:00' === $end_date->format( 'H:i:s' ) ) {
				$e->set_end_date( $end_date->addMinute() );
			}

			return $e;
		})->values();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function create_scheduler() {
		// Get all rooms indexed by room type ID.
		$all_rooms = $this->room_types
			->keyBy( 'id' )
			->map( function( $r ) {
				return $r->get_rooms();
			});

		// All resources for the provider.
		$all_resources = $this->create_room_resources(
			$all_rooms->collapse()
		);

		// Create provider with all resources to increase performance.
		$provider = new Aggregate_Provider([
			$this->create_calendar_provider( 'state', $all_resources ),
			$this->create_calendar_provider( 'booking', $all_resources ),
		]);

		// Build the nested scheduler.
		$scheduler = new Scheduler;
		foreach ( $this->room_types as $room_type ) {
			$_resources = $this->create_room_resources( $all_rooms->get( $room_type->get_id() ) );
			$_scheduler = $this->create_scheduler_for( $_resources, $provider );

			$_scheduler->set_uid( $room_type->get_id() );
			$_scheduler->set_name( $room_type->get_title() );
			$_scheduler->set_reference( $room_type );

			$scheduler->push( $_scheduler );
		}

		return $scheduler;
	}

	/**
	 * Display the legends.
	 *
	 * @return void
	 */
	protected function display_legends() {
		echo '<span class="tippy" title="' . esc_html__( 'Blocked', 'awebooking' ) . '"></span>';
		echo '<span class="awebooking-pending-color tippy" title="' . esc_html__( 'Pending', 'awebooking' ) . '"></span>';
		echo '<span class="awebooking-inprocess-color tippy" title="' . esc_html__( 'Processing', 'awebooking' ) . '"></span>';
		echo '<span class="awebooking-on-hold-color tippy" title="' . esc_html__( 'Reserved', 'awebooking' ) . '"></span>';
		echo '<span class="awebooking-deposit-color tippy" title="' . esc_html__( 'Deposit', 'awebooking' ) . '"></span>';
		echo '<span class="awebooking-completed-color tippy" title="' . esc_html__( 'Paid', 'awebooking' ) . '"></span>';
		echo '<span class="awebooking-checked-in-color tippy" title="' . esc_html__( 'Checked In', 'awebooking' ) . '"></span>';
		echo '<span class="awebooking-checked-out-color tippy" title="' . esc_html__( 'Checked Out', 'awebooking' ) . '"></span>';
	}

	/**
	 * Display the toolbars.
	 *
	 * @return void
	 */
	protected function display_toolbars() {
		echo '<div class="scheduler-flexspace"></div>';
		$this->template( 'toolbar/datepicker.php' );
	}

	/**
	 * Display the actions.
	 *
	 * @return void
	 */
	protected function display_actions() { ?>
		<li><a href="#" data-schedule-action="block"><i class="dashicons dashicons-lock"></i><span><?php echo esc_html__( 'Set as Blocked', 'awebooking' ); ?></span></a></li>
		<li><a href="#" data-schedule-action="unblock"><i class="dashicons dashicons-unlock"></i><span><?php echo esc_html__( 'Clear Blocked', 'awebooking' ); ?></span></a></li>
		<?php
	}

	/**
	 * Display content in divider columns.
	 *
	 * @param  \AweBooking\Calendar\Period\Day $day       The current day.
	 * @param  \AweBooking\Calendar\Scheduler  $scheduler The current scheduler.
	 * @return void
	 */
	protected function display_divider_column( $day, $scheduler ) {
		$matrix = $this->get_matrix( $scheduler->get_uid() );
		if ( empty( $matrix ) ) {
			return;
		}

		$available = 0;
		foreach ( $matrix as $item ) {
			if ( 0 === $item->get( $day->format( 'Y-m-d' ) ) ) {
				$available++;
			}
		}

		/* translators: Available rooms */
		$title = sprintf( _nx( '%s room available', '%s rooms available', $available, 'awebooking' ), esc_html( $available ) );
		echo sprintf( '<div class="scheduler-flex--center"><strong title="' . esc_attr( $title ) . '">%1$s/%2$s</strong></div>', esc_html( $available ), esc_html( $scheduler->count() ) );
	}

	/**
	 * Display content in event columns.
	 *
	 * @param  \AweBooking\Calendar\Period\Day $day       The current day.
	 * @param  \AweBooking\Calendar\Caelendar  $calendar  The current loop calendar.
	 * @param  \AweBooking\Calendar\Scheduler  $scheduler The current loop scheduler.
	 * @return void
	 */
	protected function display_event_column( $day, $calendar, $scheduler ) {
		// Get the events.
		$events = $this->get_events( $scheduler->get_uid() . '.' . $calendar->get_uid() );
		if ( is_null( $events ) || $events->isEmpty() ) {
			return;
		}

		// Find events in this day.
		$day_events = $this->find_events_in_date( $day, $events );
		if ( is_null( $day_events ) || $events->isEmpty() ) {
			return;
		}

		// Loop all events and display them.
		foreach ( $day_events as $event ) {
			// Calculate the event attributes.
			$attributes = $this->calculate_event_attributes( $event );

			// Create the template data.
			$_data = compact( 'event', 'day', 'calendar', 'scheduler', 'attributes' );
			$_data['calr'] = $this;

			$contents = ( $event instanceof State_Event )
				? abrs_admin_template( 'calendar/html-blocked-state.php', $_data )
				: abrs_admin_template( 'calendar/html-booking-state.php', $_data );

			print $contents; // WPCS: XSS OK.
		}
	}

	/**
	 * Find the events in a date.
	 *
	 * @param  \AweBooking\Calendar\Period\Day   $date   The date.
	 * @param  \AweBooking\Calendar\Event\Events $events The events.
	 * @return \AweBooking\Calendar\Event\Events|null
	 */
	protected function find_events_in_date( $date, $events ) {
		return $events->filter( function ( $event ) use ( $date ) {
			$date = $date->get_start_date();

			// If the check date same with start date, find event
			// have start-date less than or equal check date.
			if ( $date->eq( $this->period->get_start_date() ) ) {
				return $event->get_start_date()->lte( $date );
			}

			// Otherwise, get event have start-date equal with check date.
			return $event->get_start_date()->eq( $date );
		});
	}

	/**
	 * Calculate event attributes.
	 *
	 * @param  \AweBooking\Calendar\Event\Event $event The state event.
	 * @return array
	 */
	protected function calculate_event_attributes( $event ) {
		// Get the period of this event.
		$period = $event->get_period();

		$classes = [];
		$total_days = (int) $period->days;

		if ( $event instanceof State_Event ) {
			$classes[] = 'scheduler__state-event unavailable';
		} elseif ( $event instanceof Booking_Event ) {
			$classes[] = 'scheduler__booking-event';
		}

		// Calculate in the border.
		if ( $period->start_date->lt( $this->period->start_date ) ) {
			$classes[] = 'continues-prior';
			$total_days--; // Subtract 1 day.
		} elseif ( $period->end_date->gte( $this->period->end_date ) ) {
			$classes[] = 'continues-after';
		}

		return [
			'class' => trim( implode( ' ', $classes ) ),
			'style' => $this->calculate_event_styles( $total_days, $classes ),
			'data-start-date' => $period->start_date->format( 'Y-m-d' ),
			'data-end-date' => $period->end_date->format( 'Y-m-d' ),
		];
	}

	/**
	 * Calculate the event styles.
	 *
	 * @param  int   $total_days Total days.
	 * @param  array $classes    The event classes.
	 * @return string
	 */
	protected function calculate_event_styles( $total_days, $classes ) {
		$width = ( $total_days * 100 ) . '%';

		$style = '';
		if ( in_array( 'continues-prior', $classes ) ) {
			$style .= "width: calc({$width} + 50%); left: 0;";
		} elseif ( in_array( 'continues-after', $classes ) ) {
			$style .= "width: calc({$width} - 50%); left: 50%;";
		} else {
			$style .= "width: {$width}; left: 50%;";
		}

		return $style;
	}
}
