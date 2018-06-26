<?php
namespace AweBooking\Admin\Metaboxes;

class Booking_Calendar_Metabox {
	/**
	 * Output the metabox.
	 *
	 * @param \WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		global $the_booking;

		if ( is_null( $the_booking ) ) {
			$the_booking = abrs_get_booking( $post );
		}

		include trailingslashit( __DIR__ ) . 'views/html-booking-calendar.php';
	}
}
