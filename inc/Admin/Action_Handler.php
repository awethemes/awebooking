<?php
namespace AweBooking\Admin;

use AweBooking\Factory;
use AweBooking\AweBooking;
use Skeleton\Support\Validator;

class Action_Handler {
	/**
	 * Constructor actions handler.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/post_action_action/
	 */
	public function __construct() {
		add_action( 'post_action_delete_awebooking_item', [ $this, 'delete_booking_item' ] );
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
		$the_booking->remove_item( $booking_item );
		$the_booking->save();

		$the_booking->calculate_totals();

		awebooking( 'admin_notices' )->info(
			sprintf( esc_html__( 'The booking item #%s has been deleted.', 'awebooking' ), esc_html( $post_id ) )
		);

		wp_redirect( $the_booking->get_edit_url() );
		exit;
	}
}
