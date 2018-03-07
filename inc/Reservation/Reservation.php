<?php
namespace AweBooking\Reservation;

use AweBooking\Assert;
use AweBooking\Factory;
use AweBooking\Constants;
use AweBooking\Model\Stay;
use AweBooking\Model\Room;
use AweBooking\Model\Rate;
use AweBooking\Model\Guest;
use AweBooking\Model\Source;
use AweBooking\Model\Room_Type;
use AweBooking\Support\Collection;
use AweBooking\Reservation\Pricing\Pricing;
use AweBooking\Reservation\Searcher\Query;
use AweBooking\Reservation\Searcher\Checker;

class Reservation {
	/**
	 * The request source.
	 *
	 * @var \AweBooking\Model\Source
	 */
	protected $source;

	/**
	 * The room stays.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $room_stays;

	/**
	 * The selected services.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $services;

	/**
	 * The deposit amount.
	 *
	 * @var \AweBooking\Model\Deposit
	 */
	protected $deposit;

	/**
	 * The totals.
	 *
	 * @var \AweBooking\Reservation\Totals
	 */
	protected $totals;

	/**
	 * The reservation currency.
	 *
	 * @var \AweBooking\Money\Currency
	 */
	protected $currency;

	protected $language;

	/**
	 * The reservation session ID.
	 *
	 * @var string
	 */
	protected $session_id;

	/**
	 * Create new reservation.
	 *
	 * @param \AweBooking\Model\Source $source The source implementation.
	 */
	public function __construct( Source $source ) {
		$this->source = $source;
		$this->rooms = new Collection;
		$this->totals = new Totals( $this );
	}

	/**
	 * Search the availability.
	 *
	 * @param  \AweBooking\Model\Stay  $stay        The stay for the reservation.
	 * @param  \AweBooking\Model\Guest $guest       Optional, the guest for the reservation.
	 * @param  array                   $constraints The constraints.
	 * @return \AweBooking\Reservation\Searcher\Results
	 */
	public function search( Stay $stay, Guest $guest = null, array $constraints = [] ) {
		$constraints = array_merge( $constraints, [
			new Searcher\Constraints\Session_Reservation_Constraint( $this ),
		]);

		return ( new Query( $stay, $guest, $constraints ) )->get();
	}

	/**
	 * Get the Source.
	 *
	 * @return \AweBooking\Model\Source
	 */
	public function get_source() {
		return $this->source;
	}

	public function set_source() {
	}

	/**
	 * Get the Guest.
	 *
	 * @return \AweBooking\Model\Guest|null
	 */
	public function get_guest() {
		return $this->guest;
	}

	/**
	 * Set the Guest.
	 *
	 * @param Guest $guest The Guest instance.
	 */
	public function set_guest( Guest $guest ) {
		$this->guest = $guest;

		return $this;
	}

	/**
	 * Get the deposit.
	 *
	 * @return \AweBooking\Model\Deposit|null
	 */
	public function get_deposit() {
		return $this->deposit;
	}

	/**
	 * Set the deposit.
	 *
	 * @param  \AweBooking\Model\Deposit $deposit The deposit instance.
	 * @return $this
	 */
	public function set_deposit( Deposit $deposit ) {
		$this->deposit = $deposit;

		return $this;
	}

	/**
	 * Get the totals.
	 *
	 * @return \AweBooking\Reservation\Totals
	 */
	public function totals() {
		return $this->get_totals();
	}

	/**
	 * Get the totals.
	 *
	 * @return \AweBooking\Reservation\Totals
	 */
	public function get_totals() {
		return $this->totals;
	}

	/**
	 * Get all reservation rooms.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_rooms() {
		return $this->rooms;
	}

	/**
	 * Determines a reservation-room exists in current reservation.
	 *
	 * @param  \AweBooking\Model\Room $room The room instance.
	 * @return boolean
	 */
	public function has_room( Room $room ) {
		return $this->rooms->has( $room->get_id() );
	}

	/**
	 * Get reservation-room by given a room-unit.
	 *
	 * @param  \AweBooking\Model\Room $room The room instance.
	 * @return \AweBooking\Reservation\Room_Stay
	 */
	public function get_room( Room $room ) {
		return $this->rooms->get( $room->get_id() );
	}

	/**
	 * Add a reservation-room to the reservation.
	 *
	 * @param \AweBooking\Model\Room  $room  The room instance.
	 * @param \AweBooking\Model\Rate  $rate  The rate instance.
	 * @param \AweBooking\Model\Guest $guest The guest instance.
	 */
	public function add_room( Room $room, Rate $rate, Guest $guest ) {
		// First, we will check the valid number of guest.
		Assert::guest_number( $guest, $room_type = $this->resolve_room_type( $room ) );

		// Next, check the current room can be bookable.
		$this->validate_bookable( $room, $this->stay );

		// Everything OK.
		$item = new Room_Stay( $room, $rate, $this->stay, $guest );

		$this->rooms->put( $room->get_id(), $item );

		return $item;
	}

	/**
	 * Resolve room type by given room-unit.
	 *
	 * @param  Room $room The room-unit instance.
	 * @return \AweBooking\Model\Room_Type
	 */
	protected function resolve_room_type( Room $room ) {
		return Factory::get_room_type( $room->get_room_type_id() );
	}

	/**
	 * Set the reservation session ID.
	 *
	 * @param string $session_id The session ID.
	 */
	public function set_session_id( $session_id ) {
		$this->session_id = $session_id;
	}

	/**
	 * Get the reservation session ID.
	 *
	 * @return string
	 */
	public function get_session_id() {
		return $this->session_id;
	}

	public function get_context() {
		$overflow_adults = max( 0, $this->request->get_adults() - $this->room_type->get_number_adults() );
		$overflow_children = max( 0, $this->request->get_children() - $this->room_type->get_number_children() );

		return [
			'booking_date'      => Carbonate::today(),
			'check_in_date'     => $this->period->get_start_date(),
			'check_out_date'    => $this->period->get_end_date(),
			'booking_before'    => Carbonate::today()->diffInDays( $this->period->get_start_date() ),
			'check_in'          => $this->period->get_start_date()->dayOfWeek,
			'check_out'         => $this->period->get_end_date()->dayOfWeek,
			'stay_nights'       => $this->request->get_nights(),
			'number_adults'     => $this->request->get_adults(),
			'number_children'   => $this->request->get_children(),
			'number_people'     => $this->request->get_people(),
			'overflow_adults'   => $overflow_adults,
			'overflow_children' => $overflow_children,
			'overflow_people'   => $overflow_adults + $overflow_children,
		];
	}

	/**
	 * Check if this room can be for booking.
	 *
	 * @param  Room $room The room instance.
	 * @param  Stay $stay The stay instance.
	 *
	 * @throws Exceptions\No_Room_Left_Exception
	 * @throws Exceptions\Duplicate_Room_Exception
	 */
	protected function validate_bookable( Room $room, Stay $stay ) {
		if ( $this->has_room( $room ) ) {
			throw new Exceptions\Duplicate_Room_Exception( esc_html__( 'A room already exists in current reservation', 'awebooking' ) );
		}

		// Check the availability state.
		$checker = new Checker;

		if ( ! $checker->is_available_for( $room, $stay ) ) {
			throw new Exceptions\No_Room_Left_Exception( esc_html__( 'No room left for the reservation', 'awebooking' ) );
		}
	}
}
