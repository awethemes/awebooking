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
		return new Date_Period( $this->get_check_in(), $this->get_check_out() );
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
		if ( $this->exists() && $this->is_dirty( 'check_in', 'check_out' ) ) {
			if ( ! $this->is_changeable( $date_period ) ) {
				throw new \RuntimeException( esc_html__( 'Dates could not be changed because at least one of the rooms is occupied on the selected dates.', 'awebooking' ) );
			}
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

		$date_period = $this->get_date_period();

		$booking = $this->get_booking();
		$the_room = $this->get_booking_room();

		$concierge = awebooking()->make( 'concierge' );

		// Moveing date period.
		if ( ! $this->recently_created && $this->is_dirty( 'check_in', 'check_out' ) && $this->is_changeable( $date_period ) ) {
			$original_period = new Date_Period(
				$this->original['check_in'], $this->original['check_out']
			);

			$concierge->set_room_state( $the_room, $original_period, Room_State::AVAILABLE, [
				'force' => true,
			]);

			$concierge->store_room_booking( $the_room, $original_period, 0 );
		}

		$concierge->set_room_state( $the_room, $date_period, $booking->get_state_status(), [
			'force' => true,
		]);

		$concierge->store_room_booking( $the_room, $date_period, $booking->get_id() );
	}

	/**
	 * Determines whether the new period can be changeable.
	 *
	 * @throws \LogicException
	 *
	 * @param  Date_Period $to_period Change to date period.
	 * @return bool|null
	 */
	public function is_changeable( Date_Period $to_period ) {
		if ( $to_period->nights() < 1 ) {
			throw new \LogicException( esc_html__( 'The date period must be have minimum one night.', 'awebooking' ) );
		}

		$room_unit = $this->get_booking_room();
		if ( ! $room_unit->exists() ) {
			return;
		}

		$original_period = new Date_Period(
			$this->original['check_in'], $this->original['check_out']
		);

		// If new period inside the current-period,
		// so it alway can be change.
		if ( $original_period->contains( $to_period ) ) {
			return true;
		}

		// If both period object not overlaps, so we just
		// determines new period is bookable or not.
		if ( ! $original_period->overlaps( $to_period ) ) {
			return $room_unit->is_free( $to_period );
		}

		// Create an array difference between two Period.
		// @see http://period.thephpleague.com/api/comparing/#perioddiff .
		$diff = $original_period->diff( $to_period );

		// Loop each piece of diff-period, if one of them
		// un-available for changing just leave and return false.
		foreach ( $diff as $piece ) {
			if ( ! $original_period->contains( $piece ) && ! $room_unit->is_free( $piece ) ) {
				return false;
			}
		}

		return true;
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
