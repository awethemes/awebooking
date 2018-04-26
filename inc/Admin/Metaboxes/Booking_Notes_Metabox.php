<?php
namespace AweBooking\Admin\Metaboxes;

class Booking_Notes_Metabox {
	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		$notes = abrs_get_booking_notes( [ 'booking_id' => $post->ID ] );

		include trailingslashit( __DIR__ ) . 'views/html-booking-notes.php';
	}
}
