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

	public function add_room_stay( $room_type, $rate_plan = null, Request $request = null ) {
		if ( is_null( $request ) && is_null( $this->current_request ) ) {
			throw new \Exception( 'Error Processing Request' );
		}

		// Resolve the reservation request.
		if ( is_null( $request ) ) {
			$request = $this->get_current_request();
		} else {
			$this->set_current_request( $request );
		}

		// Create the room rate.
		$room_rate = new Room_Rate( $request->get_timespan(), $request->get_guest_counts(), $room_type, $rate_plan );
		$room_rate->set_request( $request );

		$remain_rooms = $room_rate->get_remain_rooms();

		if ( 0 === count( $remain_rooms ) ) {
			return new WP_Error( 'no_room_left', esc_html__( 'No room left', 'awebooking' ) );
		}

		$rate = $room_rate->get_price( 'total' );
		if ( $rate <= 0 ) {
			return new WP_Error( 'rate_error', esc_html__( 'Rate Error', 'awebooking' ) );
		}

		$room = $remain_rooms->first()['resource'];
		$room_rate->assign( $room );

		$this->room_stays->put( $room->get_id(), $room_rate );

		$this->session->put( 'reservation', $this->room_stays );

		return true;
	}
}
