<?php
namespace AweBooking\Reservation;

use AweBooking\Support\Collection;
use Awethemes\WP_Session\WP_Session;

class Reservation {
	/**
	 * The reservation source.
	 *
	 * @var string
	 */
	public $source;

	/**
	 * ISO currency code.
	 *
	 * @var string
	 */
	public $currency;

	/**
	 * The session instance.
	 *
	 * @var \Awethemes\WP_Session\WP_Session
	 */
	protected $session;

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
	protected $current_request;

	/**
	 * Constructor.
	 *
	 * @param \Awethemes\WP_Session\WP_Session $session The WP_Session class instance.
	 */
	public function __construct( WP_Session $session ) {
		$this->session    = $session;
		$this->room_stays = new Collection;

		$this->source     = apply_filters( 'awebooking/default_reservation_source', 'website' );
		$this->currency   = abrs_current_currency();
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
	 * Sets the source.
	 *
	 * @param  string $source The reservation source.
	 * @return $this
	 */
	public function set_source( $source ) {
		$this->source = $source;

		return $this;
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
	 * Add a room_stay into the reservation.
	 *
	 * @param \AweBooking\Reservation\Room_Stay $room_stay The room_stay implementation.
	 */
	public function add_room_stay( Room_Stay $room_stay ) {
		$this->room_stays->put( $room_stay );
	}
}
