<?php
namespace AweBooking\Admin\Controllers;

use WP_Error;
use Awethemes\Http\Request;
use AweBooking\Model\Booking\Room_Item;
use AweBooking\Model\Common\Guest_Counts;
use AweBooking\Admin\Forms\Edit_Booking_Room_Form;

class Booking_Room_Controller extends Controller {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->require_capability( 'manage_awebooking' );
	}

	/**
	 * Handle search rooms.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return mixed
	 */
	public function search( Request $request ) {
		// Get the check the booking reference.
		if ( ! $booking = abrs_get_booking( $request['refer'] ) ) {
			return new WP_Error( 404, esc_html__( 'The booking reference is does not exist.', 'awebooking' ) );
		}

		if ( ! $booking->is_editable() ) {
			return new WP_Error( 400, esc_html__( 'This booking is no longer editable.', 'awebooking' ) );
		}

		if ( $request->filled( 'check_in', 'check_out' ) || $request->filled( 'check-in', 'check-out' ) ) {
			$res_request = abrs_create_res_request( $request );

			if ( is_null( $res_request ) || is_wp_error( $res_request ) ) {
				return new WP_Error( 400, esc_html__( 'ERR.', 'awebooking' ) ); // TODO: ...
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
	 * @return mixed
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

		// TODO: ...
		$rate_plan = isset( $request->rate_plan )
			? abrs_get_rate( $request->rate_plan )
			: abrs_get_base_rate( $room_type );

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
	 * Display the edit form.
	 *
	 * @param  \Awethemes\Http\Request             $request   The current request.
	 * @param  \AweBooking\Model\Booking\Room_Item $room_item The booking payment item.
	 * @return mixed
	 */
	public function edit( Request $request, Room_Item $room_item ) {
		if ( ! $booking = abrs_get_booking( $room_item->booking_id ) ) {
		}

		$controls = new Edit_Booking_Room_Form( $room_item );

		return $this->response( 'booking/page-edit-room.php', compact( 'booking', 'room_item', 'controls' ) );
	}

	/**
	 * Perform update the room stay.
	 *
	 * @param  \Awethemes\Http\Request             $request   The current request.
	 * @param  \AweBooking\Model\Booking\Room_Item $room_item The booking payment item.
	 * @return mixed
	 */
	public function update( Request $request, Room_Item $room_item ) {
		check_admin_referer( 'update_room_stay', '_wpnonce' );

		$data = ( new Edit_Booking_Room_Form( $room_item ) )->handle( $request );

		$redirect_fallback = abrs_admin_route( "/booking-room/{$room_item->get_id()}", $request->only( 'action' ) );

		switch ( $request->get( '_action' ) ) {
			case 'swap-room':
				// TODO: ...
				break;

			case 'change-timespan':
				if ( $request->filled( 'change_check_in', 'change_check_out' ) ) {
					$to_timespan = abrs_timespan( $request->get( 'change_check_in' ), $request->get( 'change_check_out' ), 1 );

					if ( is_wp_error( $to_timespan ) ) {
						abrs_admin_notices( $to_timespan->get_error_message(), 'error' );
						return $this->redirect()->back( $redirect_fallback );
					}

					// Change to the new timespan.
					$changed = $room_item->change_timespan( $to_timespan );

					if ( is_wp_error( $changed ) ) {
						abrs_admin_notices( $changed->get_error_message(), 'error' );
						return $this->redirect()->back( $redirect_fallback );
					}
				}
				break;

			default:
				$room_item->set_guests( new Guest_Counts( $data['adults'], $data['children'], $data['infants'] ) );
				$room_item->set_subtotal( $data['subtotal'] );
				$room_item->set_total( $data['total'] );
				break;
		}

		try {
			$room_item->save();
		} catch ( \Exception $e ) {
			abrs_report( $e );
		}

		return $this->redirect()->to( get_edit_post_link( $room_item->get( 'booking_id' ), 'raw' ) );
	}

	/**
	 * Perform delete a booking room.
	 *
	 * @param  \Awethemes\Http\Request             $request   The current request.
	 * @param  \AweBooking\Model\Booking\Room_Item $room_item The room item instance.
	 * @return mixed
	 */
	public function destroy( Request $request, Room_Item $room_item ) {
		check_admin_referer( 'delete_room_' . $room_item->get_id(), '_wpnonce' );

		abrs_delete_booking_item( $room_item );

		abrs_admin_notices( esc_html__( 'The booking room has been destroyed', 'awebooking' ), 'info' )->dialog();

		return $this->redirect()->back( get_edit_post_link( $room_item->get( 'booking_id' ), 'raw' ) );
	}
}
