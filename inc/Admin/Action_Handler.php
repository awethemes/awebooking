<?php
namespace AweBooking\Admin;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Booking_Room_Item;
use AweBooking\Support\Date_Period;
use AweBooking\Admin\Forms\Add_Booking_Form;
use Skeleton\Support\Validator;

class Action_Handler {
	/**
	 * Constructor actions handler.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/post_action_action/
	 */
	public function __construct() {
		add_action( 'post_action_delete_awebooking_item', [ $this, 'delete_booking_item' ] );
		add_action( 'post_action_add_awebooking_room_item', [ $this, 'add_booking_room_item' ] );
	}

	/**
	 * Handle add booking room item.
	 *
	 * @param  int $post_id Raw post_id from request.
	 * @return void
	 */
	public function add_booking_room_item( $post_id ) {
		// Check post type correctly first.
		if ( AweBooking::BOOKING !== get_post_type( $post_id ) ) {
			wp_die( esc_html__( 'Invalid post type.', 'awebooking' ) );
		}

		// Create the form and check admin refererent.
		$form = new Add_Booking_Form;
		check_admin_referer( $form->nonce(), $form->nonce() );

		$redirect_back = admin_url( 'admin.php?page=awebooking-add-item&booking=' . $post_id );

		// Get sanitized values from input data.
		$input_data = $form->get_sanitized_values( $_POST );

		if ( empty( $input_data ) || $form->fails() ) {
			awebooking( 'admin_notices' )->error( esc_html__( 'Missing input data', 'awebooking' ) );

			wp_redirect( $redirect_back );
			exit;
		}

		try {
			$period = new Date_Period( $input_data['check_in_out'][0], $input_data['check_in_out'][1] );
		} catch ( \Exception $e ) {
			wp_die( esc_html__( 'Invalid date period.', 'awebooking' ) );
		}

		// Get objects from input.
		$the_room = Factory::get_room_unit( $input_data['add_room'] );
		$the_booking = Factory::get_booking( $post_id );

		if ( ! $the_room->exists() || ! $the_booking->exists() ) {
			wp_die( esc_html__( 'Something went wrong.', 'awebooking' ) );
		}

		if ( $the_room->is_free( $period ) ) {
			$item = new Booking_Room_Item;

			$item['room_id']   = $the_room->get_id();
			$item['name']      = $the_room->get_room_type()->get_title();
			$item['check_in']  = $period->get_start_date()->toDateString();
			$item['check_out'] = $period->get_end_date()->toDateString();
			$item['adults']    = isset( $input_data['adults'] ) ? absint( $input_data['adults'] ): 1;
			$item['children']  = isset( $input_data['children'] ) ? absint( $input_data['children'] ) : 0;
			$item['total']     = awebooking_sanitize_price( $input_data['price'] );

			$the_booking->add_item( $item );
			$the_booking->save();

			wp_redirect( get_edit_post_link( $the_booking->get_id(), 'link' ) );
			exit;
		}

		$redirect_back = add_query_arg([
			'booking'   => $the_booking->get_id(),
			'add_room'  => $the_room->get_id(),
			'check_in'  => $period->get_start_date()->toDateString(),
			'check_out' => $period->get_end_date()->toDateString(),
		], $redirect_back );

		wp_redirect( $redirect_back );
		exit;
	}

	/**
	 * Handle delete booking item.
	 *
	 * @param  int $post_id Raw post_id from request.
	 * @return void
	 */
	public function delete_booking_item( $post_id ) {
		check_admin_referer( 'delete_item_awebooking_' . $post_id );

		if ( AweBooking::BOOKING !== get_post_type( $post_id ) ) {
			wp_die( esc_html__( 'Invalid post type.', 'awebooking' ) );
		}

		if ( empty( $_REQUEST['item'] ) && ! is_int( $_REQUEST['item'] ) ) {
			wp_die( esc_html__( 'You are wrong somewhere, please try again.', 'awebooking' ) );
		}

		$the_booking = Factory::get_booking( $post_id );
		if ( ! $the_booking->exists() ) {
			wp_die( esc_html__( 'The booking not found.', 'awebooking' ) );
		}

		$booking_item = $the_booking->get_item( (int) $_REQUEST['item'] );
		if ( is_null( $booking_item ) ) {
			wp_die( esc_html__( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?', 'awebooking' ) );
		}

		if ( isset( $_REQUEST['item_type'] ) && $booking_item->get_type() !== $_REQUEST['item_type'] ) {
			wp_die( esc_html__( 'Invalid booking item type to delete.', 'awebooking' ) );
		}

		// Delete the booking item.
		$booking_item->delete();

		wp_redirect( $the_booking->get_edit_url() );
		exit;
	}
}
