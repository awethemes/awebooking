<?php
namespace AweBooking\Reservation;

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use Illuminate\Support\Arr;
use AweBooking\Availability\Request;
use AweBooking\Reservation\Storage\Store;
use AweBooking\Support\Collection;

class Reservation {
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
	 * The reservation hotel ID.
	 *
	 * @var int
	 */
	public $hotel_id = 0;

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
	 * List of booked rooms.
	 *
	 * @var array
	 */
	protected $booked_rooms;

	/**
	 * List of booked services.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $services;

	/**
	 * The reservation totals.
	 *
	 * @var \AweBooking\Reservation\Totals
	 */
	protected $totals;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Storage\Store $store The store instance.
	 */
	public function __construct( Store $store ) {
		$this->store        = $store;
		$this->services     = new Collection;
		$this->room_stays   = new Collection;
		$this->booked_rooms = [];
		$this->totals       = new Totals( $this );
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

		add_action( 'wp_loaded', [ $this, 'restore_request' ], 10 );
		add_action( 'wp_loaded', [ $this, 'restore' ], 20 );

		do_action( 'abrs_reservation_init', $this );
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
	 * @return \AweBooking\Support\Collection \AweBooking\Reservation\Item[]
	 */
	public function get_room_stays() {
		return $this->room_stays;
	}

	/**
	 * Add a room_stay into the list.
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
	 * Remove a room_stay from the list.
	 *
	 * @param  string $row_id The room stay row ID.
	 * @return \AweBooking\Reservation\Item|false
	 */
	public function remove( $row_id ) {
		if ( ! $this->has( $row_id ) ) {
			return false;
		}

		do_action( 'abrs_remove_room_stay', $row_id, $this );

		$removed = $this->room_stays->pull( $row_id );

		do_action( 'abrs_room_stay_removed', $removed, $this );

		$this->store();
		$this->calculate_totals();

		return $removed;
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

		// Create the room rate, perform check after.
		$room_rate = abrs_get_room_rate( compact( 'request', 'room_type', 'rate_plan' ) );
		$this->check_room_rate( $room_rate, $quantity );

		$request = $room_rate->get_request();
		list ( $room_type, $rate_plan ) = [ $room_rate->get_room_type(), $room_rate->get_rate_plan() ];

		$options = array_merge( $request->to_array(), [
			'room_type' => $room_type->get_id(),
			'rate_plan' => $rate_plan->get_id(),
		]);

		$row_id = Item::generate_row_id( $room_type->get_id(), $options );

		if ( $this->has( $row_id ) ) {
			$room_stay = $this->get( $row_id );
			$room_stay->increment( $quantity );
		} else {
			// Create the room stay instance.
			$room_stay = new Item([
				'id'       => $room_type->get_id(),
				'name'     => $room_type->get( 'title' ),
				'price'    => $room_rate->get_rate()->as_numeric(),
				'quantity' => $quantity,
				'options'  => $options,
			]);
		}

		$room_stay->set_data( $room_rate );
		$room_stay->associate( $room_type );

		if ( abrs_tax_enabled() ) {
			$room_stay['tax_rate'] = Arr::get( $room_rate->get_tax_rate(), 'rate', 0 );
		}

		// In single mode, we'll clear all room stays was added before.
		if ( abrs_is_reservation_mode( Constants::MODE_SINGLE ) ) {
			$this->room_stays->clear();
		}

		// Take the room IDs for temporary lock.
		$take_rooms = $room_rate->get_remain_rooms()
			->take( $room_stay->get_quantity() )
			->all();

		$room_ids = array_keys( $take_rooms );

		$this->add_booked_rooms( $room_ids );
		$this->room_stays->put( $room_stay->get_row_id(), $room_stay );

		do_action( 'abrs_room_stay_added', $room_stay );

		$this->calculate_totals();
		$this->store();

		return $room_stay;
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

		do_action( 'abrs_check_room_rate', $room_rate, compact( 'quantity' ), $this );
	}

	/**
	 * Flush the session data.
	 *
	 * @return void
	 */
	public function flush() {
		$this->room_stays->clear();

		$this->current_request = null;
		$this->previous_request = null;

		$this->store->flush( 'room_stays' );
		$this->store->flush( 'booked_rooms' );
		$this->store->flush( 'previous_request' );

		do_action( 'abrs_reservation_emptied', $this );
	}

	/**
	 * Save the reservation state.
	 *
	 * @return void
	 */
	public function store() {
		if ( $this->room_stays->isEmpty() ) {
			$this->flush();
			return;
		}

		$this->store->put( 'booked_rooms', $this->booked_rooms );
		$this->store->put( 'room_stays', $this->room_stays->to_array() );

		if ( $this->current_request ) {
			$this->store->put( 'previous_request', $this->current_request );
		}

		do_action( 'abrs_reservation_stored', $this );
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

		$booked_rooms = $this->store->get( 'booked_rooms' );
		if ( empty( $booked_rooms ) || ! is_array( $booked_rooms ) ) {
			return;
		}

		// Set the booked_rooms.
		$this->booked_rooms = wp_parse_id_list( $booked_rooms );

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
			$room_stay = ( new Item )->update( $values );
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

		do_action( 'abrs_reservation_restored', $this );
		$this->calculate_totals();

		// Re-store the session.
		if ( count( $session_room_stays ) !== count( $this->room_stays ) ) {
			$this->store();
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

		do_action( 'abrs_reservation_request_restored', $this );
	}

	/**
	 * Calculate the totals.
	 *
	 * @return void
	 */
	public function calculate_totals() {
		$this->totals->calculate();
	}

	/**
	 * Gets the total after calculation.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_total() {
		return $this->totals->get( 'total' );
	}

	/**
	 * Gets the totals instance.
	 *
	 * @return Totals
	 */
	public function get_totals() {
		return $this->totals;
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
	 * Gets the current res_request.
	 *
	 * @return \AweBooking\Availability\Request|null
	 */
	public function get_current_request() {
		return $this->current_request;
	}

	/**
	 * Sets the current res_request.
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
	 * Resolve the res_request.
	 *
	 * @return \AweBooking\Availability\Request|null
	 */
	public function resolve_res_request() {
		$res_request = $this->get_current_request();

		if ( ! $res_request ) {
			$res_request = $this->get_previous_request();
		}

		return $res_request;
	}

	/**
	 * Gets the booked rooms.
	 *
	 * @return array
	 */
	public function get_booked_rooms() {
		return $this->booked_rooms;
	}

	/**
	 * Add booked rooms.
	 *
	 * @param array $rooms The room IDs.
	 */
	protected function add_booked_rooms( array $rooms ) {
		$this->booked_rooms = wp_parse_id_list(
			array_merge( $this->booked_rooms, $rooms )
		);
	}

	/**
	 * Gets the current hotel instance.
	 *
	 * @return \AweBooking\Model\Hotel
	 */
	public function get_hotel() {
		return $this->hotel_id
			? abrs_get_hotel( $this->hotel_id )
			: abrs_get_primary_hotel();
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
