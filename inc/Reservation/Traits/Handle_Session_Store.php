<?php
namespace AweBooking\Reservation\Traits;

use AweBooking\Constants;
use AweBooking\Reservation\Item;
use AweBooking\Availability\Request;
use Illuminate\Support\Arr;

/**
 * Trait Handle_Session_Store
 *
 * @property \AweBooking\Support\Collection $room_stays
 * @property array                          $booked_rooms
 *
 * @package AweBooking\Reservation\Traits
 */
trait Handle_Session_Store {
	/**
	 * The store instance.
	 *
	 * @var \AweBooking\Reservation\Storage\Store
	 */
	protected $store;

	/**
	 * The current res request.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	protected $current_request;

	/**
	 * The previous res request.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	protected $previous_request;

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
	 * @return $this
	 */
	public function set_current_request( Request $current_request ) {
		$this->current_request = $current_request;

		if ( $this->maybe_flush() ) {
			$this->flush();
		}

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
	 * Flush the session data.
	 *
	 * @return void
	 */
	public function flush() {
		$this->room_stays->clear();
		$this->booked_rooms     = [];
		$this->current_request  = null;
		$this->previous_request = null;

		$this->store->flush( 'room_stays' );
		$this->store->flush( 'booked_rooms' );
		$this->store->flush( 'previous_request' );

		if ( abrs_running_on_multilanguage() ) {
			$this->store->flush( 'reservation_language' );
		}

		do_action( 'abrs_reservation_emptied', $this );
	}

	/**
	 * Is need flush session data.
	 *
	 * @return bool
	 */
	public function maybe_flush() {
		// Flush when session request & current request is different.
		$previous_request = $this->get_previous_request();

		if ( $previous_request && ! $this->current_request->same_with( $previous_request ) ) {
			return true;
		}

		if ( abrs_running_on_multilanguage() && abrs_multilingual()->get_current_language() !== $this->language ) {
			return true;
		}

		return false;
	}

	/**
	 * Save the reservation state.
	 *
	 * @return void
	 */
	public function store() {
		if ( $this->is_empty() ) {
			$this->flush();
			return;
		}

		if ( $this->current_request ) {
			$this->store->put( 'previous_request', $this->current_request );
		}

		$this->store->put( 'room_stays', $this->room_stays->to_array() );
		$this->store->put( 'booked_rooms', $this->booked_rooms );

		do_action( 'abrs_reservation_stored', $this );
	}

	/**
	 * Restore the res request from the store.
	 *
	 * @return void
	 */
	protected function restore_request() {
		$previous_request = $this->store->get( 'previous_request' );

		if ( ! $previous_request || ! $previous_request instanceof Request ) {
			return;
		}

		$this->previous_request = $previous_request;

		do_action( 'abrs_res_request_restored', $this );
	}

	/**
	 * Restore the reservation from its saved state.
	 *
	 * @return void
	 */
	public function restore_rooms() {
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
			$room_rate = abrs_retrieve_room_rate( $room_stay->get_options()->all() );

			try {
				$this->check_room_rate( $room_rate, $room_stay->get_quantity() );
			} catch ( \Exception $e ) {
				continue;
			}

			$room_stay->set( 'price', $room_rate->get_rate() );
			$room_stay->set_data( $room_rate );

			// Put the room stay into the list.
			$this->room_stays->put( $row_id, $room_stay );
		}

		do_action( 'abrs_room_stays_restored', $this );

		// Re-store the session.
		if ( count( $session_room_stays ) !== count( $this->room_stays ) ) {
			$this->store();
		}
	}
}
