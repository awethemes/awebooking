<?php
namespace AweBooking\Reservation;

use AweBooking\Support\Collection;
use AweBooking\Reservation\Storage\Store;

class Reservation {
	use Traits\With_Room_Stays,
		Traits\Handle_Session_Store;

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
	 * The reservation hotel ID.
	 *
	 * @var int
	 */
	public $hotel;

	/**
	 * The reservation totals.
	 *
	 * @var \AweBooking\Reservation\Totals
	 */
	protected $totals;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Storage\Store $store The store instance.
	 */
	public function __construct( Store $store ) {
		$this->store        = $store;
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
		add_action( 'setup_res_request', [ $this, 'setup' ], 20 );
		add_action( 'abrs_room_stay_added', [ $this, 'calculate_totals' ], 20, 0 );
		add_action( 'abrs_room_stay_removed', [ $this, 'calculate_totals' ], 20, 0 );
		add_action( 'abrs_reservation_restored', [ $this, 'calculate_totals' ], 20, 0 );
		add_action( 'abrs_complete_calculate_totals', [ $this, 'store' ], 20, 0 );

		do_action( 'abrs_reservation_init', $this );
	}

	/**
	 * Restore the reservation from its saved state.
	 *
	 * @return void
	 */
	public function restore() {
		do_action( 'abrs_prepare_restore_reservation', $this );

		$this->restore_request();
		$this->restore_rooms();

		do_action( 'abrs_reservation_restored', $this );
	}

	public function setup( $res_request ) {
		$this->set_current_request( $res_request );

		// Flush the session when something change.
		$this->maybe_flush();
	}

	/**
	 * Is need flush session data.
	 *
	 * @return bool
	 */
	public function maybe_flush() {
		// When session request & current request is different.
		$previous_request = $this->get_previous_request();
		if ( $previous_request && ! $this->current_request->same_with( $previous_request ) ) {
			return true;
		}

		if ( abrs_running_on_multilanguage() && abrs_multilingual()->get_current_language() !== $this->language ) {
			return true;
		}

		return false;
	}

	/**
	 * Calculate the totals.
	 *
	 * @return void
	 */
	public function calculate_totals() {
		// $this->reset_totals();

		if ( $this->is_empty() ) {
			// $this->session->set_session();
			return;
		}

		do_action( 'abrs_calculate_totals', $this );

		$this->totals->calculate();

		do_action( 'abrs_complete_calculate_totals', $this );
	}

	/**
	 * Gets the total after calculation.
	 *
	 * @return \AweBooking\Support\Decimal
	 */
	public function get_total() {
		return $this->totals->get( 'total' );
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
	 * Gets the current hotel instance.
	 *
	 * @return \AweBooking\Model\Hotel
	 */
	public function get_hotel() {
		return $this->hotel
			? abrs_get_hotel( $this->hotel )
			: abrs_get_primary_hotel();
	}
}
