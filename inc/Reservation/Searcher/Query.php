<?php
namespace AweBooking\Reservation\Searcher;

use AweBooking\Factory;
use AweBooking\Model\Stay;
use AweBooking\Model\Guest;
use AweBooking\Model\Room_Type;
use AweBooking\Support\Utils as U;

class Query {
	/**
	 * The Stay instance.
	 *
	 * @var \AweBooking\Model\Stay
	 */
	protected $stay;

	/**
	 * The Guest instance.
	 *
	 * @var \AweBooking\Model\Guest
	 */
	protected $guest;

	/**
	 * The constraints to apply to search.
	 *
	 * @var array
	 */
	protected $constraints = [];

	/**
	 * Constructor.
	 *
	 * @param Stay  $stay        The Stay instance.
	 * @param Guest $guest       The Guest instance.
	 * @param array $constraints The constraints.
	 */
	public function __construct( Stay $stay, Guest $guest = null, array $constraints = [] ) {
		$this->stay = $stay;
		$this->guest = $guest;
		$this->constraints = $constraints;
	}

	/**
	 * Get the availability results.
	 *
	 * @return \AweBooking\Reservation\Searcher\Results
	 */
	public function get() {
		$room_type = $this->query_rooms_types();

		$results = new Results;
		$checker = new Checker;

		foreach ( $room_type as $room_type ) {
			$availability = $checker->check( $room_type, $this->stay, $this->constraints );

			$results->push( $availability );
		}

		return $results;
	}

	/**
	 * Get the Stay.
	 *
	 * @return \AweBooking\Model\Stay
	 */
	public function get_stay() {
		return $this->stay;
	}

	/**
	 * Set the Stay.
	 *
	 * @param Stay $stay The Stay instance.
	 */
	public function set_stay( Stay $stay ) {
		$this->stay = $stay;

		return $this;
	}

	/**
	 * Get the Guest.
	 *
	 * @return \AweBooking\Model\Guest
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
	 * Apply constraints.
	 *
	 * @param  array $constraints Constraint_Interface[].
	 * @return $this
	 */
	public function set_constraints( array $constraints ) {
		$this->constraints = $constraints;

		return $this;
	}

	/**
	 * Query the room types available for searching.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	protected function query_rooms_types() {
		$args = apply_filters( 'awebooking/query_room_type_args', [
			'post_status'      => 'publish',
			'posts_per_page'   => 1000, // Force limit 1000 room-type in query.
			'booking_adults'   => $this->guest ? $this->guest->get_adults() : -1,
			'booking_children' => $this->guest ? $this->guest->get_children() : -1,
			'booking_infants'  => $this->guest ? $this->guest->get_infants() : -1,
		], $this );

		// Query the room_typess.
		$room_types = Room_Type::query( $args )->posts;

		return U::collect( $room_types )->map( function( $post ) {
			return Factory::get_room_type( $post );
		});
	}
}
