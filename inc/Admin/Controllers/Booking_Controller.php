<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;
use AweBooking\Model\Booking;

class Note_Controller extends Controller {
	/**
	 * Ajax add a booking note.
	 *
	 * @param  \Awethemes\Http\Request  $request The current request.
	 * @param  \Awethemes\Model\Booking $booking The Booking instance.
	 * @return \Awethemes\Http\Response
	 */
	public function add_note( Request $request, Booking $booking ) {
		$request->validate([
			'note'      => 'required|length_min:2',
			'note_type' => 'optional',
		]);

		$note = wp_kses_post( trim( stripslashes( $request['note'] ) ) );
		$is_customer_note = ( 'customer' === $request['note_type'] ) ? 1 : 0;

		// Insert the note.
		$comment_id = $booking->add_booking_note( $note, $is_customer_note, true );

		// Build response note.
		$new_note = '<li rel="' . esc_attr( $comment_id ) . '" class="note ';
		if ( $is_customer_note ) {
			$new_note .= 'customer-note';
		}

		$new_note .= '"><div class="note_content">';
		$new_note .= wpautop( wptexturize( $note ) );
		$new_note .= '</div><p class="meta"><a href="#" class="delete_note">' . esc_html__( 'Delete note', 'awebooking' ) . '</a></p>';
		$new_note .= '</li>';

		return [ 'new_note' => $new_note ];
	}

	/**
	 * Delete booking note via ajax.
	 */
	public function delete_booking_note() {
		$note_id = sanitize_text_field( $_POST['note_id'] );
		$booking_id = sanitize_text_field( $_POST['booking_id'] );

		try {
			if ( empty( $note_id ) ) {
				return wp_send_json_error( [ 'message' => __( 'Invalid booking note ID', 'awebooking' ) ], 400 );
			}

			// Ensure note ID is valid.
			$note = get_comment( $note_id );

			if ( is_null( $note ) ) {
				return wp_send_json_error( [ 'message' => __( 'A booking note with the provided ID could not be found', 'awebooking' ) ], 404 );
			}

			// Ensure note ID is associated with given order.
			if ( $note->comment_post_ID != $booking_id ) {
				return wp_send_json_error( [ 'message' => __( 'The booking note ID provided is not associated with the booking', 'awebooking' ) ], 400 );
			}

			// Force delete since trashed booking notes could not be managed through comments list table.
			$result = wp_delete_comment( $note->comment_ID, true );

			if ( ! $result ) {
				return wp_send_json_error( [ 'message' => __( 'This booking note cannot be deleted', 'awebooking' ) ], 500 );
			}

			do_action( 'awebooking/api_delete_booking_note', $note->comment_ID, $note_id, $this );

			return wp_send_json_success( [ 'note_id' => $note_id ], 200 );

		} catch ( \Exception $e ) {
			// ...
		}
	}
}
