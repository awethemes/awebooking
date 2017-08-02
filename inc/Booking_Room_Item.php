<?php
namespace AweBooking;

use AweBooking\BAT\Request;
use AweBooking\BAT\Calendar;
use AweBooking\Pricing\Price;
use AweBooking\Support\Date_Period;

class Booking_Room_Item extends Booking_Item {
	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'room_item';

	/**
	 * The attributes for this object.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $attributes = [
		'name'         => '',
		'booking_id'   => 0,
		'room_id'      => 0,
		'check_in'     => '',
		'check_out'    => '',
		'adults'       => 0,
		'children'     => 0,
		'subtotal'     => 0,
		'total'        => 0,
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'room_id'    => 'int',
		'booking_id' => 'int',
		'adults'     => 'int',
		'children'   => 'int',
		'total'      => 'float',
		'subtotal'   => 'float',
	];

	/**
	 * An array of attributes mapped with metadata.
	 *
	 * @var array
	 */
	protected $maps = [
		'room_id'   => '_room_id',
		'adults'    => '_adults',
		'children'  => '_children',
		'check_in'  => '_check_in',
		'check_out' => '_check_out',
		'subtotal'  => '_line_subtotal',
		'total'     => '_line_total',
	];

	protected $_booking;

	/**
	 * Returns booking item type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'line_item';
	}

	public function get_booking() {
		if ( is_null( $this->_booking ) ) {
			$this->_booking = new Booking( $this->get_booking_id() );
		}

		return $this->_booking;
	}

	public function get_booking_room() {
		return new Room( $this['room_id'] );
	}

	public function get_room_id() {
		return $this['room_id'];
	}

	public function get_check_in() {
		return apply_filters( $this->prefix( 'get_check_in' ), $this['check_in'], $this );
	}

	public function get_check_out() {
		return apply_filters( $this->prefix( 'get_check_out' ), $this['check_out'], $this );
	}

	public function get_date_period() {
		return new Date_Period( $this->get_check_in(), $this->get_check_out(), false );
	}

	public function get_adults() {
		return apply_filters( $this->prefix( 'get_adults' ), $this['adults'], $this );
	}

	public function get_children() {
		return apply_filters( $this->prefix( 'get_children' ), $this['children'], $this );
	}

	public function get_total() {
		return apply_filters( $this->prefix( 'get_total' ), new Price( $this['total'], $this->get_booking()->get_currency() ), $this );
	}

	public function get_subtotal() {
		return apply_filters( $this->prefix( 'get_subtotal' ), $this['subtotal'], $this );
	}

	public function get_total_price() {
		return $this->get_total()->multiply(
			$this->get_date_period()->nights()
		);
	}

	/**
	 * Set Room ID.
	 *
	 * @param int $value
	 * @throws WC_Data_Exception
	 */
	public function set_product_id( $value ) {
		if ( $value > 0 && 'product' !== get_post_type( absint( $value ) ) ) {
			$this->error( 'order_item_product_invalid_product_id', __( 'Invalid product ID', 'woocommerce' ) );
		}
		$this->set_prop( 'product_id', absint( $value ) );
	}

	/**
	 * Line subtotal (before discounts).
	 *
	 * @param string $value
	 * @throws WC_Data_Exception
	 */
	public function set_subtotal( $value ) {
		$this->attributes['subtotal'] = awebooking_sanitize_price( $value );
	}

	/**
	 * Line total (after discounts).
	 *
	 * @param string $value
	 */
	public function set_total( $value ) {
		$this->attributes['total'] = awebooking_sanitize_price( $value );

		// Subtotal cannot be less than total.
		if ( ! $this->get_subtotal() || $this['subtotal'] < $this['total'] ) {
			$this->attributes['subtotal'] = $value;
		}
	}

	/**
	 * Do something before doing save.
	 *
	 * @throws \LogicException
	 * @throws \RuntimeException
	 *
	 * @return void
	 */
	protected function before_save() {
		$date_period = $this->get_date_period();

		// A booking stay cannot less than one night.
		// BTW, an Exception will be throws in $this->get_date_period()
		// if check-in and check-out date is invalid date format.
		if ( $date_period->nights() < 1 ) {
			throw new \LogicException( esc_html__( 'Required minimum one night for the booking.', 'awebooking' ) );
		}

		// If this save action considered as update, we check `check_in`, `check_out`
		// have changes, we'll re-check available of booking room again to make sure
		// everything in AweBooking it is working perfect.
		if ( $this->exists() && $this->is_dirty( 'check_in', 'check_out' ) && ! $this->is_room_available() ) {
			throw new \RuntimeException( esc_html__( 'Dates could not be changed because at least one of the rooms is occupied on the selected dates.', 'awebooking' ) );
		}

		// To prevent change `room_id` and `booking_id`, we don't
		// allow change them, so just set to original value.
		if ( $this->exists() && $this->is_dirty( 'room_id', 'booking_id' ) ) {
			$this->revert_attribute( 'room_id' );
			$this->revert_attribute( 'booking_id' );
		}
	}

	/**
	 * Do somethings when finish save.
	 *
	 * @return void
	 */
	protected function finish_save() {
		parent::finish_save();

		$booking = $this->get_booking();
		$the_room = $this->get_booking_room();

		try {
			$concierge = awebooking( 'concierge' );
			$date_period = $this->get_date_period();

			$concierge->set_room_state( $the_room, $date_period, $booking->get_state_status(), [
				'force' => true,
			]);

			$concierge->store_room_booking( $the_room, $date_period, $booking->get_id() );

		} catch ( \Exception $e ) {
			return;
		}
	}

	public function is_available_for_changes() {
		$request  = $this->get_booking_request( '2017-09-03', '2017-09-08' );
		$calendar = new Calendar( [ $this->get_booking_room() ], awebooking( 'store.availability' ) );

		$response = $calendar->getMatchingUnits(
			$request->get_check_in(),
			$request->get_check_out()->subMinute(),
			$request->valid_states()
		);

		// If the change date-period available for made booking,
		// just return true, tell that available for moving next action.
		/*if ( $available ) {
			return true;
		}*/

		// Check if we working on this date.

		// Get the booking ID of current.

		dd( $response );
	}

	/**
	 * The the booking request.
	 *
	 * If check_in and check_out not present,
	 * using default $this->get_date_period() method.
	 *
	 * @param  string|Carbon $check_in  Optional, check-in date.
	 * @param  string|Carbon $check_out Optional, check-out date.
	 * @return AweBooking\BAT\Request
	 */
	protected function get_booking_request( $check_in = null, $check_out = null ) {
		if ( ! is_null( $check_in ) && ! is_null( $check_out ) ) {
			$date_period = new Date_Period( $check_in, $check_out, false );
		} else {
			$date_period = $this->get_date_period();
		}

		return new Request( $date_period, [
			'adults'   => $this->get_adults(),
			'children' => $this->get_children(),
		]);
	}

	/**
	 * //
	 *
	 * @return boolean
	 */
	protected function is_room_available() {
		$the_room = $this->get_booking_room();

		$availability = awebooking( 'concierge' )->check_room_type_availability(
			$the_room->get_room_type(),
			$this->get_booking_request()
		);

		return $availability->available() &&
			in_array( $the_room->get_id(), $availability->get_rooms_ids() );
	}

	/**
	 * Perform delete object.
	 *
	 * @param  bool $force Force delete or not.
	 * @return bool
	 */
	protected function perform_delete( $force ) {
		// Before we delete a room-item, restore available state and booking room.
		$concierge = awebooking()->make( 'concierge' );

		try {

		} catch ( \Exception $e ) {
			// ...
		}

		$booking = $this->get_booking();

		$concierge->set_room_state(
			$this->get_booking_room(),
			$this->get_date_period(),
			Room_State::AVAILABLE,
			[ 'force' => true ]
		);

		$concierge->store_room_booking(
			$this->get_booking_room(),
			$this->get_date_period(),
			0
		);

		return parent::perform_delete( $force );
	}
}
