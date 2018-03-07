<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Model\Stay;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Base_Rate;
use AweBooking\Model\Contracts\Rate;
use AweBooking\Model\Contracts\Rate_Plan;
use AweBooking\Support\Decimal;
use AweBooking\Support\Collection;

class Room_Rate {
	/**
	 * The stay date.
	 *
	 * @var \AweBooking\Model\Stay
	 */
	protected $stay;

	/**
	 * The selected room-type.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * The booked rate-plan.
	 *
	 * @var \AweBooking\Model\Contracts\Rate_Plan
	 */
	protected $rate_plan;

	/**
	 * The selected rates.
	 *
	 * @var \AweBooking\Model\Contracts\Rate
	 */
	protected $selected;
	protected $extra;

	/**
	 * The total of selected rates.
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $total;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Stay      $stay       The Stay instance.
	 * @param \AweBooking\Model\Room_Type $room_type  The room-type.
	 */
	public function __construct( Stay $stay, Room_Type $room_type, Rate_Plan $rate_plan = null ) {
		$this->stay = $stay;
		$this->room_type = $room_type;

		if ( is_null( $rate_plan ) ) {
			$rate_plan = new Base_Rate( $room_type );
		}

		$this->use_rate_plan( $rate_plan );
	}

	/**
	 * Get the stay instance.
	 *
	 * @return \AweBooking\Model\Stay
	 */
	public function get_stay() {
		return $this->stay;
	}

	/**
	 * Get the room-type instance.
	 *
	 * @return \AweBooking\Model\Room
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Get the rate plan instance.
	 *
	 * @return \AweBooking\Model\Contracts\Rate_Plan
	 */
	public function get_rate_plan() {
		return $this->rate_plan;
	}

	/**
	 * Use a rate_plan for retrieve the price.
	 *
	 * @param  \AweBooking\Model\Contracts\Rate_Plan $rate_plan The rate plan instance.
	 * @return $this
	 */
	public function use_rate_plan( Rate_Plan $rate_plan ) {
		if ( ! $this->room_type->has_rate_plan( $rate_plan ) ) {
			throw new \InvalidArgumentException;
		}

		$this->rate_plan = $rate_plan;

		$this->selected = null;

		return $this;
	}

	public function select( Rate $rate ) {
	}

	/**
	 * Select a rate.
	 *
	 * @param  \AweBooking\Model\Rate|int $rate   The rate ID or instance.
	 * @param  boolean                    $on_top Add rate on the top.
	 * @return $this
	 */
	public function with( Rate $rate, $on_top = false ) {
		$pricing = new Pricing( $rate, $this->stay );

		$item = compact( 'rate', 'pricing' );

		if ( $on_top ) {
			$this->selected_rates->prepend( $item, $rate->get_id() );
		} else {
			$this->selected_rates->put( $rate->get_id(), $item );
		}

		if ( $this->total ) {
			$this->total = $total->add( $pricing->get_amount() );
		}

		return $this;
	}

	/**
	 * Return the total.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function total() {
		if ( is_null( $this->total ) ) {
			$this->total = $this->calculate_total();
		}

		return $this->total;
	}

	/**
	 * Calculate the total amount of rates.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	protected function calculate_total() {
		return $this->selected_rates->reduce( function( $total, $rate ) {
			return $total->add( $rate['pricing']->get_amount() );
		}, Decimal::zero() );
	}
}
