<?php

namespace AweBooking\Model\Pricing;

use AweBooking\Model\Room_Type;
use AweBooking\Model\Common\Timespan;

class Standard_Rate_Interval implements Contracts\Rate_Interval {
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
	public function get_rate_id() {
		return $this->instance->get_id();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return esc_html__( 'Standard Rate', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_rack_rate() {
		return $this->instance->get( 'rack_rate' );
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
		return apply_filters( 'abrs_rate_restrictions', [
			'min_los' => $this->instance->get( 'rate_min_los' ),
			'max_los' => $this->instance->get( 'rate_max_los' ),
		], $this );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_breakdown( Timespan $timespan ) {
		$breakdown = new Breakdown( $timespan, $this->get_rack_rate() );

		$itemized = abrs_retrieve_rate( $this, $timespan );

		return apply_filters( 'abrs_rate_breakdown', $breakdown->merge( $itemized ), $this );
	}
}
