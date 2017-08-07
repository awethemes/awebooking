<?php
namespace AweBooking;

/**
 * Simple Factory Pattern
 *
 * Create all things related to AweBooking.
 */
class Factory {
	/**
	 * //
	 *
	 * @var AweBooking
	 */
	protected $awebooking;

	/**
	 * //
	 *
	 * @param AweBooking $awebooking //.
	 */
	public function __construct( AweBooking $awebooking ) {
		$this->awebooking = $awebooking;
	}

	/**
	 * Get the a room unit.
	 *
	 * @param  mixed $room_unit Room unit ID or instance.
	 * @return AweBooking\Room
	 */
	public static function get_room_unit( $room_unit ) {
		return new Room( $room_unit );
	}

	public static function get_booking( $booking ) {
		return new Booking( $booking );
	}

	public static function get_booking_item( $a ) {
		if ( $a instanceof Booking_Item ) {
			return $a;
		}

		$class = static::reslove_booking_item_class( $a->booking_item_type );

		return new $class( $a->booking_item_id );
	}

	protected static function reslove_booking_item_class( $type ) {
		$class_maps = apply_filters( 'awebooking/booking_item_class_maps', [
			'line_item'    => 'AweBooking\\Booking_Room_Item',
			'service_item' => 'AweBooking\\Booking_Service_Item',
		]);

		if ( isset( $class_maps[ $type ] ) ) {
			return $class_maps[ $type ];
		}

		return 'AweBooking\\Booking_Item';
	}

	/**
	 * Create new booking.
	 *
	 * @param  array $args The booking arguments.
	 * @return \AweBooking\Booking
	 */
	public function create_booking( array $args ) {
		$args = wp_parse_args( $args, [
			'status'        => Booking::PENDING,
			'adults'        => 1,
			'children'      => 0,
			'check_in'      => '',
			'check_out'     => '',
			'room_id'       => 0,
			'availability'  => null,

			'customer_id'         => 0,
			'customer_note'       => '',
			'customer_first_name' => '',
			'customer_last_name'  => '',
			'customer_email'      => '',
			'customer_phone'      => '',
			'customer_company'    => '',
		]);

		$insert_id = wp_insert_post([
			'post_status'   => $args['status'],
			'post_type'     => AweBooking::BOOKING,
		], true );

		if ( is_wp_error( $insert_id ) ) {
			return false;
		}

		$booking = new Booking( $insert_id );

		$booking['status']        = $args['status'];
		$booking['adults']        = absint( $args['adults'] );
		$booking['children']      = absint( $args['children'] );
		$booking['check_in']      = $args['check_in'];
		$booking['check_out']     = $args['check_out'];
		$booking['room_id']       = absint( $args['room_id'] );

		$booking['customer_id']         = absint( $args['customer_id'] );
		$booking['customer_note']       = $args['customer_note'];
		$booking['customer_first_name'] = $args['customer_first_name'];
		$booking['customer_last_name']  = $args['customer_last_name'];
		$booking['customer_email']      = $args['customer_email'];
		$booking['customer_phone']      = $args['customer_phone'];
		$booking['customer_company']    = $args['customer_company'];

		if ( $args['availability'] ) {
			$availability = $args['availability'];

			$booking['currency']   = awebooking( 'currency' )->get_code();
			$booking['room_total'] = $availability->get_price()->get_amount();
			$booking['total']      = $availability->get_total_price()->get_amount();

			$booking['request_services'] = $availability->get_request_services();
			$booking['services_total']   = $availability->get_extra_services_price()->get_amount();
		}

		$booking->save();

		return $booking;
	}
}
