<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;
use AweBooking\Model\Room;

class Room_Controller extends Controller {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->require_capability( 'manage_awebooking' );
	}

	/**
	 * Delete room.
	 *
	 * @param \Awethemes\Http\Request $request The current request.
	 * @param \AweBooking\Model\Room  $room    Room.
	 * @return mixed
	 */
	public function destroy( Request $request, Room $room ) {
		check_admin_referer( 'delete_room_' . $room->get_id(), '_wpnonce' );

		$booking_status = [
			'awebooking-completed',
			'checked-in',
			'checked-out',
			'awebooking-cancelled',
		];

		if ( ! abrs_get_bookings_by_room( $room->get_id(), $booking_status ) ) {
			$updated = $room->delete();

			if ( $updated && ! is_wp_error( $updated ) ) {
				abrs_admin_notices( esc_html__( 'Delete room successfully', 'awebooking' ), 'success' )->dialog();
			}
		} else {
			abrs_admin_notices( sprintf( __( 'This room is contained by other bookings. You can review that <a href="%s" target="_blank">here</a>', 'awebooking' ), esc_url( add_query_arg( '_room', $room->get_id(), admin_url( 'edit.php?post_type=awebooking' ) ) ) ), 'error' )->important();
		}

		return $this->redirect()->back();
	}
}
