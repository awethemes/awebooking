<?php

namespace AweBooking\Admin\Metaboxes;

use AweBooking\Constants;
use AweBooking\Admin\Metabox;

class Booking_Notes_Metabox extends Metabox {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id      = 'awebooking-booking-notes';
		$this->title   = esc_html__( 'Notes', 'awebooking' );
		$this->screen  = Constants::BOOKING;
		$this->context = 'side';
	}

	/**
	 * Output the metabox.
	 *
	 * @param \WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		$notes = abrs_get_booking_notes( [ 'booking_id' => $post->ID ] );

		include trailingslashit( __DIR__ ) . 'views/html-booking-notes.php';
	}
}
