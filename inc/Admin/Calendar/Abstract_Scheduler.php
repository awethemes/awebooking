<?php
namespace AweBooking\Admin\Calendar;

use WP_Query;
use AweBooking\Plugin;
use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Period\Iterator_Period;
use AweBooking\Support\Carbonate;
use Awethemes\Http\Request;
use Illuminate\Support\Arr;

abstract class Abstract_Scheduler {
	use Concerns\Calendar_Creator;

	/**
	 * The datepoint to begin the scheduler.
	 *
	 * @var string
	 */
	protected $datepoint;

	/**
	 * The period of the datepoint.
	 *
	 * @var \AweBooking\Support\Period
	 */
	protected $period;

	/**
	 * Cache the main scheduler.
	 *
	 * @var \AweBooking\Calendar\Scheduler
	 */
	protected $scheduler;

	/**
	 * The matrix of the scheduler.
	 *
	 * @var array
	 */
	protected $matrix;

	/**
	 * The events of the scheduler.
	 *
	 * @var array
	 */
	protected $events;

	/**
	 * The scheduler context.
	 *
	 * @var string
	 */
	protected $context = 'scheduler';

	/**
	 * The main HTML layout.
	 *
	 * @var string
	 */
	protected $main_layout = 'scheduler.php';

	/**
	 * Display the Scheduler.
	 *
	 * @return void
	 */
	public function display() {
		if ( is_null( $this->period ) || is_null( $this->scheduler ) ) {
			return;
		}

		// Ensure enqueue the schedule-calendar.
		if ( ! wp_script_is( 'awebooking-scheduler', 'enqueued' ) ) {
			wp_enqueue_script( 'awebooking-scheduler' );
		}

		// Output the HTML.
		$this->template( $this->main_layout );
	}

	/**
	 * Get the matrix.
	 *
	 * @param  string|int|null $item Get a special item.
	 * @return mixed
	 */
	public function get_matrix( $item = null ) {
		if ( is_null( $item ) ) {
			return $this->matrix;
		}

		return Arr::get( $this->matrix, $item );
	}

	/**
	 * Get the events.
	 *
	 * @param  string|int|null $item Get a special item.
	 * @return mixed
	 */
	public function get_events( $item = null ) {
		if ( is_null( $item ) ) {
			return $this->events;
		}

		return Arr::get( $this->events, $item );
	}

	/**
	 * Get the wrapper classes.
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
	 * @param  mixed  ...$args    Call method args.
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
	 * Load a HTML template.
	 *
	 * @param  string $template The template relative path.
	 * @param  array  $vars     The data inject to template.
	 * @return void
	 */
	protected function template( $template, $vars = [] ) {
		$calendar = $this;

		extract( $vars, EXTR_SKIP ); // @codingStandardsIgnoreLine

		include trailingslashit( __DIR__ ) . 'views/' . $template;
	}

	/**
	 * Prepares the Scheduler before it is sent to the client.
	 *
	 * @param  \Awethemes\Http\Request $request The request instance.
	 * @return void
	 */
	public function prepare( Request $request ) {
		$this->datepoint = $this->filter_datepoint(
			$request->filled( 'date' ) ? $request->get( 'date' ) : 'today'
		);

		// Create the period for the Calendar.
		$duration = absint( abrs_option( 'scheduler_display_duration', 30 ) );

		// Higher duration can be affected to browser render.
		if ( $duration > 120 ) { // Limit about 4 months.
			$duration = 120;
		}

		$this->period = Iterator_Period::createFromDuration(
			$this->datepoint, "+{$duration} days"
		)->moveStartDate( '-2 days' );

		// Create the scheduler.
		$this->scheduler = $this->create_scheduler();

		if ( ! is_null( $this->scheduler ) ) {
			$this->setup();
		}
	}

	/**
	 * Create the scheduler.
	 *
	 * @return \AweBooking\Calendar\Scheduler
	 */
	abstract protected function create_scheduler();

	/**
	 * Setup the scheduler.
	 *
	 * @return void
	 */
	protected function setup() {
		$this->events = $this->setup_events( $this->scheduler );
		$this->matrix = $this->setup_matrix( $this->scheduler );
	}

	/**
	 * Setup the matrix (breakdown events by day) for a scheduler.
	 *
	 * @param  \AweBooking\Calendar\Scheduler $scheduler The Scheduler.
	 * @return array
	 */
	protected function setup_matrix( Scheduler $scheduler ) {
		$matrix = [];

		// Move period back 1 day to avoid duplicate queries.
		$period = $this->period->moveStartDate( '-1 day' );

		foreach ( $scheduler->all() as $calendar ) {
			if ( $calendar instanceof Scheduler ) {
				$matrix[ $calendar->get_uid() ] = $this->setup_matrix( $calendar );
			} else {
				$matrix[ $calendar->get_uid() ] = $calendar->get_itemized( $period );
			}
		}

		return $matrix;
	}

	/**
	 * Setup events for a scheduler.
	 *
	 * @param  \AweBooking\Calendar\Scheduler $scheduler The Scheduler.
	 * @param  array                          $options   The options will pass to Calendar::get_events().
	 * @return array
	 */
	protected function setup_events( Scheduler $scheduler, $options = [] ) {
		$events = [];

		// Move period back 1 day.
		$period = $this->period->moveStartDate( '-1 day' );

		foreach ( $scheduler->all() as $calendar ) {
			if ( $calendar instanceof Scheduler ) {
				$events[ $calendar->get_uid() ] = $this->setup_events( $calendar, $options );
			} else {
				$events[ $calendar->get_uid() ] = $this->filter_events( $calendar->get_events( $period, $options ) );
			}
		}

		return $events;
	}

	/**
	 * Perform filter events.
	 *
	 * @param  \AweBooking\Calendar\Event\Events $events The events.
	 * @return \AweBooking\Calendar\Event\Events
	 */
	protected function filter_events( $events ) {
		return $events;
	}

	/**
	 * Perform query room_types.
	 *
	 * @param  array $query The query.
	 * @return \AweBooking\Support\Collection
	 */
	protected function query_room_types( $query = [] ) {
		$wp_query_args = apply_filters( 'awebooking/scheduler/query_room_types', wp_parse_args( $query, [
			'post_type'        => Constants::ROOM_TYPE,
			'post_status'      => 'publish',
			'no_found_rows'    => true,
			'posts_per_page'   => 250,
		]), $this );

		// Create the WP_Query room types.
		$room_types = new WP_Query( $wp_query_args );

		return abrs_collect( $room_types->posts )
			->map_into( Room_Type::class )
			->reject( function ( $r ) {
				return $r->get_total_rooms() === 0;
			})
			->values();
	}

	/**
	 * Filter the datepoint.
	 *
	 * @param string $datepoint The datepoint.
	 */
	protected function filter_datepoint( $datepoint ) {
		$today = Carbonate::today();

		if ( empty( $datepoint ) || 'today' === $datepoint ) {
			return $today->toDateString();
		}

		// If given a Carbonate, return the string that represent for it.
		if ( $datepoint instanceof Carbonate ) {
			return $datepoint->toDateString();
		}

		return abrs_rescue( function () use ( $datepoint ) {
			return Carbonate::create_date( $datepoint );
		}, $today )->toDateString();
	}

	/**
	 * Getter protected property.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __get( $property ) {
		return $this->{$property};
	}
}
