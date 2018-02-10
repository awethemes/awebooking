<?php
namespace AweBooking\Http\Controllers;

use AweBooking\Factory;
use AweBooking\Model\Rate;
use AweBooking\Model\Guest;
use AweBooking\Reservation\Reservation;
use Illuminate\Support\Arr;
use Awethemes\Http\Request;

class Reservation_Controller extends Controller {
	/**
	 * Add a room-item in session reservation.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 *
	 * @throws \RuntimeException
	 */
	public function add_item( Request $request ) {
		$request->verify_nonce( '_wpnonce', 'awebooking_reservation' );

		$reservation = awebooking( 'reservation_session' )->resolve();

		if ( is_null( $reservation ) ) {
			$this->notices( 'error', esc_html__( 'The reservation session could not found, please try again.', 'awebooking' ) );
			return $this->redirect()->back();
		}

		// Get the submited room-item.
		$submited_item = Arr::first( array_keys( (array) $request->submit ) );
		dd( $submited_item );

		// // Build the add room-item data.
		// // $item_data = (array) $request->input( "reservation_room.{$submited_item}" );
		// $item_data['room_type'] = $submited_item;
		// $item_data['room_unit'] = 30;
		// $item_data['room_rate'] = 0;
		// $item_data['adults'] = $request->get( 'adults' );
		// $item_data['children'] = $request->get( 'children' );
		// $item_data['infants'] = $request->get( 'infants' );

		// try {
		// 	$this->add_reservation_item( $reservation, $item_data );

		// 	// awebooking( 'admin_notices' )->success( esc_html__( 'Item added successfully', 'awebooking' ) );
		// } catch ( \Exception $e ) {
		// 	// awebooking( 'admin_notices' )->error( $e->getMessage() );
		// }

		return $this->redirect()->back();
	}

	/**
	 * Add reservation item by given a trusted data.
	 *
	 * @param  Reservation $reservation The reservation instance.
	 * @param  array       $data        The reservation item data.
	 * @return void
	 */
	protected function add_reservation_item( Reservation $reservation, array $data ) {
		$data = wp_parse_args( $data, [
			'room_type'  => 0,
			'room_unit'  => 0,
			'room_rate'  => 0,
			'adults'     => 1,
			'children'   => 0,
			'infants'    => 0,
		]);

		// Validate the room_type.
		$room_type = Factory::get_room_type( $data['room_type'] );
		$this->assert_object_exists( $room_type );

		$room_unit = $room_type->get_room( $data['room_unit'] );
		$this->assert_object_exists( $room_unit );

		// ...
		$rate = new Rate( $room_type->get_id(), 'room_type' );

		// Add room into the reservation.
		$reservation->add_room( $room_unit, $rate,
			new Guest( $data['adults'], $data['children'], $data['infants'] )
		);

		// Update the reservation in the session store.
		awebooking( 'reservation_session' )->update( $reservation );
	}
}
