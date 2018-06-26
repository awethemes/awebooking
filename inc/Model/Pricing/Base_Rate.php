<?php
namespace AweBooking\Model\Pricing;

class Base_Rate implements Contracts\Rate {
	use Traits\Has_Services;

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
		return 0;
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
		return esc_html__( 'Base Single_Rate', 'awebooking' );
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
	public function get_single_rates() {
		if ( is_null( $this->rates ) ) {
			$this->rates = abrs_query_single_rates( $this )
				->prepend( abrs_get_base_single_rate( $this->instance ) );
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
}
