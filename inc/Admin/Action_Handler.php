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

		$form = new Add_Booking_Form;

		$handled = $form->handle(
			array_merge( $_POST, [ 'booking_id' => $post_id ] )
		);

		if ( ! $handled ) {
			// error message.
		}

		// wp_redirect( $redirect_back );
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
