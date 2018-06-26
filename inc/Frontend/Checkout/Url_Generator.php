<?php
namespace AweBooking\Frontend\Checkout;

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
		$received_url = add_query_arg( 'booking-received', $this->booking->get_id(), abrs_get_page_permalink( 'checkout' ) );

		if ( abrs_get_option( 'force_ssl_checkout' ) || is_ssl() ) {
			$received_url = str_replace( 'http:', 'https:', $received_url );
		}

		return apply_filters( 'abrs_get_booking_received_url', $received_url, $this->booking );
	}

	/**
	 * //TODO
	 * Generates a URL so that a customer can pay for their (unpaid - pending) booking.
	 *
	 * Pass 'true' for the checkout version which doesn't offer gateway choices.
	 *
	 * @param  bool $on_checkout If on checkout.
	 * @return string
	 */
	public function get_checkout_payment_url( $on_checkout = false ) {
		$pay_url = wc_get_endpoint_url( 'booking-pay', $this->get_id(), wc_get_page_permalink( 'checkout' ) );

		if ( 'yes' === get_option( 'woocommerce_force_ssl_checkout' ) || is_ssl() ) {
			$pay_url = str_replace( 'http:', 'https:', $pay_url );
		}

		if ( $on_checkout ) {
			$pay_url = add_query_arg( 'key', $this->get_booking_key(), $pay_url );
		} else {
			$pay_url = add_query_arg(
				array(
					'pay_for_booking' => 'true',
					'key'           => $this->get_booking_key(),
				), $pay_url
			);
		}

		return apply_filters( 'woocommerce_get_checkout_payment_url', $pay_url, $this );
	}

	/**
	 * //TODO
	 * Generates a URL so that a customer can cancel their (unpaid - pending) booking.
	 *
	 * @param string $redirect Redirect URL.
	 * @return string
	 */
	public function get_cancel_booking_url( $redirect = '' ) {
		return apply_filters(
			'woocommerce_get_cancel_booking_url', wp_nonce_url(
				add_query_arg(
					array(
						'cancel_booking' => 'true',
						'booking'        => $this->get_booking_key(),
						'booking_id'     => $this->get_id(),
						'redirect'     => $redirect,
					), $this->get_cancel_endpoint()
				), 'woocommerce-cancel_booking'
			)
		);
	}

	/**
	 * //TODO
	 * Generates a raw (unescaped) cancel-booking URL for use by payment gateways.
	 *
	 * @param string $redirect Redirect URL.
	 * @return string The unescaped cancel-booking URL.
	 */
	public function get_cancel_booking_url_raw( $redirect = '' ) {
		return apply_filters(
			'woocommerce_get_cancel_booking_url_raw', add_query_arg(
				array(
					'cancel_booking' => 'true',
					'booking'        => $this->get_booking_key(),
					'booking_id'     => $this->get_id(),
					'redirect'     => $redirect,
					'_wpnonce'     => wp_create_nonce( 'woocommerce-cancel_booking' ),
				), $this->get_cancel_endpoint()
			)
		);
	}

	/**
	 * //TODO
	 * Helper method to return the cancel endpoint.
	 *
	 * @return string the cancel endpoint; either the cart page or the home page.
	 */
	public function get_cancel_endpoint() {
		$cancel_endpoint = wc_get_page_permalink( 'cart' );
		if ( ! $cancel_endpoint ) {
			$cancel_endpoint = home_url();
		}

		if ( false === strpos( $cancel_endpoint, '?' ) ) {
			$cancel_endpoint = trailingslashit( $cancel_endpoint );
		}

		return $cancel_endpoint;
	}
}
