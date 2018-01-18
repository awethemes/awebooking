<?php
namespace AweBooking\Model\Traits\Room_Type;

use AweBooking\Factory;
use AweBooking\Constants;
use AweBooking\Pricing\Rate;
use AweBooking\Pricing\Price;
use AweBooking\Support\Collection;

trait Room_Rates_Trait {

	/**
	 * Get base price.
	 *
	 * @return Price
	 */
	public function get_base_price() {
		return apply_filters( $this->prefix( 'get_base_price' ), new Price( $this['base_price'] ), $this );
	}

	/**
	 * Get minimum nights.
	 *
	 * @return int
	 */
	public function get_minimum_night() {
		return apply_filters( $this->prefix( 'get_minimum_night' ), $this['minimum_night'], $this );
	}

	/**
	 * Get collection of rates.
	 *
	 * @return Collection
	 */
	public function get_rates() {
		return Collection::make( get_children([
			'post_parent' => $this->get_id(),
			'post_type'   => Constants::PRICING_RATE,
			'orderby'     => 'menu_order',
			'order'       => 'ASC',
		]))->map(function( $post ) {
			return new Rate( $post->ID, $this );
		})->prepend(
			$this->get_standard_rate()
		);
	}

	/**
	 * Get standard rate.
	 *
	 * @return Rate
	 */
	public function get_standard_rate() {
		return new Rate( $this->get_id(), $this );
	}
}
