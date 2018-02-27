<?php
namespace AweBooking\Http\Controllers;

use Awethemes\Http\Request;

class Checkout_Controller extends Controller {
	/**
	 * Process checkout action.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function process( Request $request ) {
		try {
			$request->verify_nonce( '_wpnonce', 'awebooking-checkout' );
		} catch ( \Exception $e ) {
			$this->notices( 'error', esc_html__( 'We were unable to process your reservation, please try again.', 'awebooking' ) );
			return $this->redirect()->back();
		}

		try {
			$this->validate_form( $request );
		} catch ( \Exception $e ) {
			$this->notices( 'error', $e->getMessage() );
			return $this->redirect()->back()->with_input();
		}

		return;
	}

	protected function validate_form( Request $request ) {
		// Do validator the input before doing checkout.
		$rules = apply_filters( 'awebooking/checkout/validator_rules', [
			'customer_first_name' => 'required',
			'customer_last_name'  => 'required',
			'customer_email'      => 'required|email',
			'customer_phone'      => 'required|numeric',
		]);

		$labels = apply_filters( 'awebooking/checkout/validator_labels', [
			'customer_first_name' => esc_html__( 'First name', 'awebooking' ),
			'customer_last_name'  => esc_html__( 'Last name', 'awebooking' ),
			'customer_email'      => esc_html__( 'Email address', 'awebooking' ),
			'customer_phone'      => esc_html__( 'Phone number', 'awebooking' ),
		]);

		$request->validate( $rules, $labels );
	}

	public function completed( Request $request ) {
		return $this->response()->view( 'checkout/completed.php' );
	}
}
