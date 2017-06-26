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
	 * @var AweBooking]
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
