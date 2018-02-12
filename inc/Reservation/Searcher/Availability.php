<?php
namespace AweBooking\Reservation\Searcher;

use AweBooking\Model\Stay;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Support\Collection;

class Availability {
	/**
	 * The Room_Type model.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * The Stay model.
	 *
	 * @var \AweBooking\Model\Stay
	 */
	protected $stay;

	/**
	 * The room-type rooms.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $rooms;

	/**
	 * The remain rooms.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $includes;

	/**
	 * The excludes room-units.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $excludes;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Room_Type $room_type The Room_Type model.
	 * @param \AweBooking\Model\Stay      $stay      The Stay model.
	 */
	public function __construct( Room_Type $room_type, Stay $stay, $rooms ) {
		$this->stay      = $stay;
		$this->room_type = $room_type;
		$this->rooms     = $rooms;

		$this->includes  = Collection::make();
		$this->excludes  = Collection::make();
	}

	/**
	 * The the stay.
	 *
	 * @return \AweBooking\Model\Stay
	 */
	public function get_stay() {
		return $this->stay;
	}

	/**
	 * Get the room_type model.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Include a room from the availability.
	 *
	 * @param  Room   $room           The room.
	 * @param  string $reason         The reason why added into this.
	 * @param  string $reason_message Optional, the reason message.
	 * @return bool
	 */
	public function include( Room $room, $reason, $reason_message = '' ) {
		$reason_message = $reason_message ?: Reason::get_message( $reason );

		$this->includes->push( compact( 'room', 'reason', 'reason_message' ) );

		return true;
	}

	/**
	 * Exclude a room from the availability.
	 *
	 * @param  Room   $room           The room.
	 * @param  string $reason         The reason why added into this.
	 * @param  string $reason_message Optional, the reason message.
	 * @return bool
	 */
	public function exclude( Room $room, $reason, $reason_message = '' ) {
		$reason_message = $reason_message ?: Reason::get_message( $reason );

		$this->excludes->push( compact( 'room', 'reason', 'reason_message' ) );

		return true;
	}

	/**
	 * Reject a included room from the availability.
	 *
	 * @param  Room   $room           The room.
	 * @param  string $reason         The reason why added into this.
	 * @param  string $reason_message Optional, the reason message.
	 * @return bool
	 */
	public function reject( Room $room, $reason, $reason_message = '' ) {
		if ( $this->remain( $room ) ) {
			$this->includes = $this->includes->reject( function( $item ) use ( $room ) {
				return $item['room']->get_id() === $room->get_id();
			});
		}

		return $this->exclude( $room, $reason, $reason_message );
	}

	/**
	 * Determines if a room-unit contains in remain_rooms.
	 *
	 * @param  \AweBooking\Model\Room $room The room_unit instance.
	 * @return bool
	 */
	public function remain( Room $room ) {
		return $this->remain_rooms()->contains( 'room.id', '==', $room->get_id() );
	}

	/**
	 * Get the remain rooms left.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function remain_rooms() {
		return $this->includes;
	}

	/**
	 * Get the rooms included.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_included() {
		return $this->includes;
	}

	/**
	 * Get the rooms excluded.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_excluded() {
		return $this->excludes;
	}

	/**
	 * Apply the constraints.
	 *
	 * @param  array $constraints Constraint[].
	 * @return void
	 */
	public function apply_constraints( array $constraints ) {
		foreach ( $constraints as $constraint ) {
			if ( is_string( $constraint ) ) {
				awebooking()->call( $constraint, [ $this ], 'apply' );
			} else {
				$constraint->apply( $this );
			}
		}
	}
}
