<?php

namespace AweBooking\Admin\Metaboxes;

use AweBooking\Constants;
use AweBooking\Admin\Metabox;

class Booking_Payments_Metabox extends Metabox {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id      = 'awebooking-booking-payments';
		$this->title   = esc_html__( 'Payments', 'awebooking' );
		$this->screen  = Constants::BOOKING;
	}

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

		include trailingslashit( __DIR__ ) . 'views/html-booking-payments.php';
	}
}
