<?php

namespace AweBooking\Admin\Calendar;

use WP_Query;
use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Calendar\Scheduler;
use AweBooking\Calendar\Period\Iterator_Period;
use AweBooking\Support\Carbonate;
use WPLibs\Http\Request;
use Illuminate\Support\Arr;

abstract class Abstract_Scheduler {
	use Concerns\Calendar_Creator;

	/**
	 * Current http request.
	 *
	 * @var \WPLibs\Http\Request
	 */
	protected $request;

	/**
	 * Cache results of room types.
	 *
	 * @var \AweBooking\Support\Collection|Room_Type[]
	 */
	protected $room_types;

	/**
	 * //
	 *
	 * @var array
	 */
	protected $pagination_args = [
		'per_page'    => 15,
		'total_items' => 0,
		'total_pages' => 0,
	];

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
	 * Prepares the Scheduler before it is sent to the client.
	 *
	 * @param  \WPLibs\Http\Request $request The request instance.
	 * @return void
	 */
	public function prepare( Request $request ) {
		$this->request = $request;

		$this->datepoint = $this->filter_datepoint(
			$request->filled( 'date' ) ? $request->get( 'date' ) : 'today'
		);

		// Create the period for the Calendar.
		$duration = absint( abrs_get_option( 'scheduler_display_duration', 30 ) );

		// Higher duration can be affected to browser render.
		if ( $duration > 90 ) { // Limit about 3 months.
			$duration = 90;
		}

		// Create the period.
		$this->period = Iterator_Period::createFromDuration( abrs_date( $this->datepoint ), "+{$duration} days" )
		                               ->moveStartDate( '-2 days' );

		// Create the scheduler.
		$this->scheduler = $this->create_scheduler();

		if ( ! is_null( $this->scheduler ) ) {
			$this->setup();
		}
	}

	/**
	 * Display the Scheduler.
	 *
	 * @return void
	 */
	public function display() {
		if ( is_null( $this->period ) || is_null( $this->scheduler ) ) {
			return;
		}

		if ( abrs_blank( $this->scheduler ) ) {
			$this->template( 'empty.php' );

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
	 * @param  string|int|null $item Get a specified item.
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
	 * @param  string|int|null $item Get a specified item.
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
	 * @param  mixed  ...$args Call method args.
	 * @return void
	 */
	public function call( $method, ...$args ) {
		if ( ! method_exists( $this, $method ) ) {
			return;
		}

		ob_start();
		call_user_func_array( [ $this, $method ], $args );
		$contents = ob_get_clean();

		// @codingStandardsIgnoreLine
		echo apply_filters( 'abrs_scheduler_' . $method, $contents, $args, $this );
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
	 * Create the scheduler.
	 *
	 * @return \AweBooking\Calendar\Scheduler
	 */
	abstract protected function create_scheduler();

	/**
	 * Display the toolbars.
	 *
	 * @return void
	 */
	protected function display_toolbar() {
		echo '<div class="scheduler-flexspace"></div>';
		$this->template( 'toolbar/datepicker.php' );
		$this->template( 'toolbar/pagination.php' );
	}

	/**
	 * Display the main toolbars.
	 *
	 * @return void
	 */
	protected function display_main_toolbar() {
		echo '<div class="abrs-spacer"></div>';
		$this->template( 'main-toolbar/hotel-filter.php' );
	}

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
				/* @var \AweBooking\Calendar\Calendar $calendar */
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
				/* @var \AweBooking\Calendar\Calendar $calendar */
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
	 * @return void
	 */
	protected function query_room_types( $query = [] ) {
		if ( $this->request->filled( 'paged' ) ) {
			$query['paged'] = max( 1, (int) $this->request->get( 'paged' ) );
		}

		if ( $this->request->filled( 'only' ) ) {
			$query['post__in'] = wp_parse_id_list( $this->request->get( 'only' ) );
		}

		if ( $this->request->filled( 'hotel' ) ) {
			$query['meta_query'][] = [
				'key'     => '_hotel_id',
				'value'   => absint( $this->request->get( 'hotel' ) ),
				'type'    => 'numeric',
				'compare' => '=',
			];
		}

		$wp_query_args = apply_filters( 'abrs_scheduler_query_room_types', wp_parse_args( $query, [
			'post_type'      => Constants::ROOM_TYPE,
			'post_status'    => 'publish',
			'no_found_rows'  => false,
			'posts_per_page' => $this->pagination_args['per_page'] ?: 15,
		] ), $this );

		// Create the WP_Query room types.
		$query_results = new WP_Query( $wp_query_args );

		$room_types = $query_results->posts;

		// Prime caches to reduce future queries.
		abrs_prime_room_caches( wp_list_pluck( $room_types, 'ID' ) );

		$this->room_types = abrs_collect( $room_types )
			->map_into( Room_Type::class )
			->reject( function ( Room_Type $r ) {
				return count( $r->get_rooms() ) === 0;
			} )->values();

		$this->pagination_args['total_items'] = $query_results->max_num_pages;

		if ( ! $this->pagination_args['total_pages'] && $this->pagination_args['per_page'] > 0 ) {
			$this->pagination_args['total_pages'] = ceil( $this->pagination_args['total_items'] / $this->pagination_args['per_page'] );
		}

		return $this->room_types;
	}

	/**
	 * Filter the datepoint.
	 *
	 * @param  string $datepoint The datepoint.
	 * @return string
	 */
	protected function filter_datepoint( $datepoint ) {
		$today = Carbonate::today( abrs_get_wp_timezone() );

		if ( empty( $datepoint ) || 'today' === $datepoint ) {
			return $today->toDateString();
		}

		// If given a Carbonate, return the string that represent for it.
		if ( $datepoint instanceof Carbonate ) {
			return $datepoint->toDateString();
		}

		return abrs_rescue( function () use ( $datepoint ) {
			return Carbonate::create_date( $datepoint, abrs_get_wp_timezone() );
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

	/**
	 * Check exists a protected property.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __isset( $property ) {
		return isset( $this->{$property} );
	}
}
