<?php
namespace AweBooking\Admin\Controllers;

use WP_Error;
use Awethemes\Http\Request;
use AweBooking\Model\Booking\Room_Item;

class Booking_Room_Controller extends Controller {
	/**
	 * Handle store the settings.
	 *
	 * @param  \Awethemes\Http\Request   $request The current request.
	 * @param  \AweBooking\Model\Booking $booking The booking reference.
	 * @return \Awethemes\Http\Response
	 */
	public function create( Request $request ) {
		// Get the check the booking reference.
		if ( ! $booking = abrs_get_booking( $request['refer'] ) ) {
			return new WP_Error( 404, esc_html__( 'The booking reference is does not exist.', 'awebooking' ) );
		}

		if ( ! $booking->is_editable() ) {
			return new WP_Error( 404, esc_html__( 'This booking is no longer editable.', 'awebooking' ) );
		}

		if ( $request->filled( 'check-in', 'check-out' ) ) {
			$res_request = abrs_create_res_request([
				'check_in'  => $request->get( 'check-in' ),
				'check_out' => $request->get( 'check-out' ),
			]);

			if ( is_wp_error( $res_request ) ) {
				return $res_request;
			}

			$results = $res_request->search();
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
			return $this->whoops();
		}

		if ( empty( $request['reservation'][ $request->submit ] ) ) {
			return;
		}

		// The summit room & occupancy data.
		$submit_data = $request['reservation'][ $request->submit ];

		$room_type = abrs_get_room_type( $request->submit );
		$room_unit = abrs_get_room( $submit_data['room'] );

		// Create the reservation request.
		$res_request = abrs_create_res_request([
			'check_in'  => $request->get( 'check_in' ),
			'check_out' => $request->get( 'check_out' ),
			'adults'    => absint( $submit_data['adults'] ),
			'children'  => isset( $submit_data['children'] ) ? absint( $submit_data['children'] ) : 0,
			'infants'   => isset( $submit_data['infants'] ) ? absint( $submit_data['infants'] ) : 0,
		]);

		if ( is_wp_error( $res_request ) ) {
			return $res_request;
		}

		if ( $res_request->guest_counts->get_totals() > $room_type['maximum_occupancy'] ) {
			return 'aaaa';
		}

		$room_item = ( new Room_Item )->fill([
			'booking_id' => $booking->get_id(),
			'room_id'    => $room_unit->get_id(),
			'name'       => $room_unit['name'],
			'check_in'   => $res_request['check_in'],
			'check_out'  => $res_request['check_out'],
			'adults'     => $res_request['adults'],
			'children'   => $res_request['children'],
			'infants'    => $res_request['infants'],
		]);

		// dd( $room_item );
		$room_item->save();

		$room_item->set_timespan( $res_request->timespan );

		return $this->redirect()->back();
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
