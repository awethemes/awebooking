<?php
namespace AweBooking\Reservation;

use Awethemes\Http\Request;
use AweBooking\Component\Http\Exceptions\NonceMismatchException;

class Checkout {
	/**
	 * Process the checkout.
	 *
	 * @param  \Awethemes\Http\Request $request The Http request.
	 * @return void
	 *
	 * @throws NonceMismatchException
	 */
	public function process( Request $request ) {
		if ( ! $request->filled( '_wpnonce' ) || ! wp_verify_nonce( $request->get( '_wpnonce' ), 'awebooking_process_checkout' ) ) {
			throw new NonceMismatchException( esc_html__( 'We were unable to process your booking, please try again.', 'awebooking' ) );
		}

		abrs_set_time_limit( 0 );
		if ( ! defined( 'AWEBOOKING_CHECKOUT' ) ) {
			define( 'AWEBOOKING_CHECKOUT', true );
		}

		do_action( 'awebooking/before_checkout_process' );

		if ( WC()->cart->is_empty() ) {
			throw new Exception( sprintf( __( 'Sorry, your session has expired. <a href="%s" class="wc-backward">Return to shop</a>', 'awebooking' ), esc_url( wc_get_page_permalink( 'shop' ) ) ) );
		}

		do_action( 'awebooking/checkout_process' );

		$errors      = new WP_Error();
		$posted_data = $this->get_posted_data();

		// Update session for customer and totals.
		$this->update_session( $posted_data );

		// Validate posted data and cart items before proceeding.
		$this->validate_checkout( $posted_data, $errors );

		foreach ( $errors->get_error_messages() as $message ) {
			wc_add_notice( $message, 'error' );
		}

		if ( empty( $posted_data['awebooking_checkout_update_totals'] ) && 0 === wc_notice_count( 'error' ) ) {
			$this->process_customer( $posted_data );
			$order_id = $this->create_order( $posted_data );
			$order    = wc_get_order( $order_id );

			if ( is_wp_error( $order_id ) ) {
				throw new Exception( $order_id->get_error_message() );
			}

			do_action( 'awebooking_checkout_order_processed', $order_id, $posted_data, $order );

			if ( WC()->cart->needs_payment() ) {
				$this->process_order_payment( $order_id, $posted_data['payment_method'] );
			} else {
				$this->process_order_without_payment( $order_id );
			}
		}
	}
}
