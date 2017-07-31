<?php
namespace AweBooking;

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

		'check_in'     => '',
		'check_out'    => '',
		'adults'       => 0,
		'children'     => 0,
		'room_id'      => 0,

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

	public function get_check_in() {
		return apply_filters( $this->prefix( 'get_check_in' ), $this['check_in'], $this );
	}

	public function get_check_out() {
		return apply_filters( $this->prefix( 'get_check_out' ), $this['check_out'], $this );
	}

	public function get_period() {
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
			$this->get_period()->nights()
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
		$this->attributes['subtotal'] = floatval( $value );
	}

	/**
	 * Line total (after discounts).
	 *
	 * @param string $value
	 * @throws WC_Data_Exception
	 */
	public function set_total( $value ) {
		$this->attributes['total'] = floatval( $value );

		// Subtotal cannot be less than total.
		if ( ! $this->get_subtotal() || $this['subtotal'] < $this['total'] ) {
			$this->attributes['subtotal'] = $value;
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
			$date_period = $this->get_period();

			$concierge->set_room_state( $the_room, $date_period, $booking->get_state_status(), [
				'force' => true,
			]);

			$concierge->store_room_booking( $the_room, $date_period, $booking->get_id() );

		} catch ( \Exception $e ) {
			return;
		}
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
			$this->get_period(),
			Room_State::AVAILABLE,
			[ 'force' => true ]
		);

		$concierge->store_room_booking(
			$this->get_booking_room(),
			$this->get_period(),
			0
		);

		return parent::perform_delete( $force );
	}
}
