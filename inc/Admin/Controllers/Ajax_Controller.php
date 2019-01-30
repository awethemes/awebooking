<?php

namespace AweBooking\Admin\Controllers;

use AweBooking\Model\Booking\Room_Item;
use WPLibs\Http\Request;
use WPLibs\Http\Json_Response;
use AweBooking\Model\Room;

class Ajax_Controller extends Controller {
	/**
	 * Delete room.
	 *
	 * @param \WPLibs\Http\Request   $request The current request.
	 * @param \AweBooking\Model\Room $room    Room.
	 * @return mixed
	 */
	public function delete_room( Request $request, Room $room ) {
		$this->require_capability( 'manage_awebooking' );

		if ( ! wp_verify_nonce( $request->get( '_wpnonce' ), 'delete_room' ) ) {
			return $this->response_json( 'error', esc_html__( 'Something went wrong. Please try again.', 'awebooking' ) );
		}

		$rooms = abrs_get_booking_booked_by_room( $room->get_id(), [
			'awebooking-inprocess',
			'awebooking-deposit',
			'awebooking-on-hold',
			'awebooking-completed',
			'checked-in',
		] );

		// No rooms belong to any booking, just delete that!
		if ( count( $rooms ) === 0 ) {
			$room->delete();
			return $this->response_json( 'success', esc_html__( 'Room successfully deleted', 'awebooking' ) );
		}

		return $this->response_json( 'error', esc_html__( 'This room cannot delete, because it contained by another booking.', 'awebooking' ) );
	}

	/**
	 * Handle add booking note.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @return mixed
	 */
	public function add_booking_note( Request $request ) {
		$this->require_capability( 'manage_awebooking' );

		if ( ! check_ajax_referer( 'awebooking_add_note', null, false ) ) {
			return $this->response_json( 'error', esc_html__( 'Something went wrong.', 'awebooking' ) );
		}

		$booking       = absint( $request->booking );
		$note          = wp_kses_post( trim( stripslashes( $request->note ) ) );
		$customer_note = ( 'customer' === $request->note_type );

		if ( $booking <= 0 || empty( $note ) ) {
			return $this->response_json( 'error', esc_html__( 'Please enter some content to note.', 'awebooking' ) );
		}

		// Perform add booking note.
		$comment_id = abrs_add_booking_note( $booking, $note, $customer_note, true );

		if ( ! $comment_id || is_wp_error( $comment_id ) ) {
			return $this->response_json( 'error', esc_html__( 'Could not create note, please try again.', 'awebooking' ) );
		}

		// Response back HTML booking note.
		$note = abrs_get_booking_note( $comment_id );
		$data = abrs_admin_template( ABRS_ADMIN_PATH . '/Metaboxes/views/html-booking-note.php', compact( 'note' ) );

		return $this->response_json( 'success', null, $data );
	}

	/**
	 * Handle add booking note.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @param  int                  $note    The booking note ID to delete.
	 * @return mixed
	 */
	public function delete_booking_note( Request $request, $note ) {
		$this->require_capability( 'manage_awebooking' );

		if ( ! check_ajax_referer( 'awebooking_delete_note', null, false ) ) {
			return $this->response_json( 'error', esc_html__( 'Something went wrong.', 'awebooking' ) );
		}

		$deleted = abrs_delete_booking_note( $note );

		return $this->response_json( $deleted ? 'success' : 'failure' );
	}

	/**
	 * Query for customers.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @return \WPLibs\Http\Response|mixed
	 */
	public function search_customers( Request $request ) {
		$term = abrs_clean( stripslashes( $request->get( 'term' ) ) );
		if ( empty( $term ) ) {
			return [];
		}

		// Begin build the customers IDs.
		$ids = [];

		// First, check if term is numeric so we just search by ID.
		if ( is_numeric( $term ) ) {
			$customer = get_userdata( absint( $term ) );

			if ( $customer && 0 !== $customer->ID ) {
				$ids = [ $customer->ID ];
			}
		}

		// Exclude IDs if requested.
		if ( $request->filled( 'exclude' ) ) {
			$ids = array_diff( $ids, wp_parse_id_list( $request->get( 'exclude' ) ) );
		}

		// Usernames can be numeric so we first check that no users was found by ID before
		// searching for numeric username, this prevents performance issues with ID lookups.
		if ( empty( $ids ) ) {
			// If search is smaller than 3 characters, limit result set to avoid
			// too many rows being returned.
			$limit = ( strlen( $term ) < 3 ) ? 20 : 0;

			$ids = abrs_search_customers( $term, $limit );
		}

		// Now, let's build the results.
		$found_customers = [];

		foreach ( $ids as $id ) {
			$customer = get_userdata( $id );
			if ( ! $customer ) {
				continue;
			}

			$found_customers[] = [
				'id'         => $id,
				'email'      => $customer->user_email,
				'first_name' => $customer->first_name,
				'last_name'  => $customer->last_name,
				/* translators: 1: user display name 2: user ID 3: user email */
				'display'    => sprintf( esc_html__( '%1$s (#%2$s - %3$s)', 'awebooking' ), $customer->first_name . ' ' . $customer->last_name, $customer->ID, $customer->user_email ),
			];
		}

		return new Json_Response( $found_customers );
	}

	/**
	 * Query for services.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @return \WPLibs\Http\Response|mixed
	 */
	public function search_services( Request $request ) {
		$term = abrs_clean( stripslashes( $request->get( 'term' ) ) );
		if ( empty( $term ) ) {
			return [];
		}

		$services = [];

		// First, check if term is numeric so we just search by ID.
		if ( is_numeric( $term ) ) {
			$service = abrs_get_service( absint( $term ) );

			if ( $service ) {
				$services = abrs_collect( [ $service ] );
			}
		}

		if ( empty( $services ) ) {
			$services = abrs_list_services([
				's'            => $term,
				'post__not_in' => wp_parse_id_list( $request->get( 'exclude', [] ) ),
			]);
		}

		return new Json_Response( $services );
	}

	/**
	 * Check the rates.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @return \WPLibs\Http\Response|mixed
	 */
	public function check_rates( Request $request ) {
		if ( $request->filled( 'booked' ) ) {
			$this->fill_booked_request( $request );
		}

		if ( ! $request->filled( 'check_in', 'check_out', 'room_type' ) ) {
			return new Json_Response( [ 'status' => 'error' ], 400 );
		}

		$args = $request->only(
			'adults', 'children', 'infants', 'check_in', 'check_out', 'room_type', 'rate_plan'
		);

		$room_rate = abrs_retrieve_room_rate( array_merge( $args, [
			'request' => $request,
		] ) );

		if ( is_wp_error( $room_rate ) ) {
			return new Json_Response( [
				'status'  => 'error',
				'message' => $room_rate->get_error_message(),
			], 400 );
		}

		$data = [
			'prices'                => $room_rate->get_prices(),
			'breakdown'             => $room_rate->get_breakdown()->all(),
			'additional_rates'      => $room_rate->get_additional_rates(),
			'additional_breakdowns' => array_map( function ( $breakdown ) {
				return $breakdown->all();
			}, $room_rate->get_additional_breakdowns() ),
		];

		// @codingStandardsIgnoreLine
		return new Json_Response( [ 'status' => 'success', 'data' => $data ] );
	}

	/**
	 * //
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 */
	protected function fill_booked_request( Request $request ) {
		$booking_item = abrs_get_booking_item( $request->get( 'booked' ) );

		if ( $booking_item instanceof Room_Item ) {
			$request['room_type'] = $booking_item->get( 'room_type_id' );
			$request['rate_plan'] = $booking_item->get( 'rate_plan_id' );

			foreach ( [ 'adults', 'infants', 'children', 'check_in', 'check_out' ] as $key ) {
				if ( ! $request->has( $key ) ) {
					$request[ $key ] = $booking_item->get( $key );
				}
			}
		}
	}
}
