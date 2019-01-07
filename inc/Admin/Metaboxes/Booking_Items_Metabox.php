<?php

namespace AweBooking\Admin\Metaboxes;

use AweBooking\Constants;
use AweBooking\Admin\Metabox;
use WPLibs\Http\Request;

class Booking_Items_Metabox extends Metabox {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id      = 'awebooking-booking-rooms';
		$this->title   = esc_html__( 'Booking Rooms', 'awebooking' );
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

		include trailingslashit( __DIR__ ) . 'views/html-booking-items.php';
	}

	/**
	 * Handle save the the metabox.
	 *
	 * @param \WP_Post                $post    The WP_Post object instance.
	 * @param \WPLibs\Http\Request $request The HTTP Request.
	 */
	public function save( $post, Request $request ) {
		if ( $request->has( 'awebooking-calculate-totals' ) ) {
			abrs_get_booking( $post )->calculate_totals();
		}
	}
}
