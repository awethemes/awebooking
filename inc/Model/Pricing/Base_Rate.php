<?php

namespace AweBooking\Model\Pricing;

class Base_Rate implements Contracts\Rate {
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

	public function get_instance() {
		return $this->instance;
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
		return $this->instance->get( 'title' );
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
	public function get_rate_intervals() {
		if ( is_null( $this->rates ) ) {
			$this->rates = abrs_get_rate_intervals( $this )
				->push( abrs_get_standard_rate_interval( $this->instance ) );
		}

		return $this->rates;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_taxable() {
		return abrs_tax_enabled();
	}

	/**
	 * {@inheritdoc}
	 */
	public function price_includes_tax() {
		return abrs_prices_includes_tax();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_tax_rate() {
		return $this->instance->get( 'tax_rate_id' );
	}

	/**
	 * Gets all services of the rate.
	 *
	 * @return array
	 */
	public function get_services() {
		return (array) $this->instance->get( 'rate_services' );
	}
}
