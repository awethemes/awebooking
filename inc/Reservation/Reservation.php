<?php
namespace AweBooking\Reservation;

use AweBooking\Constants;
use Illuminate\Support\Arr;
use AweBooking\Availability\Request;
use AweBooking\Reservation\Storage\Store;
use AweBooking\Support\Collection;

class Reservation {
	/**
	 * The store instance.
	 *
	 * @var \AweBooking\Reservation\Storage\Store
	 */
	protected $store;

	/**
	 * The previous res request.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	protected $previous_request;

	/**
	 * The current res request.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	protected $current_request;

	/**
	 * List of booked room stays.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $room_stays;

	/**
	 * List of booked services.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $services;

	/**
	 * The reservation source.
	 *
	 * @var string
	 */
	public $source;

	/**
	 * ISO currency code.
	 *
	 * @var string
	 */
	public $currency;

	/**
	 * ISO language code.
	 *
	 * @var string
	 */
	public $language;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Storage\Store $store The store instance.
	 */
	public function __construct( Store $store ) {
		$this->store      = $store;
		$this->room_stays = new Collection;
		$this->services   = new Collection;
	}

	/**
	 * Init the reservation.
	 *
	 * @return void
	 */
	public function init() {
		$this->source   = 'website';
		$this->currency = abrs_current_currency();
		$this->language = abrs_running_on_multilanguage() ? awebooking( 'multilingual' )->get_current_language() : '';

		add_action( 'wp_loaded', [ $this, 'restore_request' ] );
		add_action( 'wp_loaded', [ $this, 'restore' ] );
		add_action( 'awebooking/search_room_rate', [ $this, 'exclude_existing_rooms' ], 5, 2 );

		do_action( 'awebooking/reservation/initial', $this );
	}

	/**
	 * Exclude existing rooms in the reservation.
	 *
	 * @param  \AweBooking\Availability\Room_Rate $room_rate The room rate instance.
	 * @param  \AweBooking\Availability\Request   $request   The res request instance.
	 * @return \AweBooking\Availability\Room_Rate
	 */
	public function exclude_existing_rooms( $room_rate, $request ) {
		foreach ( $this->get_room_stays() as $room_stay ) {
			$quantity = $room_stay->quantity;
		}

		return $room_rate;
	}

	/**
	 * Add a room stay into the list.
	 *
	 * @param \AweBooking\Availability\Request $request   The res request instance.
	 * @param int                              $room_type The room type ID.
	 * @param int|null                         $rate_plan The rate plan ID.
	 * @param int                              $quantity  The number of room to book.
	 *
	 * @return \AweBooking\Reservation\Item
	 */
	public function add( Request $request, $room_type, $rate_plan = null, $quantity = 1 ) {
		return $this->add_room_stay( $request, $room_type, $rate_plan, $quantity );
	}

	/**
	 * Retrieve a room stay item.
	 *
	 * @param  string $row_id The room stay row ID.
	 *
	 * @return \AweBooking\Reservation\Item|null
	 */
	public function get( $row_id ) {
		return $this->room_stays->get( $row_id );
	}

	/**
	 * Determine if a room item exists.
	 *
	 * @param  string $row_id The room stay row ID.
	 * @return bool
	 */
	public function has( $row_id ) {
		return $this->room_stays->has( $row_id );
	}

	/**
	 * Search the room_stays matching the given search closure.
	 *
	 * @param  callable $search Search logic.
	 * @return \AweBooking\Support\Collection
	 */
	public function search( $search ) {
		return $this->room_stays->filter( $search );
	}

	/**
	 * Checks if the reservation is empty.
	 *
	 * @return bool
	 */
	public function is_empty() {
		return 0 === count( $this->get_room_stays() );
	}

	/**
	 * Gets the room stays.
	 *
	 * @return \AweBooking\Support\Collection \AweBooking\Reservation\Room_Stay[]
	 */
	public function get_room_stays() {
		return $this->room_stays;
	}

	/**
	 * Add a room stay into the list.
	 *
	 * @param \AweBooking\Availability\Request $request   The res request instance.
	 * @param int                              $room_type The room type ID.
	 * @param int|null                         $rate_plan The rate plan ID.
	 * @param int                              $quantity  The number of room to book.
	 *
	 * @return \AweBooking\Reservation\Item
	 */
	public function add_room_stay( Request $request, $room_type, $rate_plan = null, $quantity = 1 ) {
		$this->set_current_request( $request );

		if ( is_null( $rate_plan ) ) {
			$rate_plan = $room_type;
		}

		$room_rate = abrs_get_room_rate( compact( 'request', 'room_type', 'rate_plan' ) );
		$this->check_room_rate( $room_rate, $quantity );

		$room_stay = new Room_Stay([
			'id'       => $room_rate->room_type->get_id(),
			'name'     => $room_rate->room_type->get( 'title' ),
			'price'    => $room_rate->get_rate()->as_numeric(),
			'quantity' => $quantity,
			'tax_rate' => 0,
			'options'  => $this->generate_room_stay_data( $room_rate, $quantity ),
		]);

		$room_stay->set_data( $room_rate );
		$room_stay->associate( $room_rate->get_room_type() );

		// In single mode, we'll clear all room stays was added before.
		if ( abrs_is_reservation_mode( Constants::MODE_SINGLE ) ) {
			$this->room_stays->clear();
		}

		$this->room_stays->put( $room_stay->get_row_id(), $room_stay );

		do_action( 'awebooking/reservation/added_room_stay', $room_stay );

		$this->store();

		return $room_stay;
	}

	/**
	 * Generate the room stay data.
	 *
	 * @param \AweBooking\Availability\Room_Rate $room_rate The room rate instance.
	 * @param int                                $quantity  Number of rooms.
	 * @return array
	 */
	protected function generate_room_stay_data( $room_rate, $quantity = 1 ) {
		$request = $room_rate->get_request();

		list ( $room_type, $rate_plan ) = [ $room_rate->get_room_type(), $room_rate->get_rate_plan() ];

		return array_merge( $request->to_array(), [
			'room_type' => $room_type->get_id(),
			'rate_plan' => $rate_plan->get_id(),
		]);
	}

	/**
	 * Perform validate the room rate.
	 *
	 * @param \AweBooking\Availability\Room_Rate|null $room_rate The room rate.
	 * @param int|null                                $quantity  Optional, check with quantity.
	 * @return void
	 *
	 * @throws \AweBooking\Reservation\Exceptions\RoomRateException
	 * @throws \AweBooking\Reservation\Exceptions\PastDateException
	 * @throws \AweBooking\Reservation\Exceptions\FullyBookedException
	 * @throws \AweBooking\Reservation\Exceptions\NotEnoughRoomsException
	 */
	protected function check_room_rate( $room_rate, $quantity = null ) {
		if ( is_null( $room_rate ) || is_wp_error( $room_rate ) ) {
			throw new Exceptions\RoomRateException( esc_html__( 'We are unable to find the room rate match with your request. Please try again.', 'awebooking' ) );
		}

		$timespan = $room_rate->get_timespan();
		$timespan->requires_minimum_nights( 1 );

		if ( abrs_date( $timespan->get_start_date() )->lt( abrs_date( 'today' ) ) ) {
			throw new Exceptions\PastDateException( esc_html__( 'You cannot perform reservation in the past! Please re-enter dates.', 'awebooking' ) );
		}

		if ( 0 === count( $remaining = $room_rate->get_remain_rooms() ) ) {
			throw new Exceptions\FullyBookedException( esc_html__( 'Sorry, the room is fully booked, please try another date.', 'awebooking' ) );
		}

		if ( ! is_null( $quantity ) && count( $remaining ) < $quantity ) {
			/* translators: Number of remaining rooms */
			throw new Exceptions\NotEnoughRoomsException( sprintf( esc_html__( 'You cannot book that number of rooms because there are not enough rooms (%1$s remaining)', 'awebooking' ), count( $remaining ) ) );
		}

		if ( $room_rate->get_rate()->is_negative() || $room_rate->get_rate()->is_zero() ) {
			throw new Exceptions\RoomRateException( esc_html__( 'Sorry, the room is not available. Please try another room.', 'awebooking' ) );
		}

		if ( $room_rate->has_error() || ! $room_rate->is_visible() ) {
			throw new Exceptions\RoomRateException( esc_html__( 'Sorry, some kind of error has occurred. Please try again.', 'awebooking' ) );
		}

		do_action( 'awebooking/reservation/check_room_rate', $room_rate, compact( 'quantity' ), $this );
	}

	/**
	 * Flush the session data.
	 *
	 * @return void
	 */
	public function flush() {
		$this->room_stays->clear();
		$this->store->flush( 'room_stays' );

		$this->current_request = null;

		$this->previous_request = null;
		$this->store->flush( 'previous_request' );

		do_action( 'awebooking/reservation/flush', $this );
	}

	/**
	 * Save the reservation state.
	 *
	 * @return void
	 */
	public function store() {
		if ( $this->room_stays->isEmpty() || ! $this->current_request ) {
			return;
		}

		$this->store->put( 'room_stays', $this->room_stays->to_array() );

		$this->store->put( 'previous_request', $this->current_request );

		do_action( 'awebooking/reservation/stored', $this );
	}

	/**
	 * Restore the reservation from its saved state.
	 *
	 * @return void
	 */
	public function restore() {
		if ( is_null( $this->previous_request ) ) {
			return;
		}

		$session_room_stays = $this->store->get( 'room_stays' );
		if ( empty( $session_room_stays ) || ! is_array( $session_room_stays ) ) {
			return;
		}

		if ( abrs_is_reservation_mode( Constants::MODE_SINGLE ) ) {
			$session_room_stays = [ Arr::last( $session_room_stays ) ];
		}

		// Prime caches to reduce future queries.
		if ( function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( array_keys( wp_list_pluck( $session_room_stays, 'id' ) ) );
		}

		// Perform filter valid room stays.
		foreach ( $session_room_stays as $row_id => $values ) {
			if ( ! Arr::has( $values, [ 'id', 'row_id', 'quantity', 'options' ] )
				|| $values['quantity'] <= 0 || empty( $values['options'] ) ) {
				continue;
			}

			// Transform the room stay array to object.
			$room_stay = ( new Room_Stay )->update( $values );
			if ( ! hash_equals( $values['row_id'], $room_stay->get_row_id() ) ) {
				continue;
			}

			// Re-check the availability of the rate.
			$room_rate = abrs_get_room_rate( $room_stay->get_options()->all() );

			try {
				$this->check_room_rate( $room_rate, $room_stay->get_quantity() );
			} catch ( \Exception $e ) {
				continue;
			}

			$room_stay->set_data( $room_rate );

			// Put the room stay into the list.
			$this->room_stays->put( $row_id, $room_stay );
		}

		do_action( 'awebooking/reservation/restored', $this );

		// Re-store the session.
		if ( count( $session_room_stays ) !== count( $this->room_stays ) ) {
			$this->store();
			$this->calculate_totals();
		}
	}

	/**
	 * Restore the res request from the store.
	 *
	 * @return void
	 */
	public function restore_request() {
		$previous_request = $this->store->get( 'previous_request' );

		if ( ! $previous_request || ! $previous_request instanceof Request ) {
			return;
		}

		$this->previous_request = $previous_request;
	}

	/**
	 * Calculate the totals.
	 *
	 * @return void
	 */
	public function calculate_totals() {
	}

	/**
	 * Gets the previous_request store in the session.
	 *
	 * @return \AweBooking\Availability\Request|null
	 */
	public function get_previous_request() {
		return $this->previous_request;
	}

	/**
	 * Sets the previous_request.
	 *
	 * @param \AweBooking\Availability\Request $request The res request.
	 */
	public function set_previous_request( Request $request ) {
		$this->previous_request = $request;

		return $this;
	}

	/**
	 * Gets the current res request.
	 *
	 * @return \AweBooking\Availability\Request|null
	 */
	public function get_current_request() {
		return $this->current_request;
	}

	/**
	 * Sets the current res request.
	 *
	 * @param  \AweBooking\Availability\Request $current_request The request instance.
	 *
	 * @return $this
	 */
	public function set_current_request( Request $current_request ) {
		$this->current_request = $current_request;

		return $this;
	}

	/**
	 * Gets the reservation source.
	 *
	 * @return string
	 */
	public function get_source() {
		return $this->source;
	}

	/**
	 * Sets the reservation source.
	 *
	 * @param  string $source The reservation source.
	 *
	 * @return $this
	 */
	public function set_source( $source ) {
		$this->source = $source;

		return $this;
	}

	/**
	 * Gets the ISO currency code.
	 *
	 * @return string
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * Sets the ISO currency code.
	 *
	 * @param  string $currency The ISO code currency.
	 * @return $this
	 */
	public function set_currency( $currency ) {
		$this->currency = $currency;

		return $this;
	}

	/**
	 * Gets the ISO language code.
	 *
	 * @return string
	 */
	public function get_language() {
		return $this->language;
	}

	/**
	 * Sets the ISO language code.
	 *
	 * @param string $language The language code.
	 */
	public function set_language( $language ) {
		$this->language = $language;

		return $this;
	}
}
