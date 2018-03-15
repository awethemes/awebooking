<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\Support\Carbonate;
use AweBooking\Support\Collection;
use AweBooking\Support\Utils as U;
use AweBooking\Model\Room_Type;

use AweBooking\Calendar\Calendar;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Period\Period;
use AweBooking\Calendar\Event\State_Event;
use AweBooking\Calendar\Event\Pricing_Event;
use AweBooking\Calendar\Event\Booking_Event;

abstract class Abstract_Scheduler {
	/**
	 * The datepoint to begin the scheduler.
	 *
	 * @var \AweBooking\Support\Carbonate
	 */
	protected $datepoint;

	/**
	 * The period from datepoint.
	 *
	 * @var \AweBooking\Calendar\Period\Period
	 */
	protected $period;

	/**
	 * Cache the scheduler.
	 *
	 * @var \AweBooking\Calendar\Scheduler
	 */
	protected $scheduler;

	/**
	 * The matrices of the scheduler.
	 *
	 * @var array
	 */
	protected $matrices;

	/**
	 * List of room-types to display.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $room_types;

	/**
	 * [$conext description]
	 *
	 * @var string
	 */
	protected $context = 'scheduler';

	/**
	 * The main HTML layout.
	 *
	 * @var string
	 */
	protected $main_layout = 'scheduler/scheduler.php';

	/**
	 * [$frozen description]
	 *
	 * @var boolean
	 */
	protected $frozen = false;

	/**
	 * [__construct description]
	 *
	 * @param [type] $datepoint [description]
	 */
	public function __construct( $datepoint = null ) {
		$this->set_datepoint( $datepoint );
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		// Create the scheduler.
		$this->scheduler = $this->create_scheduler();
		$this->matrices  = $this->generate_matrices();

		// Ensure enqueue the schedule-calendar.
		wp_enqueue_script( 'awebooking-scheduler' );

		// Output the HTML.
		awebooking( 'admin_template' )->partial( $this->main_layout, [ 'calendar' => $this ] );
	}

	/**
	 * Set the datepoint, fallback to today.
	 *
	 * @param mixed $datepoint The datepoint.
	 */
	public function set_datepoint( $datepoint ) {
		$today = Carbonate::today();

		if ( is_null( $datepoint ) || empty( $datepoint ) ) {
			$this->datepoint = $today;
		} else {
			$this->datepoint = U::rescue( function () use ( $datepoint ) {
				return Carbonate::create_date( $datepoint );
			}, $today );
		}

		$this->generate_period( $this->datepoint );

		return $this;
	}

	/**
	 * Get the datepoint.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_datepoint() {
		return $this->datepoint;
	}

	public function get_period() {
		return $this->period;
	}

	public function get_scheduler() {
		return $this->scheduler;
	}

	/**
	 * Get the wrapper_classes.
	 *
	 * @return string
	 */
	public function get_wrapper_classes() {
		return '';
	}

	/**
	 * Perform display a private method.
	 *
	 * @param  string $method  The call method.
	 * @param  mixed  ...$args Call method args.
	 * @return void
	 */
	public function perform_call_method( $method, ...$args ) {
		if ( ! method_exists( $this, $method ) ) {
			return;
		}

		ob_start();
		call_user_func_array( [ $this, $method ], $args );
		$contents = ob_get_clean();

		// @codingStandardsIgnoreLine
		echo apply_filters( 'awebooking/scheduler/' . $method, $contents, $args, $this );
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






	/**
	 * [filter_period description]
	 *
	 * @param  [type] $datepoint [description]
	 * @return [type]
	 */
	protected function generate_period( $datepoint ) {
		$this->period = Period::create(
			$datepoint->copy()->subDays( 2 ),
			$datepoint->copy()->addDays( 120 )
		);
	}

	protected function generate_matrices() {
		return Collection::make( $this->scheduler )
			->keyBy( function( $calendar ) {
				return $calendar->get_uid();
			})
			->map( function ( $calendar ) {
				return $calendar->get_itemized( $this->period );
			})->all();
	}


	public function get_calendar_events( Calendar $calendar, Period $period ) {
		$period = $period
			->moveStartDate( '-2 DAYS' )
			->moveEndDate( '+2 DAYS' );

		return $calendar->get_events( $period )
			->reject(function( $e ) {
				return ( $e instanceof State_Event
					|| $e instanceof Booking_Event
					|| $e instanceof Pricing_Event ) && ! $e->get_value();
			});
	}

	protected function fetch_room_types( $args = [] ) {
		$args = apply_filters( 'awebooking/calendar/query_room_types_args', wp_parse_args( $args, [
			'posts_per_page' => 50, // Limit 50 items.
		]), $this );

		return Collection::make( Room_Type::query( $args )->posts )
			->map( function ( $post ) {
				return new Room_Type( $post );
			})->reject( function ( $room_type ) {
				return $room_type->get_total_rooms() <= 0;
			});
	}
}
