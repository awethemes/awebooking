<?php
namespace AweBooking\Reservation;

use AweBooking\Support\Collection;
use AweBooking\Model\Source;
use AweBooking\Model\Common\Deposit;
use AweBooking\Model\Common\Timespan;
use AweBooking\Model\Common\Guest_Counts;

class Reservation {
	/**
	 * The reservation source.
	 *
	 * @var \AweBooking\Model\Source
	 */
	protected $source;

	/**
	 * ISO currency code.
	 *
	 * @var string
	 */
	protected $currency;

	/**
	 * The list of room stays.
	 *
	 * @var \AweBooking\Reservation\Room_Stays
	 */
	protected $room_stays;

	/**
	 * The list of services.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $services;

	/**
	 * The deposit amount.
	 *
	 * @var \AweBooking\Model\Deposit
	 */
	protected $deposit;

	/**
	 * The totals.
	 *
	 * @var \AweBooking\Reservation\Totals
	 */
	protected $totals;

	/**
	 * The reservation session ID.
	 *
	 * @var string|null
	 */
	protected $session_id;

	/**
	 * The current request.
	 *
	 * @var \AweBooking\Reservation\Request
	 */
	protected $current_request;

	/**
	 * Create new reservation.
	 *
	 * @param \AweBooking\Model\Source $source The source implementation.
	 */
	public function __construct( Source $source, $currency = null ) {
		$this->source = $source;

		$this->set_currency( $currency );
		// $this->set_language( $language );

		$this->room_stays = new Room_Stays;
	}

	/**
	 * Get the source.
	 *
	 * @return \AweBooking\Model\Source
	 */
	public function get_source() {
		return $this->source;
	}

	/**
	 * Gets the ISO currency code.
	 *
	 * @return string
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * Sets the ISO currency code.
	 *
	 * @param string $currency The ISO code currency.
	 */
	public function set_currency( $currency ) {
		if ( empty( $currency ) ) {
			$this->currency = awebooking()->get_current_currency();
		} else {
			$this->currency = ( $currency instanceof Currency ) ? $currency->get_code() : $currency;
		}
	}

	/**
	 * Gets the current request.
	 *
	 * @return \AweBooking\Reservation\Request
	 */
	public function get_current_request() {
		return $this->current_request;
	}

	/**
	 * Sets the current request.
	 *
	 * @param  \AweBooking\Reservation\Request $current_request The request instance.
	 * @return $this
	 */
	public function set_current_request( Request $current_request ) {
		$this->current_request = $current_request;

		return $this;
	}

	/**
	 * Get the deposit.
	 *
	 * @return \AweBooking\Model\Deposit|null
	 */
	public function get_deposit() {
		return $this->deposit;
	}

	/**
	 * Set the deposit.
	 *
	 * @param  \AweBooking\Model\Deposit $deposit The deposit instance.
	 * @return $this
	 */
	public function set_deposit( Deposit $deposit ) {
		$this->deposit = $deposit;

		return $this;
	}

	/**
	 * Return the totals.
	 *
	 * @return \AweBooking\Reservation\Totals
	 */
	public function totals() {
		return $this->get_totals();
	}

	/**
	 * Get the totals.
	 *
	 * @return \AweBooking\Reservation\Totals
	 */
	public function get_totals() {
		if ( is_null( $this->totals ) ) {
			$this->totals = new Totals( $this );
		}

		return $this->totals;
	}

	public function get_room_stays() {
		return $this->room_stays;
	}

	public function get_room_stay( $room ) {
	}
}
