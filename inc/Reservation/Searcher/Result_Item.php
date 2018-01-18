<?php
namespace AweBooking\Reservation\Searcher;

use AweBooking\Model\Room_Type;
use AweBooking\Concierge\Availability\Availability;

class Result_Item {
	/**
	 * The room_type instance.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * The availability instance.
	 *
	 * @var \AweBooking\Concierge\Availability\Availability
	 */
	protected $availability;

	/**
	 * Search result item.
	 *
	 * @param Room_Type    $room_type    The room_type.
	 * @param Availability $availability The availability.
	 */
	public function __construct( Room_Type $room_type, Availability $availability ) {
		$this->room_type = $room_type;
		$this->availability = $availability;
	}

	/**
	 * Get the room_type.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Get the availability.
	 *
	 * @return \AweBooking\Concierge\Availability\Availability
	 */
	public function get_availability() {
		return $this->availability;
	}

	/**
	 * Magic getter method.
	 *
	 * @param  string $property The property name.
	 * @return mixed
	 */
	public function __get( $property ) {
		if ( method_exists( $this, $method = "get_{$property}" ) ) {
			return $this->{$method}();
		}
	}
}
