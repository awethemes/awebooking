<?php

namespace AweBooking\Reservation;

use WP_Error;
use AweBooking\Model\Pricing\Rate_Plan;
use AweBooking\Reservation\Room_Stay\Room_Rate;
use AweBooking\Support\Collection;

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
	 * The session instance.
	 *
	 * @var \AweBooking\Reservation\Session
	 */
	protected $session;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Session $session The Session instance.
	 */
	public function __construct( Session $session ) {
		$this->session    = $session;
		$this->source     = 'website';
		$this->currency   = abrs_current_currency();
		$this->language   = abrs_running_on_multilanguage() ? awebooking( 'multilingual' )->get_current_language() : '';
		$this->room_stays = new Collection;
	}

	/**
	 * Init the hooks.
	 *
	 * @return void
	 */
	public function init() {
		$this->session->init();
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
	 *
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
	 *
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
	 *
	 * @return $this
	 */
	public function set_current_request( Request $current_request ) {
		$this->current_request = $current_request;

		return $this;
	}

	/**
	 * Add a room stay by given a reservation request.
	 *
	 * @param  \AweBooking\Reservation\Request $request The reservation request.
	 *
	 * @return bool|WP_Error
	 */
	public function add_room_stay( Request $request ) {
		$this->set_current_request( $request );

		$room_rate = $this->validate_room_stay( $request, $request['room_type'], $request['room_type'] );

		if ( is_wp_error( $room_rate ) ) {
			return $room_rate;
		}

		$assign_room = $room_rate->get_assigned_room();

		$room_stay = apply_filters( 'awebooking/reservation/add_room_stay', [
			'request'   => $request,
			'room_rate' => $room_rate,
		]);

		$this->room_stays->put( $assign_room->get_id(), $room_stay );

		$this->calculate_totals();

		do_action( 'awebooking/reservation/added_room_stay', $room_stay, $this );

		return true;
	}

	/**
	 * Validate the room stay before add into list.
	 *
	 * @param \AweBooking\Reservation\Request         $request   The reservation request.
	 * @param \AweBooking\Model\Room_Type|int         $room_type The room type ID or object instance.
	 * @param \AweBooking\Model\Pricing\Rate_Plan|int $rate_plan The rate plan ID or object instance.
	 *
	 * @return \AweBooking\Reservation\Room_Stay\Room_Rate|WP_Error
	 */
	public function validate_room_stay( Request $request, $room_type, $rate_plan ) {
		$room_type = abrs_get_room_type( $room_type );
		if ( ! $room_type || 'trash' === $room_type->get( 'status' ) ) {
			return new WP_Error( 'room_type', esc_html__( 'Invalid room type', 'awebooking' ) );
		}

		$rate_plan = abrs_get_rate_plan( $rate_plan );
		if ( ! $rate_plan || ! $rate_plan instanceof Rate_Plan ) {
			return new WP_Error( 'rate_plan', esc_html__( 'Invalid rate plan', 'awebooking' ) );
		}

		// Create new room rate instance.
		$room_rate = new Room_Rate( $request->get_timespan(), $request->get_guest_counts(), $room_type, $rate_plan );
		$room_rate->setup();

		// Validate bookable of room stay.
		$is_bookable = $room_rate->is_bookable();

		if ( is_wp_error( $is_bookable ) ) {
			return $is_bookable;
		} elseif ( true !== $is_bookable ) {
			return new WP_Error( __( 'Sorry the room you booked is no longer available.', 'awebooking' ) );
		}

		return $room_rate;
	}

	/**
	 * Flush the session data.
	 *
	 * @return void
	 */
	public function flush() {
		$this->room_stays = null;
	}

	public function calculate_totals() {

	}
}
