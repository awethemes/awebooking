<?php
namespace AweBooking\Admin;

use WP_Error;
use AweBooking\Factory;
use AweBooking\Concierge;
use AweBooking\Hotel\Room;
use AweBooking\Support\Period;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Admin\Forms\Add_Line_Item_Form;
use AweBooking\Admin\Forms\Edit_Line_Item_Form;
use AweBooking\Admin\Calendar\Yearly_Calendar;

class Admin_Ajax {
	/**
	 * Run admin ajax hooks.
	 */
	public function __construct() {
		// Calendar ajax.
		add_action( 'wp_ajax_get_awebooking_yearly_calendar', [ $this, 'get_yearly_calendar' ] );
		add_action( 'wp_ajax_set_awebooking_availability', [ $this, 'set_availability' ] );

		// Booking ajax hooks.
		add_action( 'wp_ajax_add_awebooking_line_item', [ $this, 'add_booking_line_item' ] );
		add_action( 'wp_ajax_edit_awebooking_line_item', [ $this, 'edit_booking_line_item' ] );
		add_action( 'wp_ajax_get_awebooking_add_item_form', [ $this, 'get_booking_add_item_form' ] );
		add_action( 'wp_ajax_get_awebooking_edit_line_item_form', [ $this, 'get_edit_line_item_form' ] );

		add_action( 'wp_ajax_awebooking/delete_booking_note', array( $this, 'delete_booking_note' ) );
		add_action( 'wp_ajax_awebooking/add_booking_note', array( $this, 'add_booking_note' ) );
	}

	/**
	 * Gets the add booking form HTML template.
	 *
	 * @return void
	 */
	public function get_booking_add_item_form() {
		if ( empty( $_REQUEST['booking_id'] ) ) {
			wp_send_json_error();
		}

		$the_booking = Factory::get_booking( absint( $_REQUEST['booking_id'] ) );
		if ( ! $the_booking || ! $the_booking->exists() ) {
			wp_send_json_error();
		}

		$form = new Add_Line_Item_Form( $the_booking );
		wp_send_json_success( [ 'html' => $form->contents() ] );
	}

	/**
	 * //
	 *
	 * @return [type] [description]
	 */
	public function get_edit_line_item_form() {
		if ( empty( $_REQUEST['line_item_id'] ) ) {
			wp_send_json_error();
		}

		$line_item = new Line_Item( absint( $_REQUEST['line_item_id'] ) );
		if ( ! $line_item || ! $line_item->exists() ) {
			wp_send_json_error();
		}

		$form = new Edit_Line_Item_Form( $line_item );
		wp_send_json_success( [ 'html' => $form->contents() ] );
	}

	/**
	 * Handler ajax add booking line item.
	 *
	 * @return void
	 */
	public function add_booking_line_item() {
		if ( empty( $_REQUEST['booking_id'] ) ) {
			wp_send_json_error();
		}

		$the_booking = Factory::get_booking( absint( $_REQUEST['booking_id'] ) );
		if ( ! $the_booking || ! $the_booking->exists() ) {
			wp_send_json_error();
		}

		try {
			$form = new Add_Line_Item_Form( $the_booking );
			$response = $form->handle( $_POST, true );

			if ( $response ) {
				wp_send_json_success();
			}

			wp_send_json_error();
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'error' => $e->getMessage() ] );
		}
	}

	/**
	 * Handler ajax edit booking line item.
	 *
	 * @return void
	 */
	public function edit_booking_line_item() {
		if ( empty( $_REQUEST['line_item_id'] ) ) {
			wp_send_json_error();
		}

		$line_item = Factory::get_booking_item( absint( $_REQUEST['line_item_id'] ) );
		if ( ! $line_item || ! $line_item->exists() ) {
			wp_send_json_error();
		}

		try {
			$form = new Edit_Line_Item_Form( $line_item );
			$response = $form->handle( $_POST, true );

			if ( $response ) {
				wp_send_json_success();
			}

			wp_send_json_error();
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'error' => $e->getMessage() ] );
		}
	}

	// ==============

	/**
	 * //
	 *
	 * @return void
	 */
	public function get_yearly_calendar() {
		$room = new Room( absint( $_REQUEST['room'] ) );

		if ( $room->exists() ) {
			$calendar = new Yearly_Calendar( absint( $_REQUEST['year'] ), $room );
			$calendar->display();
		}

		exit;
	}

	public function set_availability() {
		if ( empty( $_REQUEST['start'] ) || empty( $_REQUEST['start'] ) || ! isset( $_REQUEST['state'] ) ) {
			return wp_send_json_error();
		}

		$start = sanitize_text_field( wp_unslash( $_REQUEST['start'] ) );
		$end = sanitize_text_field( wp_unslash( $_REQUEST['end'] ) );

		try {
			$date_period = new Period( $start, $end, false );
			$room = new Room( absint( $_REQUEST['room_id'] ) );

			if ( $room->exists() ) {
				Concierge::set_availability( $room, $date_period, absint( $_REQUEST['state'] ) );
				return wp_send_json_success();
			}
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'error' => $e->getMessage() ] );
		}
	}






	/**
	 * Add booking note via ajax.
	 */
	public function add_booking_note() {
		$booking_id   = absint( $_POST['booking_id'] );
		$note      = wp_kses_post( trim( stripslashes( $_POST['note'] ) ) );
		$note_type = $_POST['note_type'];

		$is_customer_note = ( isset( $note_type ) && ( 'customer' === $note_type ) ) ? 1 : 0;

		if ( $booking_id > 0 ) {
			$booking      = new Booking( $booking_id );
			$comment_id = $booking->add_booking_note( $note, $is_customer_note, true );

			$new_note = '<li rel="' . esc_attr( $comment_id ) . '" class="note ';
			if ( $is_customer_note ) {
				$new_note .= 'customer-note';
			}
			$new_note .= '"><div class="note_content">';
			$new_note .= wpautop( wptexturize( $note ) );
			$new_note .= '</div><p class="meta"><a href="#" class="delete_note">' . __( 'Delete note', 'awebooking' ) . '</a></p>';
			$new_note .= '</li>';

			return wp_send_json_success( [ 'new_note' => $new_note ], 200 );
		}
		wp_die();
	}

	/**
	 * Delete booking note via ajax.
	 *
	 * @param string $booking_id booking ID
	 * @param string $id note ID
	 * @return WP_Error|array error or deleted message
	 */
	/**
	 * This function contains output data.
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
			//
		}
	}
}
