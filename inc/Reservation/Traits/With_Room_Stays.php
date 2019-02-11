<?php

namespace AweBooking\Reservation\Traits;

use AweBooking\Availability\Request;
use AweBooking\Constants;
use AweBooking\Reservation\Exceptions\FullyBookedException;
use AweBooking\Reservation\Exceptions\NotEnoughRoomsException;
use AweBooking\Reservation\Exceptions\PastDateException;
use AweBooking\Reservation\Exceptions\RoomRateException;
use AweBooking\Reservation\Item;
use Illuminate\Support\Arr;

/**
 * Trait With_Room_Stays
 *
 * @property \AweBooking\Reservation\Storage\Store $store
 *
 * @package AweBooking\Reservation\Traits
 */
trait With_Room_Stays {
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
	protected $booked_rooms = [];

	/**
	 * Gets the room stays.
	 *
	 * @return \AweBooking\Support\Collection \AweBooking\Reservation\Item[]
	 */
	public function all() {
		return $this->get_room_stays();
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
	 * Add a room_stay into the list.
	 *
	 * @param \AweBooking\Availability\Request $request   The res request instance.
	 * @param int                              $room_type The room type ID.
	 * @param int|null                         $rate_plan The rate ID.
	 *
	 * @return \AweBooking\Reservation\Item
	 */
	public function add( Request $request, $room_type, $rate_plan = 0 ) {
		return $this->add_room_stay( $request, $room_type, $rate_plan );
	}

	/**
	 * Remove a room_stay from the list.
	 *
	 * @param  string $row_id The room stay row ID.
	 * @return \AweBooking\Reservation\Item|false
	 */
	public function remove( $row_id ) {
		return $this->remove_room_stay( $row_id );
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
	 * Add a room stay into the list.
	 *
	 * @param \AweBooking\Availability\Request $request   The res request instance.
	 * @param int                              $room_type The room type ID.
	 * @param int|null                         $rate_plan The rate ID.
	 * @return \AweBooking\Reservation\Item
	 *
	 * @throws mixed
	 */
	public function add_room_stay( Request $request, $room_type, $rate_plan = 0 ) {
		$room_type = abrs_get_room_type( $room_type );

		// Prevent add a trash room type.
		if ( ! $room_type || 'trash' === $room_type->get( 'status' ) ) {
			throw new \InvalidArgumentException( esc_html__( 'You\'re trying booking an invalid room. Please try another.', 'awebooking' ) );
		}

		// Sets the current request.
		$this->set_current_request( $request );

		// On single mode, we'll clear all room stays was added before.
		if ( abrs_is_reservation_mode( Constants::MODE_SINGLE ) ) {
			$this->room_stays->clear();
			$this->booked_rooms = [];
		}

		// Retrieve the room rate, perform check after.
		$room_rate = abrs_retrieve_room_rate( compact( 'request', 'room_type', 'rate_plan' ) );
		$this->check_room_rate( $room_rate );

		// Generate the row_id.
		$row_id = Item::generate_row_id( $room_type->get_id(),
			$options = array_merge( $request->to_array(), [
				'room_type' => $room_type->get_id(),
				'rate_plan' => $room_rate->get_rate_plan()->get_id(),
			])
		);

		// If room_stay is already in the reservation, update that quantity
		// Otherwise, just new one and put into the list.
		if ( $this->has( $row_id ) ) {
			$room_stay = $this->get( $row_id );
			$room_stay->set( 'quantity', $room_stay->get_quantity() + 1 );
		} else {
			$room_stay = new Item([
				'id'       => $room_type->get_id(),
				'row_id'   => $row_id,
				'name'     => $room_type->get( 'title' ),
				'price'    => $room_rate->get_rate(),
				'options'  => $options,
				'quantity' => 1,
			]);

			$room_stay->associate( $room_type );
			$this->room_stays->put( $row_id, $room_stay );
		}

		// Update the room stay data.
		$room_stay->set_data( $room_rate );
		$this->set_booked_rooms( $room_stay );

		do_action( 'abrs_room_stay_added', $room_stay );

		$this->store();

		return $room_stay;
	}

	/**
	 * Remove a room_stay from the list.
	 *
	 * @param  string $row_id The room stay row ID.
	 * @return \AweBooking\Reservation\Item|false
	 */
	public function remove_room_stay( $row_id ) {
		if ( ! $this->has( $row_id ) ) {
			return false;
		}

		do_action( 'abrs_remove_room_stay', $row_id, $this );

		$removed = $this->room_stays->pull( $row_id );
		unset( $this->booked_rooms[ $removed->get_id() ][ $row_id ] );

		do_action( 'abrs_room_stay_removed', $removed, $this );

		$this->store();

		return $removed;
	}

	/**
	 * Gets flatten IDs of the booked rooms.
	 *
	 * @return array
	 */
	public function get_booked_rooms() {
		return abrs_collect( $this->booked_rooms )->flatten( 2 )->all();
	}

	/**
	 * Take the room IDs and store it for temporary lock.
	 *
	 * @param \AweBooking\Reservation\Item $room_stay The room stay instance.
	 * @return void
	 */
	protected function set_booked_rooms( Item $room_stay ) {
		$this->booked_rooms[ $room_stay->get_id() ][ $room_stay->get_row_id() ] = $room_stay->data()
			->get_remain_rooms()
			->take( $room_stay->get_quantity() )
			->pluck( 'resource.id' )
			->all();
	}

	/**
	 * Perform validate the room rate.
	 *
	 * @param \AweBooking\Availability\Room_Rate|mixed $room_rate The room rate.
	 * @param int|null                                 $quantity  Optional, check with quantity.
	 *
	 * @return void
	 *
	 * @throws \AweBooking\Reservation\Exceptions\RoomRateException
	 * @throws \AweBooking\Reservation\Exceptions\PastDateException
	 * @throws \AweBooking\Reservation\Exceptions\FullyBookedException
	 * @throws \AweBooking\Reservation\Exceptions\NotEnoughRoomsException
	 */
	protected function check_room_rate( $room_rate, $quantity = null ) {
		if ( ! $room_rate || is_wp_error( $room_rate ) ) {
			throw new RoomRateException( esc_html__( 'We are unable to find the room rate match with your request. Please try again.', 'awebooking' ) );
		}

		$timespan = $room_rate->get_timespan();
		$timespan->requires_minimum_nights( 1 );

		if ( abrs_date( $timespan->get_start_date() )->lt( abrs_date( 'today' ) ) ) {
			throw new PastDateException( esc_html__( 'You cannot perform reservation in the past! Please re-enter dates.', 'awebooking' ) );
		}

		if ( 0 === count( $remaining = $room_rate->get_remain_rooms() ) ) {
			throw new FullyBookedException( esc_html__( 'Sorry, the room is fully booked, please try another date.', 'awebooking' ) );
		}

		if ( ! is_null( $quantity ) && count( $remaining ) < $quantity ) {
			/* translators: Number of remaining rooms */
			throw new NotEnoughRoomsException( sprintf( esc_html__( 'You cannot book that number of rooms because there are not enough rooms (%1$s remaining)', 'awebooking' ), count( $remaining ) ) );
		}

		if ( $room_rate->get_rate() <= 0 ) {
			throw new RoomRateException( esc_html__( 'Sorry, the room is not available. Please try another room.', 'awebooking' ) );
		}

		if ( ! $room_rate->is_visible() ) {
			throw new RoomRateException( esc_html__( 'Sorry, some kind of error has occurred. Please try again.', 'awebooking' ) );
		}

		do_action( 'abrs_check_room_rate', $room_rate, compact( 'quantity' ), $this );
	}

	/**
	 * Restore the reservation from its saved state.
	 *
	 * @return void
	 */
	protected function restore_rooms() {
		if ( is_null( $this->previous_request ) ) {
			return;
		}

		$session_room_stays = $this->store->get( 'room_stays' );
		if ( empty( $session_room_stays ) || ! is_array( $session_room_stays ) ) {
			return;
		}

		// Resolve the booked_rooms.
		$this->booked_rooms = (array) $this->store->get( 'booked_rooms' );

		if ( abrs_is_reservation_mode( Constants::MODE_SINGLE ) ) {
			$session_room_stays = [ Arr::last( $session_room_stays ) ];
		}

		// Prime caches to reduce future queries.
		if ( function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( array_keys( wp_list_pluck( $session_room_stays, 'id' ) ) );
		}

		do_action( 'abrs_prepare_restore_room_stays', $this );

		// Perform filter valid room stays.
		foreach ( $session_room_stays as $values ) {
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
			$res_request = abrs_create_res_request(
				$args = $room_stay->get_options()->all()
			);

			$room_rate = abrs_retrieve_room_rate( $args + [ 'request' => $res_request ] );

			try {
				$this->check_room_rate( $room_rate, $room_stay->get_quantity() );
			} catch ( \Exception $e ) {
				continue;
			}

			$room_stay->set( 'price', $room_rate->get_rate() );
			$room_stay->set_data( $room_rate );

			// Put the room stay into the list.
			$this->room_stays->put( $room_stay->get_row_id(), $room_stay );
		}

		do_action( 'abrs_room_stays_restored', $this );

		// Re-store the session.
		if ( count( $session_room_stays ) !== count( $this->room_stays ) ) {
			$this->store();
		}
	}
}
