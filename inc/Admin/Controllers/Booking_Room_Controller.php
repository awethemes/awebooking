<?php

namespace AweBooking\Admin\Controllers;

use WP_Error;
use RuntimeException;
use WPLibs\Http\Request;
use AweBooking\Model\Booking;
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
	 * @param  Request $request The current request.
	 * @return mixed
	 */
	public function search( Request $request ) {
		try {
			$booking = $this->preapre_booking( abrs_get_booking( $request['refer'] ) );
		} catch ( \Exception $e ) {
			return new WP_Error( 400, $e->getMessage() );
		}

		if ( $request->filled( 'check_in', 'check_out' ) || $request->filled( 'check-in', 'check-out' ) ) {
			$res_request = abrs_create_res_request( $request );

			$res_request['query_args'] = $request->only( 'hotel', 'only' );

			if ( is_null( $res_request ) || is_wp_error( $res_request ) ) {
				return new WP_Error( 400, esc_html__( 'ERR.', 'awebooking' ) ); // TODO: ...
			}

			$results = $res_request->search();
		}

		return $this->response( 'booking/add-room.php', compact(
			'request', 'booking', 'res_request', 'results'
		) );
	}

	/**
	 * //
	 *
	 * @param  Booking|null $booking
	 * @return Booking
	 *
	 * @throws \RuntimeException
	 */
	protected function preapre_booking( $booking ) {
		if ( ! $booking ) {
			throw new RuntimeException( esc_html__( 'The booking reference is does not exist.', 'awebooking' ) );
		}

		if ( ! $booking->is_editable() ) {
			 throw new RuntimeException( esc_html__( 'This booking is no longer editable.', 'awebooking' ) );
		}

		return $booking;
	}

	/**
	 * Handle store new booking payment.
	 *
	 * @param  Request $request The current request.
	 * @return mixed
	 */
	public function store( Request $request ) {
		check_admin_referer( 'add_booking_room', '_wpnonce' );

		try {
			$booking = $this->preapre_booking( abrs_get_booking( $request['_refer'] ) );
		} catch ( \Exception $e ) {
			return new WP_Error( 400, $e->getMessage() );
		}

		if ( empty( $request['reservation'][ $request->submit ] ) ) {
			return new WP_Error( 500, '@~@' );
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

		$room_item->save();

		return $this->redirect()->to( get_edit_post_link( $booking->get_id(), 'raw' ) );
	}

	/**
	 * Display the edit form.
	 *
	 * @param  \AweBooking\Model\Booking\Room_Item $room_item The booking payment item.
	 * @return mixed
	 */
	public function edit( Room_Item $room_item ) {
		try {
			$booking = $this->preapre_booking( abrs_get_booking( $room_item->booking_id ) );
		} catch ( \Exception $e ) {
			return new WP_Error( 400, $e->getMessage() );
		}

		$controls = new Edit_Booking_Room_Form( $room_item );

		return $this->response( 'booking/page-edit-room.php', compact( 'booking', 'room_item', 'controls' ) );
	}

	/**
	 * Perform update the room stay.
	 *
	 * @param  Request                             $request   The current request.
	 * @param  \AweBooking\Model\Booking\Room_Item $room_item The booking payment item.
	 * @return mixed
	 */
	public function update( Request $request, Room_Item $room_item ) {
		check_admin_referer( 'update_room_stay', '_wpnonce' );

		try {
			$this->preapre_booking( abrs_get_booking( $room_item->booking_id ) );
		} catch ( \Exception $e ) {
			return new WP_Error( 400, $e->getMessage() );
		}

		$data = ( new Edit_Booking_Room_Form( $room_item ) )->handle( $request );

		$redirect_fallback = abrs_admin_route( "/booking-room/{$room_item->get_id()}", $request->only( 'action' ) );

		switch ( $request->get( '_action' ) ) {
			case 'swap':
				if ( $swap_to = $request->get( 'swap_to_room' ) ) {
					$changed = $room_item->swap_room( $swap_to );

					if ( is_wp_error( $changed ) ) {
						abrs_flash_notices( $changed->get_error_message(), 'error' );
						return $this->redirect()->back( $redirect_fallback );
					}
				}
				break;

			case 'change-timespan':
				if ( $request->filled( 'change_check_in', 'change_check_out' ) ) {
					$to_timespan = abrs_timespan( $request->get( 'change_check_in' ), $request->get( 'change_check_out' ), 1 );

					if ( is_wp_error( $to_timespan ) ) {
						abrs_flash_notices( $to_timespan->get_error_message(), 'error' );
						return $this->redirect()->back( $redirect_fallback );
					}

					// Change to the new timespan.
					$changed = $room_item->change_timespan( $to_timespan );

					$room_item->set_subtotal( $data['subtotal'] );
					$room_item->set_total( $data['total'] );
					$room_item->save();

					if ( is_wp_error( $changed ) ) {
						abrs_flash_notices( $changed->get_error_message(), 'error' );
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
	 * @param \AweBooking\Model\Booking\Room_Item $room_item
	 * @return mixed
	 */
	public function destroy( Room_Item $room_item ) {
		check_admin_referer( 'delete_room_' . $room_item->get_id(), '_wpnonce' );

		abrs_delete_booking_item( $room_item );

		abrs_flash_notices( esc_html__( 'The booking room has been destroyed', 'awebooking' ), 'info' )->dialog();

		return $this->redirect()->back( get_edit_post_link( $room_item->get( 'booking_id' ), 'raw' ) );
	}
}
