<?php

namespace AweBooking\Reservation;

use AweBooking\Support\Collection;
use AweBooking\Availability\Request;
use AweBooking\Reservation\Storage\Store;

class Reservation {
	use Traits\With_Room_Stays,
		Traits\With_Services,
		Traits\With_Fees;

	/**
	 * The reservation hotel ID.
	 *
	 * @var int
	 */
	public $hotel;

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
	 * The store instance.
	 *
	 * @var \AweBooking\Reservation\Storage\Store
	 */
	protected $store;

	/**
	 * The current res request.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	protected $current_request;

	/**
	 * The previous res request.
	 *
	 * @var \AweBooking\Availability\Request
	 */
	protected $previous_request;

	/**
	 * The reservation totals.
	 *
	 * @var \AweBooking\Reservation\Totals
	 */
	protected $totals;

	/**
	 * Determines the reservation has been restored.
	 *
	 * @var bool
	 */
	protected $restored = false;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Storage\Store $store The store instance.
	 */
	public function __construct( Store $store ) {
		$this->store        = $store;
		$this->fees         = new Collection;
		$this->services     = new Collection;
		$this->room_stays   = new Collection;
		$this->booked_rooms = [];
		$this->totals       = new Totals( $this );
	}

	/**
	 * Init the reservation.
	 *
	 * @return void
	 */
	public function init() {
		$this->source   = 'website';
		$this->language = abrs_running_on_multilanguage() ? abrs_multilingual()->get_current_language() : '';
		$this->currency = abrs_current_currency();

		add_action( 'wp_loaded', [ $this, 'restore' ], 20 );
		add_action( 'abrs_room_stay_added', [ $this, 'calculate_totals' ], 20, 0 );
		add_action( 'abrs_room_stay_removed', [ $this, 'calculate_totals' ], 20, 0 );
		add_action( 'abrs_reservation_restored', [ $this, 'calculate_totals' ], 20, 0 );

		do_action( 'abrs_reservation_init', $this );
	}

	/**
	 * Gets the current hotel instance.
	 *
	 * @return \AweBooking\Model\Hotel
	 */
	public function get_hotel() {
		return $this->hotel
			? abrs_get_hotel( $this->hotel )
			: abrs_get_primary_hotel();
	}

	/**
	 * Gets the total after calculation.
	 *
	 * @return float
	 */
	public function get_total() {
		return $this->totals->get( 'total' );
	}

	/**
	 * Gets the subtotal (before tax and discount).
	 *
	 * @return float
	 */
	public function get_subtotal() {
		return $this->totals->get( 'subtotal' );
	}

	/**
	 * Gets the totals instance.
	 *
	 * @return Totals
	 */
	public function get_totals() {
		return $this->totals;
	}

	/**
	 * Calculate the totals.
	 *
	 * @return void
	 */
	public function calculate_totals() {
		if ( $this->is_empty() ) {
			return;
		}

		do_action( 'abrs_calculate_totals', $this );

		$this->totals->calculate();

		do_action( 'abrs_complete_calculate_totals', $this );
	}

	/**
	 * Return all taxes.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_taxes() {
		$taxes = [ [] ];

		foreach ( $this->room_stays as $room_stay ) {
			$taxes[] = array_keys( $room_stay->get_tax_rates() );
		}

		return abrs_collect( array_merge( ...$taxes ) )
			->map( 'abrs_get_tax_rate' )
			->filter();
	}

	/**
	 * Gets the previous_request store in the session.
	 *
	 * @return \AweBooking\Availability\Request|null
	 */
	public function get_previous_request() {
		return $this->previous_request;
	}

	/**
	 * Sets the previous_request.
	 *
	 * @param \AweBooking\Availability\Request $request The res request.
	 * @return $this
	 */
	public function set_previous_request( Request $request ) {
		$this->previous_request = $request;

		return $this;
	}

	/**
	 * Gets the current res_request.
	 *
	 * @return \AweBooking\Availability\Request|null
	 */
	public function get_current_request() {
		return $this->current_request;
	}

	/**
	 * Sets the current res_request.
	 *
	 * @param  \AweBooking\Availability\Request $current_request The request instance.
	 * @return $this
	 */
	public function set_current_request( Request $current_request ) {
		$this->current_request = $current_request;

		if ( $hotel = $current_request->get_hotel() ) {
			$this->hotel = $hotel;
		}

		if ( $this->maybe_flush() ) {
			$this->flush();
		}

		return $this;
	}

	/**
	 * Resolve the res_request.
	 *
	 * @return \AweBooking\Availability\Request|null
	 */
	public function resolve_res_request() {
		$res_request = $this->get_current_request();

		if ( ! $res_request ) {
			$res_request = $this->get_previous_request();
		}

		return $res_request;
	}

	/**
	 * Flush the session data.
	 *
	 * @return void
	 */
	public function flush() {
		$this->fees->clear();
		$this->services->clear();
		$this->room_stays->clear();

		$this->booked_rooms     = [];
		$this->current_request  = null;
		$this->previous_request = null;

		$this->store->flush( 'room_stays' );
		$this->store->flush( 'booked_rooms' );
		$this->store->flush( 'booked_services' );
		$this->store->flush( 'previous_request' );
		$this->store->flush( 'reservation_hash' );

		$this->totals->calculate();

		do_action( 'abrs_reservation_emptied', $this );
	}

	/**
	 * Is need flush session data.
	 *
	 * @return bool
	 */
	public function maybe_flush() {
		if ( ! $session_hashid = $this->store->get( 'reservation_hash' ) ) {
			return false;
		}

		if ( ! hash_equals( $this->generate_hash_id(), $session_hashid ) ) {
			return true;
		}

		// Flush when session request & current request is different.
		$previous_request = $this->get_previous_request();

		return ( $this->current_request && $previous_request ) && ! $this->current_request->same_with( $previous_request );
	}

	/**
	 * Save the reservation state.
	 *
	 * @return void
	 */
	public function store() {
		if ( $this->current_request ) {
			$this->store->put( 'previous_request', $this->current_request->to_array() );
		}

		$this->store->put( 'room_stays', $this->room_stays->to_array() );
		$this->store->put( 'booked_rooms', (array) $this->booked_rooms );
		$this->store->put( 'booked_services', $this->get_services( true )->to_array() );
		$this->store->put( 'reservation_hash', $this->generate_hash_id() );

		do_action( 'abrs_reservation_stored', $this );
	}

	/**
	 * Restore the res request from the store.
	 *
	 * @return void
	 */
	protected function restore_request() {
		$previous_request = $this->store->get( 'previous_request' );

		if ( ! $previous_request || ! is_array( $previous_request ) ) {
			return;
		}

		$this->previous_request = ( new Request() )->initialize( $previous_request );

		do_action( 'abrs_res_request_restored', $this );
	}

	/**
	 * Restore the reservation from its saved state.
	 *
	 * @return void
	 */
	public function restore() {
		if ( defined( 'DOING_CRON' ) ) {
			return;
		}

		do_action( 'abrs_prepare_restore_reservation', $this );

		$this->restore_request();
		$this->restore_rooms();
		$this->restore_services();

		// Indicator the reservation has been restored.
		$this->restored = true;

		do_action( 'abrs_reservation_restored', $this );
	}

	/**
	 * Generate the reservation hash ID.
	 *
	 * @return string
	 */
	protected function generate_hash_id() {
		return sha1( implode( '-', [
			// $this->hotel,
			$this->source,
			$this->currency,
			$this->language,
		] ) );
	}
}
