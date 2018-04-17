<?php
namespace AweBooking\Model\Pricing;

use AweBooking\Ruler\Rule;
use AweBooking\Support\Fluent;
use AweBooking\Support\Decimal;
use AweBooking\Reservation\Request;

class Base_Rate implements Rate {
	/**
	 * The room-type instance.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $instance;

	/**
	 * Create base-rate instance from a room-type.
	 *
	 * @param mixed $instance The room-type ID or instance.
	 */
	public function __construct( $instance ) {
		$this->instance = abrs_get_room_type( $instance );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_id() {
		return $this->instance->get_id();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_parent_id() {
		return $this->instance->get_id();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return $this->instance->get_title();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_rack_rate() {
		return abrs_decimal( $this->instance['rack_rate'] );
	}

	/**
	 * Get the effective_date.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_effective_date() {
		return;
	}

	/**
	 * Get the expire_date.
	 *
	 * @return \AweBooking\Support\Carbonate
	 */
	public function get_expire_date() {
		return;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_priority() {
		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function apply( Request $request ) {
		return true;
	}
}
