<?php
namespace AweBooking\Model\Pricing;

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
		return esc_html__( 'Base Rate', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_inclusions() {
		return $this->instance->get_meta( '_rate_inclusions' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_policies() {
		return $this->instance->get_meta( '_rate_policies' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_priority() {
		return 0;
	}
}
