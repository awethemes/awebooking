<?php
namespace AweBooking\Admin;

use WP_Error;
use AweBooking\Factory;
use AweBooking\Concierge;
use AweBooking\AweBooking;
use AweBooking\Hotel\Room;
use AweBooking\Hotel\Room_Type;
use AweBooking\Hotel\Service;
use AweBooking\Support\Period;
use AweBooking\Booking\Request;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Admin\Forms\Add_Line_Item_Form;
use AweBooking\Admin\Forms\Edit_Line_Item_Form;
use AweBooking\Admin\Calendar\Yearly_Calendar;
use Skeleton\Support\Validator;
use AweBooking\Pricing\Price;
use AweBooking\Pricing\Price_Calculator;
use AweBooking\Calculator\Service_Calculator;

class Admin_Ajax {
	/**
	 * Run admin ajax hooks.
	 */
	public function __construct() {
		// Calendar ajax.
		add_action( 'wp_ajax_get_awebooking_yearly_calendar', [ $this, 'get_yearly_calendar' ] );
		add_action( 'wp_ajax_set_awebooking_availability', [ $this, 'set_availability' ] );

		// Service ajax.
		add_action( 'wp_ajax_add_awebooking_service', [ $this, 'add_service' ] );

		// Booking ajax hooks.
		add_action( 'wp_ajax_add_awebooking_line_item', [ $this, 'add_booking_line_item' ] );
		add_action( 'wp_ajax_edit_awebooking_line_item', [ $this, 'edit_booking_line_item' ] );
		add_action( 'wp_ajax_get_awebooking_add_item_form', [ $this, 'get_booking_add_item_form' ] );
		add_action( 'wp_ajax_get_awebooking_edit_line_item_form', [ $this, 'get_edit_line_item_form' ] );
		add_action( 'wp_ajax_awebooking_calculate_line_item_total', [ $this, 'calculate_add_line_item_total' ] );
		add_action( 'wp_ajax_awebooking_calculate_update_line_item_total', [ $this, 'calculate_update_line_item_total' ] );

		add_action( 'wp_ajax_add_awebooking_note', array( $this, 'add_booking_note' ) );
		add_action( 'wp_ajax_delete_awebooking_note', array( $this, 'delete_booking_note' ) );

		// Miscs.
		add_action( 'wp_ajax_awebooking_json_search_customers', [ $this, 'json_search_customers' ] );
	}

	/**
	 * Calculate line item total cost.
	 *
	 * @return void
	 */
	public function calculate_add_line_item_total() {
		if ( empty( $_REQUEST['booking_id'] ) ) {
			wp_send_json_error();
		}

		$the_booking = Factory::get_booking( absint( $_REQUEST['booking_id'] ) );
		if ( ! $the_booking || ! $the_booking->exists() ) {
			wp_send_json_error();
		}

		try {
			$form = new Add_Line_Item_Form( $the_booking );
			$sanitized = $form->get_sanitized( $_POST, true );

			$room = Factory::get_room_unit( $sanitized['add_room'] );
			$room_type = $room->get_room_type();

			$period = new Period(
				$sanitized['add_check_in_out'][0],
				$sanitized['add_check_in_out'][1]
			);

			$request = new Request( $period, [
				'room-type'  => $room_type->get_id(), // TODO: ...
				'adults'     => $sanitized['add_adults'],
				'children'   => $sanitized['add_children'],
				'extra_services' => isset( $sanitized['add_services'] ) ? $sanitized['add_services'] : [],
			]);

			// Next, call to Concierge and check availability our hotel.
			$availability = Concierge::check_room_type_availability( $room_type, $request );

			if ( $availability->unavailable() ) {
				wp_send_json_error();
			}

			wp_send_json_success( $availability->to_array() );

		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'error' => $e->getMessage() ] );
		}
	}

	public function calculate_update_line_item_total() {
		if ( empty( $_REQUEST['line_item_id'] ) ) {
			wp_send_json_error();
		}

		$line_item = new Line_Item( absint( $_REQUEST['line_item_id'] ) );
		if ( ! $line_item || ! $line_item->exists() ) {
			wp_send_json_error();
		}

		$booking = $line_item->get_booking();
		$room_type = $line_item->get_room_unit()->get_room_type();

		$sanitized = (new Edit_Line_Item_Form( $line_item ))->get_sanitized();
		$current_price = $line_item->get_total();

		if ( ! empty( $sanitized['edit_check_in_out'] ) ) {
			$period = new Period(
				$sanitized['edit_check_in_out'][0],
				$sanitized['edit_check_in_out'][1]
			);
		} else {
			$period = $line_item->get_period();
		}

		$request = new Request( $period, [
			'adults' => $sanitized['edit_adults'],
			'children' => $sanitized['edit_children'],
		]);

		// ----------
		$base_price = Concierge::get_room_price( $room_type, $request );

		$pipes = [];
		foreach ( $sanitized['edit_services'] as $service ) {
			$extra_service = new Service( $service );
			if ( ! $extra_service->exists() ) {
				continue;
			}

			$pipes[] = new Service_Calculator( $extra_service, $request, $booking->get_price( $current_price ) );
		}

		$price = (new Price_Calculator( $base_price ))
			->through( $pipes )
			->process();

		wp_send_json_success( [ 'total' => $price->get_amount(), 'total_display' => (string) $price ] );
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
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'error' => $e->getMessage() ] );
		}

		wp_send_json_error();
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
		if ( empty( $_REQUEST['room'] ) || empty( $_REQUEST['year'] ) ) {
			wp_send_json_error();
		}

		$room = new Room( absint( $_REQUEST['room'] ) );

		if ( $room->exists() ) {
			$calendar = new Yearly_Calendar( $room, absint( $_REQUEST['year'] ) );

			ob_start();
			$calendar->display();
			$contents = ob_get_clean();

			wp_send_json_success( [ 'html' => $contents ] );
		}

		wp_send_json_error();
	}

	public function set_availability() {
		$validator = new Validator( $_POST, [
			'start_date' => 'required|date',
			'end_date'   => 'required|date',
			'room_id'    => 'required|int',
			'state'      => 'required|int',
			'state'      => 'required|int',
			'only_day_options' => 'array',
		]);

		// If have any errors.
		if ( $validator->fails() ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Validation error', 'awebooking' ) ] );
		}

		$only_days = [];
		if ( isset( $_POST['only_day_options'] ) && is_array( $_POST['only_day_options'] ) ) {
			$only_days = array_map( 'absint', $_POST['only_day_options'] );
		}

		try {
			$room = new Room( absint( $_POST['room_id'] ) );

			$period = new Period(
				sanitize_text_field( wp_unslash( $_POST['start_date'] ) ),
				sanitize_text_field( wp_unslash( $_POST['end_date'] ) )
			);

			if ( $room->exists() ) {
				Concierge::set_availability( $room, $period, absint( $_POST['state'] ), [
					'only_days' => $only_days,
				]);

				return wp_send_json_success();
			}
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'error' => $e->getMessage() ] );
		}
	}

	/**
	 * Add a new service to a room-type.
	 *
	 * TODO: Show error log message.
	 *
	 * @return void
	 */
	public function add_service() {
		$validator = new Validator( $_POST, [
			'name'      => 'required',
			'value'     => 'required|numeric',
			'operation' => 'required',
			'type'      => 'required',
			'room_type' => 'required|int|min:1',
		]);

		// If have any errors.
		if ( $validator->fails() ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Validation error', 'awebooking' ) ] );
		}

		$room_type = new Room_Type( absint( $_POST['room_type'] ) );
		if ( ! $room_type->exists() ) {
			wp_send_json_error();
		}

		$service = new Service;
		$service['name']      = sanitize_text_field( wp_unslash( $_POST['name'] ) );
		$service['value']     = sanitize_text_field( wp_unslash( $_POST['value'] ) );
		$service['operation'] = sanitize_text_field( wp_unslash( $_POST['operation'] ) );
		$service['type']      = sanitize_text_field( wp_unslash( $_POST['type'] ) );
		$service->save();

		if ( $service->exists() ) {
			$tt_ids = wp_set_object_terms( $room_type->get_id(), $service->get_id(), AweBooking::HOTEL_SERVICE, true );

			if ( ! is_wp_error( $tt_ids ) ) {
				wp_send_json_success( $service );
			}
		}

		wp_send_json_error();
	}

	/**
	 * Add booking note via ajax.
	 */
	public function add_booking_note() {
		$booking_id = absint( $_POST['booking_id'] );
		$note       = wp_kses_post( trim( stripslashes( $_POST['note'] ) ) );
		$note_type  = isset( $_POST['note_type'] ) ? $_POST['note_type'] : null;

		$is_customer_note = ( isset( $note_type ) && ( 'customer' === $note_type ) ) ? 1 : 0;

		if ( $booking_id > 0 ) {
			$booking      = Factory::get_booking( $booking_id );
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

	/**
	 * Search for customers and return json.
	 */
	public static function json_search_customers() {
		ob_start();

		// check_ajax_referer( 'search-customers', 'security' );

		/*if ( ! current_user_can( 'edit_hotel_bookings' ) ) {
			wp_die( -1 );
		}*/

		$term    = sanitize_text_field( wp_unslash( $_GET['term'] ) );
		$exclude = array();
		$limit   = '';

		if ( empty( $term ) ) {
			wp_die();
		}

		// Search by ID.
		if ( is_numeric( $term ) ) {
			$customer = get_userdata( intval( $term ) );

			// Customer does not exists.
			if ( $customer instanceof \WP_User ) {
				wp_die();
			}

			$ids = array( $customer->ID );
		} else {
			// If search is smaller than 3 characters, limit result set to avoid
			// too many rows being returned.
			if ( 3 > strlen( $term ) ) {
				$limit = 20;
			}

			$ids = $this->search_customers( $term, $limit );
		}

		$found_customers = array();

		if ( ! empty( $_GET['exclude'] ) ) {
			$ids = array_diff( $ids, (array) $_GET['exclude'] );
		}

		foreach ( $ids as $id ) {
			$customer = get_userdata( $id );

			/* translators: 1: user display name 2: user ID 3: user email */
			$found_customers[ $id ] = sprintf(
				esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'awebooking' ),
				$customer->first_name . ' ' . $customer->last_name,
				$customer->ID,
				$customer->user_email
			);
		}

		wp_send_json( apply_filters( 'awebooking_json_search_found_customers', $found_customers ) );
	}

	/**
	 * Search customers and return customer IDs.
	 *
	 * TODO: Move to new class.
	 *
	 * @param  string     $term  //.
	 * @param  int|string $limit //.
	 * @return array
	 */
	public function search_customers( $term, $limit = '' ) {
		$query = new \WP_User_Query( apply_filters( 'awebooking/customer_search_customers', array(
			'search'         => '*' . esc_attr( $term ) . '*',
			'search_columns' => array( 'user_login', 'user_url', 'user_email', 'user_nicename', 'display_name' ),
			'fields'         => 'ID',
			'number'         => $limit,
		), $term, $limit, 'main_query' ) );

		$query2 = new \WP_User_Query( apply_filters( 'awebooking/customer_search_customers', array(
			'fields'         => 'ID',
			'number'         => $limit,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'first_name',
					'value'   => $term,
					'compare' => 'LIKE',
				),
				array(
					'key'     => 'last_name',
					'value'   => $term,
					'compare' => 'LIKE',
				),
			),
		), $term, $limit, 'meta_query' ) );

		$results = wp_parse_id_list(
			array_merge( $query->get_results(), $query2->get_results() )
		);

		if ( $limit && count( $results ) > $limit ) {
			$results = array_slice( $results, 0, $limit );
		}

		return $results;
	}
}
