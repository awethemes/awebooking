<?php
namespace AweBooking\Reservation\Pricing;

use AweBooking\Model\Stay;
use AweBooking\Support\Decimal;

class Pricing implements Pricing_Interface {
	/**
	 * The Stay instance.
	 *
	 * @var \AweBooking\Model\Stay
	 */
	protected $stay;

	/**
	 * The amount.
	 *
	 * @var \AweBooking\Support\Decimal
	 */
	protected $amount;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Stay $stay   The stay.
	 * @param float|int              $amount The amount.
	 */
	public function __construct( Stay $stay, $amount ) {
		$this->stay = $stay;
		$this->set_amount( $amount );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_stay() {
		return $this->stay;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_stay( Stay $stay ) {
		$this->stay = $stay;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_amount( $amount ) {
		$this->amount = Decimal::create( $amount );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_breakdown() {
		$breakdown = new Breakdown;

		$average = $this->amount->multiply(
			$this->stay->nights()
		);

		foreach ( $this->stay->to_period() as $night ) {
			$breakdown->put( $night->format( 'Y-m-d' ), new Night( $night, $average ) );
		}

		return $breakdown;
	}
}
