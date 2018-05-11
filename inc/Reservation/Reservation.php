<?php
namespace AweBooking\Reservation;

use WP_Error;
use AweBooking\Support\Collection;
use Awethemes\WP_Session\WP_Session;
use AweBooking\Reservation\Room_Stay\Room_Rate;

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
	 * ISO language code.
	 *
	 * @var string
	 */
	public $language;

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
		$this->language   = abrs_running_on_multilanguage() ? awebooking( 'multilingual' )->get_current_language() : '';
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
	 * Add a room stay by given a reservation request.
	 *
	 * @param  \AweBooking\Reservation\Request $request  The reservation request.
	 * @return bool|WP_Error
	 */
	public function add_room_stay( Request $request ) {
		$this->set_current_request( $request );

		// Get the room type.
		$room_type = abrs_get_room_type( absint( $request['room_type'] ) );

		$rate_plan = is_null( $request['room_type'] )
			? $room_type->get_standard_plan()
			: abrs_get_rate_plan( $request['room_type'] );

		// Create the room rate.
		$room_rate = new Room_Rate( $request->get_timespan(), $request->get_guest_counts(), $room_type, $rate_plan );

		dd( $room_rate );

		$constraints = [];

		$room_rate->set_constraints( $constraints );
		$room_rate->setup();

		// $remain_rooms = $room_rate->get_remain_rooms();

		// $this->room_stays->put( null, $room_rate );

		dd( $this );

		return true;
	}

	public function flush() {
	}
}
