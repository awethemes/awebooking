<?php
namespace AweBooking\Reservation\Traits;

use AweBooking\Constants;
use AweBooking\Availability\Request;
use AweBooking\Reservation\Item;
use AweBooking\Reservation\Exceptions\PastDateException;
use AweBooking\Reservation\Exceptions\RoomRateException;
use AweBooking\Reservation\Exceptions\FullyBookedException;
use AweBooking\Reservation\Exceptions\NotEnoughRoomsException;

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
	protected $booked_rooms;

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
	 * @param int                              $quantity  The number of room to book.
	 *
	 * @return \AweBooking\Reservation\Item
	 */
	public function add( Request $request, $room_type, $rate_plan = 0, $quantity = 1 ) {
		return $this->add_room_stay( $request, $room_type, $rate_plan, $quantity );
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
	 * Add a room stay into the list.
	 *
	 * @param \AweBooking\Availability\Request $request   The res request instance.
	 * @param int                              $room_type The room type ID.
	 * @param int|null                         $rate_plan The rate ID.
	 * @param int                              $quantity  The number of room to book.
	 *
	 * @return \AweBooking\Reservation\Item
	 */
	public function add_room_stay( Request $request, $room_type, $rate_plan = 0, $quantity = 1 ) {
		$this->set_current_request( $request );

		// Create the room rate, perform check after.
		$room_rate = abrs_retrieve_room_rate( compact( 'request', 'room_type', 'rate_plan' ) );
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

		do_action( 'abrs_room_stay_removed', $removed, $this );

		return $removed;
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

		if ( $room_rate->get_rate()->is_negative() || $room_rate->get_rate()->is_zero() ) {
			throw new RoomRateException( esc_html__( 'Sorry, the room is not available. Please try another room.', 'awebooking' ) );
		}

		if ( $room_rate->has_error() || ! $room_rate->is_visible() ) {
			throw new RoomRateException( esc_html__( 'Sorry, some kind of error has occurred. Please try again.', 'awebooking' ) );
		}

		do_action( 'abrs_check_room_rate', $room_rate, compact( 'quantity' ), $this );
	}
}
