<?php

namespace AweBooking\Checkout;

use AweBooking\Model\Booking;

class Url_Generator {
	/**
	 * The booking instance.
	 *
	 * @var \AweBooking\Model\Booking
	 */
	protected $booking;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Booking $booking The booking instance.
	 */
	public function __construct( Booking $booking ) {
		$this->booking = $booking;
	}

	/**
	 * Generates a URL for the thanks page (booking received).
	 *
	 * @return string
	 */
	public function get_booking_received_url() {
		$received_url = add_query_arg([
			'booking-received' => $this->booking->get_id(),
			'token'            => $this->booking->get_public_token(),
		], abrs_get_page_permalink( 'checkout' ) );

		if ( abrs_get_option( 'force_ssl_checkout' ) || is_ssl() ) {
			$received_url = str_replace( 'http:', 'https:', $received_url );
		}

		return apply_filters( 'abrs_get_booking_received_url', $received_url, $this->booking );
	}
}
