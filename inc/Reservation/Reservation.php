<?php
namespace AweBooking\Reservation;

use AweBooking\Availability\Query;
use AweBooking\Availability\Request;
use AweBooking\Reservation\Storage\Store;
use AweBooking\Support\Collection;
use Illuminate\Support\Arr;

class Reservation {
	/**
	 * The store instance.
	 *
	 * @var \AweBooking\Reservation\Storage\Store
	 */
	protected $store;

	/**
	 * The current request.
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

		// Perform restore the reservation when wp_loaded.
		if ( ! did_action( 'wp_loaded' ) ) {
			add_action( 'wp_loaded', [ $this, 'restore' ] );
		}

		do_action( 'awebooking/reservation/initial', $this );
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

		$room_type = abrs_get_room_type( $room_type );
		$rate_plan = abrs_get_rate_plan( $rate_plan ?: $room_type );

		if ( $quantity <= 0 || ! $rate_plan || ! $room_type || 'trash' === $room_type->get( 'status' ) ) {
			return;
		}

		// Query the room rate.
		$room_rate = ( new Query( $request ) )->room_rate( $room_type, $rate_plan );
		$this->check_room_rate( $room_rate, $quantity );

		$room_stay = new Item([
			'id'       => $room_type->get_id(),
			'name'     => $room_type->get( 'title' ),
			'price'    => $room_rate->get_rate(),
			'quantity' => $quantity,
			'tax_rate' => 0,
			'options'  => $this->generate_room_stay_data( $room_rate ),
		]);

		$room_stay->associate( $room_type );
		$room_stay->set_data( $room_rate );

		$this->room_stays->put( $room_stay->get_row_id(), $room_stay );

		do_action( 'awebooking/reservation/added_room_stay', $room_stay );

		$this->store();

		return $room_stay;
	}

	/**
	 * Generate the room stay data.
	 *
	 * @param \AweBooking\Availability\Room_Rate $room_rate The room rate instance.
	 * @return array
	 */
	protected function generate_room_stay_data( $room_rate, $rooms = [] ) {
		$request = $room_rate->get_request();

		list ( $room_type, $rate_plan ) = [ $room_rate->get_room_type(), $room_rate->get_rate_plan() ];

		return array_merge( $request->to_array(), [
			'rooms'     => $rooms,
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

		if ( $room_rate->get_rate() <= 0 ) {
			throw new Exceptions\RoomRateException( esc_html__( 'Sorry, the room is not available. Please try another room.', 'awebooking' ) );
		}

		if ( $room_rate->has_error() ) {
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

		do_action( 'awebooking/reservation/flush', $this );
	}

	/**
	 * Save the reservation state.
	 *
	 * @return void
	 */
	public function store() {
		$this->store->put( 'room_stays', $this->room_stays->to_array() );
	}

	/**
	 * Restore the reservation from its saved state.
	 *
	 * @return void
	 */
	public function restore() {
		$session_room_stays = $this->store->get( 'room_stays' );

		if ( empty( $session_room_stays ) || ! is_array( $session_room_stays ) ) {
			return;
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

		do_action( 'awebooking/reservation/restored', $this );

		// Re-store the session.
		if ( count( $session_room_stays ) !== count( $this->room_stays ) ) {
			$this->store();
			$this->calculate_totals();
		}
	}

	/**
	 * Calculate the totals.
	 *
	 * @return void
	 */
	public function calculate_totals() {
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
	 *
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
