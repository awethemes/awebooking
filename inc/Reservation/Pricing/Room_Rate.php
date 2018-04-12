<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Model\Room_Type;
use AweBooking\Model\Pricing\Rate;
use AweBooking\Model\Common\Timespan;
use AweBooking\Support\Decimal;
use AweBooking\Support\Collection;

class Room_Rate {
	/**
	 * The price apply for room-type.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * The timespan.
	 *
	 * @var \AweBooking\Model\Common\Timespan
	 */
	protected $timespan;

	/**
	 * The list rates.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $rates;

	/**
	 * The total amount.
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $amount;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Room_Type       $room_type The room type.
	 * @param \AweBooking\Model\Common\Timespan $timespan  The timespan.
	 */
	public function __construct( Room_Type $room_type, Timespan $timespan ) {
		$this->room_type = $room_type;
		$this->timespan = $timespan;
	}

	/**
	 * Get the room-type.
	 *
	 * @return \AweBooking\Model\Room_Type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Get the stay.
	 *
	 * @return \AweBooking\Model\Common\Timespan
	 */
	public function get_timespan() {
		return $this->timespan;
	}

	/**
	 * Gets the total amount.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_amount() {
		if ( is_null( $this->amount ) ) {
			$this->amount = $this->calculate_amount();
		}

		return $this->amount;
	}

	/**
	 * Sets the total amount.
	 *
	 * @param \AweBooking\Support\Decimal $amount The amount.
	 */
	public function custom_amount( $amount ) {
		$this->flush_rates( true );

		$this->amount = Decimal::create( $amount );

		return $this;
	}

	/**
	 * Select a rate.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate $rate The rate.
	 * @return $this
	 */
	public function select( Rate $rate ) {
		$this->flush_rates( true );

		return $this->add_rate( $rate, true );
	}

	/**
	 * Push a rate into the addition_rates.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate $rate The rate.
	 * @return $this
	 */
	public function addition( Rate $rate ) {
		// Need a primary rate before call this action.
		if ( is_null( $this->rates ) ) {
			return $this;
		}

		return $this->add_rate( $rate );
	}

	/**
	 * Flush the selected rates.
	 *
	 * @param  boolean $reset_amount The reset total amount.
	 * @return $this
	 */
	public function flush_rates( $reset_amount = true ) {
		$this->rates = new Collection;

		if ( $reset_amount ) {
			$this->amount = null;
		}

		return $this;
	}

	/**
	 * Add a rate into the list.
	 *
	 * @param  \AweBooking\Model\Pricing\Rate $rate    The rate.
	 * @param  boolean                        $primary Is this is primary rate.
	 * @return $this
	 */
	protected function add_rate( Rate $rate, $primary = false ) {
		// Get the price amount & breakdown.
		list( $amount, $breakdown ) = ( new Pricing )->get( $rate, $this->timespan );

		if ( $primary ) {
			$this->rates->prepend( compact( 'primary', 'rate', 'amount', 'breakdown' ) );
		} else {
			$this->rates->push( compact( 'primary', 'rate', 'amount', 'breakdown' ) );
		}

		return $this;
	}

	/**
	 * Calculate the room rate total.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	protected function calculate_amount() {
		$zero = Decimal::zero();

		if ( is_null( $this->rates ) || $this->rates->isEmpty() ) {
			return $zero;
		}

		$total = $this->rates->reduce( function ( $total, $item ) {
			return $total->add( $item['amount'] );
		}, $zero );

		return apply_filters( 'awebooking/pricing/calculate_room_rate', $total, $this );
	}
}
