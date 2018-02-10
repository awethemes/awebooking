<?php
namespace AweBooking\Http\Controllers;

use Awethemes\Http\Request;
use Illuminate\Support\Arr;
use AweBooking\Model\Rate;
use AweBooking\Factory;
use AweBooking\Model\Guest;
use AweBooking\Reservation\Reservation;
use AweBooking\Model\Exceptions\Model_Not_Found_Exception;

class Reservation_Controller extends Controller{

	public function create( Request $request ) {
		$request->verify_nonce( '_wpnonce', 'awebooking_reservation' );

		// Try to resolve from session.
		$reservation = awebooking( 'reservation_session' )->resolve();

		if ( is_null( $reservation ) ) {
			throw new \RuntimeException( esc_html__( 'The reservation session could not found, please try again.', 'awebooking' ) );
		}

		// Get the submited room-item.
		$submited_item = Arr::first( array_keys( (array) $request->submit ) );

		// Build the add room-item data.
		// $item_data = (array) $request->input( "reservation_room.{$submited_item}" );
		$item_data['room_type'] = $submited_item;
		$item_data['room_unit'] = 30;
		$item_data['room_rate'] = 0;
		$item_data['adults'] = $request->get( 'adults' );
		$item_data['children'] = $request->get( 'children' );
		$item_data['infants'] = $request->get( 'infants' );

		try {
			$this->add_reservation_item( $reservation, $item_data );

			// awebooking( 'admin_notices' )->success( esc_html__( 'Item added successfully', 'awebooking' ) );
		} catch ( \Exception $e ) {
			// awebooking( 'admin_notices' )->error( $e->getMessage() );
		}

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


	/**
	 * Assert a given object exists.
	 *
	 * @param  \AweBooking\Model\WP_Object $object WP_Object implementation.
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 * @throws \AweBooking\Model\Exceptions\Model_Not_Found_Exception
	 */
	protected function assert_object_exists( $object ) {
		if ( is_null( $object ) ) {
			throw new Model_Not_Found_Exception( esc_html__( 'Resource not found', 'awebooking' ) );
		}

		if ( ! $object->exists() ) {
			throw (new Model_Not_Found_Exception)->set_model( get_class( $object ) );
		}
	}
}
