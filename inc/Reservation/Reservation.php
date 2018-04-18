<?php
namespace AweBooking\Reservation;

use AweBooking\Support\Collection;

class Reservation {
	/**
	 * The reservation source.
	 *
	 * @var string
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
	 * @var \AweBooking\Support\Collection
	 */
	protected $room_stays;

	/**
	 * The current request.
	 *
	 * @var \AweBooking\Reservation\Request
	 */
	protected $last_request;

	/**
	 * Create new reservation.
	 *
	 * @param string   $source   The source implementation.
	 * @param currency $currency The currency code.
	 */
	public function __construct( $source = 'website', $currency = null ) {
		$this->source = $source;

		$this->currency = is_null( $currency ) ? abrs_current_currency() : $currency;

		$this->room_stays = new Collection;
	}

	/**
	 * Get the source.
	 *
	 * @return string
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
	 * @param  string $currency The ISO code currency.
	 * @return $this
	 */
	public function set_currency( $currency ) {
		$this->currency = $currency;

		return $this;
	}

	/**
	 * Gets the current request.
	 *
	 * @return \AweBooking\Reservation\Request
	 */
	public function get_last_request() {
		return $this->last_request;
	}

	/**
	 * Sets the current request.
	 *
	 * @param  \AweBooking\Reservation\Request $last_request The request instance.
	 * @return $this
	 */
	public function set_last_request( Request $last_request ) {
		$this->last_request = $last_request;

		return $this;
	}
}
