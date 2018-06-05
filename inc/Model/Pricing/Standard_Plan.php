<?php
namespace AweBooking\Model\Pricing;

class Standard_Plan implements Rate_Plan {
	use With_Services;

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
		return $this->instance->get( 'title' );
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
	public function get_rates() {
		if ( is_null( $this->rates ) ) {
			// Multiple rates only available in pro version, please upgrade :).
			$rates = apply_filters( 'awebooking/standard_plan/setup_rates', [], $this );

			$this->rates = abrs_collect( $rates )
				->prepend( new Base_Rate( $this->instance ) )
				->filter( function ( $plan ) {
					return $plan instanceof Rate;
				})->sortBy( function( Rate $rate ) {
					return $rate->get_priority();
				})->values();
		}

		return $this->rates;
	}
}
