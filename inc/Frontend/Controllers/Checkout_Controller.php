<?php

namespace AweBooking\Frontend\Controllers;

use WPLibs\Http\Request;
use AweBooking\Checkout\Checkout;
use AweBooking\Checkout\Url_Generator;

class Checkout_Controller {
	/**
	 * Handle checkout.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @return \WPLibs\Http\Response
	 */
	public function checkout( Request $request, Checkout $checkout ) {
		abrs_nocache_headers();

		$checkout_url = abrs_get_page_permalink( 'checkout' );

		// Validate the nonce.
		if ( ! $request->filled( '_wpnonce' ) || ! wp_verify_nonce( $request->get( '_wpnonce' ), 'awebooking_checkout_process' ) ) {
			abrs_add_notice( esc_html__( 'We were unable to process your reservation, please try again.', 'awebooking' ), 'error' );
			return abrs_redirector()->back( $checkout_url );
		}

		try {
			$response = $checkout->process( $request );

			$url_generator = new Url_Generator( $response->get_data() );

			if ( $response->is_redirect() ) {
				return abrs_redirector()->to( $response->get_redirect_url() );
			}

			if ( $response->is_successful() ) {
				return abrs_redirector()->to( $url_generator->get_booking_received_url() );
			}
		} catch ( \AweBooking\Component\Http\Exceptions\ValidationFailedException $e ) {
			$this->handle_validate_exception( $e );
		} catch ( \Exception $e ) {
			abrs_add_notice( $e->getMessage(), 'error' );
		}

		return abrs_redirector()->to( $checkout_url )->with_input();
	}

	/**
	 * Handle the ValidationFailedException.
	 *
	 * @param \AweBooking\Component\Http\Exceptions\ValidationFailedException $e The exception.
	 */
	protected function handle_validate_exception( $e ) {
		$errors = $e->get_errors();

		if ( ! is_wp_error( $errors ) && ! $message = $e->getMessage() ) {
			abrs_add_notice( $message, 'error' );
			return;
		}

		foreach ( $e->get_errors()->get_error_messages() as $message ) {
			abrs_add_notice( $message, 'error' );
		}
	}
}
