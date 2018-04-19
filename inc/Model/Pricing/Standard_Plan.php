<?php
namespace AweBooking\Model\Pricing;

use AweBooking\Reservation\Constraints\Minmax_Days_Constraint;

class Standard_Plan implements Rate_Plan {
	/**
	 * The room-type instance.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $instance;

	/**
	 * The line rates.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $rates;

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
	public function get_name() {
		return $this->instance->get_title();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_private_name() {
		return esc_html__( 'Standard Plan', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_inclusions() {
		return $this->instance->get( 'rate_inclusions' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_policies() {
		return $this->instance->get( 'rate_policies' );
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
		return apply_filters( 'awebooking/standard_plan/get_restrictions', [
			'min_los' => $this->instance->get( 'rate_min_los' ),
			'max_los' => $this->instance->get( 'rate_max_los' ),
		], $this );
	}
}
