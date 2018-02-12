<?php
namespace AweBooking\Reservation;

use AweBooking\Model\Stay;
use AweBooking\Model\Room;
use AweBooking\Model\Rate;
use AweBooking\Model\Guest;
use AweBooking\Reservation\Pricing\Pricing;

class Item {
	/**
	 * The room will be add to the reservation.
	 *
	 * @var \AweBooking\Model\Room
	 */
	protected $room;

	/**
	 * The rate (pricing) for the this room.
	 *
	 * @var \AweBooking\Model\Rate
	 */
	protected $rate;

	/**
	 * The stay period for the this room.
	 *
	 * @var \AweBooking\Model\Rate
	 */
	protected $stay;

	/**
	 * The number of guest stay in this room.
	 *
	 * @var \AweBooking\Model\Guest
	 */
	protected $guest;

	/**
	 * Cache the pricing results.
	 *
	 * @var \AweBooking\Reservation\Pricing\Pricing
	 */
	protected $pricing;

	/**
	 * Create new reservation item (represent for a booking room).
	 *
	 * @param Room  $room  The room instance.
	 * @param Rate  $rate  The rate instance.
	 * @param Stay  $stay  The stay instance.
	 * @param Guest $guest The guest instance.
	 */
	public function __construct( Room $room, Rate $rate, Stay $stay, Guest $guest ) {
		$this->room  = $room;
		$this->rate  = $rate;
		$this->stay  = $stay;
		$this->guest = $guest;
	}

	/**
	 * Get the pricing.
	 *
	 * @return \AweBooking\Reservation\Pricing\Pricing
	 */
	public function get_pricing() {
		if ( is_null( $this->pricing ) ) {
			$this->pricing = new Pricing( $this->rate, $this->stay );
		}

		return $this->pricing;
	}

	/**
	 * Get the room instance.
	 *
	 * @return \AweBooking\Model\Room
	 */
	public function get_room() {
		return $this->room;
	}

	/**
	 * Set the rate.
	 *
	 * @return \AweBooking\Model\Rate
	 */
	public function get_rate() {
		return $this->rate;
	}

	/**
	 * Set the stay.
	 *
	 * @return \AweBooking\Model\Stay
	 */
	public function get_stay() {
		return $this->stay;
	}

	/**
	 * Get the guest instance.
	 *
	 * @return \AweBooking\Model\Guest
	 */
	public function get_guest() {
		return $this->guest;
	}

	/**
	 * Get the display label.
	 *
	 * @return string
	 */
	public function get_label() {
		$room_type = $this->room->get_room_type();

		return sprintf( /* translators: 1: Room type name. 2: Room unit name */
			esc_html_x( '%1$s (%2$s)', 'room item label', 'awebooking' ),
			esc_html( $room_type->get_title() ),
			esc_html( $this->room->get_name() )
		);
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
