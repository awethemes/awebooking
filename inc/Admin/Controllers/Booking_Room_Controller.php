<?php
namespace AweBooking\Admin\Controllers;

use WP_Error;
use Awethemes\Http\Request;
use AweBooking\Model\Booking\Room_Item;
use AweBooking\Reservation\Room_Stay\Room_Rate;

class Booking_Room_Controller extends Controller {
	/**
	 * Handle search rooms.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function search( Request $request ) {
		// Get the check the booking reference.
		if ( ! $booking = abrs_get_booking( $request['refer'] ) ) {
			return new WP_Error( 404, esc_html__( 'The booking reference is does not exist.', 'awebooking' ) );
		}

		if ( ! $booking->is_editable() ) {
			return new WP_Error( 400, esc_html__( 'This booking is no longer editable.', 'awebooking' ) );
		}

		if ( $request->filled( 'check-in', 'check-out' ) ) {
			$res_request = abrs_create_res_request( $request );

			if ( is_wp_error( $res_request ) ) {
				return $res_request;
			}

			$results = $res_request->search( [] );
		}

		return $this->response( 'booking/add-room.php', compact(
			'request', 'booking', 'res_request', 'results'
		));
	}

	/**
	 * Handle store new booking payment.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function store( Request $request ) {
		check_admin_referer( 'add_booking_room', '_wpnonce' );

		if ( ! $request->filled( '_refer' ) || ! $booking = abrs_get_booking( $request['_refer'] ) ) {
			return new WP_Error( 404, esc_html__( 'The booking reference is does not exist.', 'awebooking' ) );
		}

		if ( empty( $request['reservation'][ $request->submit ] ) ) {
			return;
		}

		// The summit room & occupancy data.
		$submit_data = $request['reservation'][ $request->submit ];

		$room_unit = abrs_get_room( $submit_data['room'] );
		$room_type = abrs_get_room_type( $request->submit );
		$rate_plan = isset( $request->rate_plan ) ? abrs_get_rate_plan( $request->rate_plan ) : $room_type->get_standard_plan();

		// Create the reservation request.
		$timespan = abrs_timespan( $request->get( 'check_in' ), $request->get( 'check_out' ), 1 );
		if ( is_wp_error( $timespan ) ) {
			return $timespan;
		}

		$room_item = ( new Room_Item )->fill([
			'name'           => $room_unit->get( 'name' ),
			'room_id'        => $room_unit->get_id(),
			'booking_id'     => $booking->get_id(),
			'room_type_id'   => $room_type->get_id(),
			'rate_plan_id'   => $rate_plan->get_id(),
			'room_type_name' => $room_type->get( 'title' ),
			'rate_plan_name' => $rate_plan->get_private_name(),
			'adults'         => absint( $submit_data['adults'] ),
			'children'       => isset( $submit_data['children'] ) ? absint( $submit_data['children'] ) : 0,
			'infants'        => isset( $submit_data['infants'] ) ? absint( $submit_data['infants'] ) : 0,
		]);

		$room_item->set_timespan( $timespan );
		$room_item->set_total( isset( $submit_data['total'] ) ? $submit_data['total'] : 0 );

		$saved = $room_item->save();

		return $this->redirect()->to( get_edit_post_link( $booking->get_id(), 'raw' ) );
	}

	/**
	 * Perform delete a booking room.
	 *
	 * @param  \Awethemes\Http\Request             $request   The current request.
	 * @param  \AweBooking\Model\Booking_Room_Item $room_item The room item instance.
	 * @return \Awethemes\Http\Response
	 */
	public function destroy( Request $request, Room_Item $room_item ) {
		check_admin_referer( 'delete_room_' . $room_item->get_id(), '_wpnonce' );

		// Delete the room item.
		$room_item->delete();

		abrs_admin_notices( esc_html__( 'The booking room has been destroyed', 'awebooking' ), 'info' )->dialog();

		return $this->redirect()->back( get_edit_post_link( $room_item['booking_id'], 'raw' ) );
	}
}
