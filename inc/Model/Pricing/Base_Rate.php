<?php
namespace AweBooking\Model\Pricing;

use AweBooking\Model\Room_Type;

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
	 * @param \AweBooking\Model\Room_Type $room_type The room type instance.
	 */
	public function __construct( Room_Type $room_type ) {
		$this->instance = $room_type;
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
	 * {@inheritdoc}
	 */
	public function get_effective_date() {
		return '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_expires_date() {
		return '';
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
	public function get_restrictions() {
		return apply_filters( 'awebooking/rate/get_restrictions', [
			'min_los' => $this->instance->get( 'rate_min_los' ),
			'max_los' => $this->instance->get( 'rate_max_los' ),
		], $this );
	}
}
