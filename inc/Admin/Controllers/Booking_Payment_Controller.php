<?php
namespace AweBooking\Admin\Controllers;

use WP_Error;
use Awethemes\Http\Request;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking\Payment_Item;
use AweBooking\Admin\Forms\Booking_Payment_Form;

class Booking_Payment_Controller extends Controller {
	/**
	 * Handle create new payment.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function create( Request $request ) {
		if ( ! $request->filled( 'refer' ) || ! $booking = abrs_get_booking( $request['refer'] ) ) {
			return $this->whoops();
		}

		// Create an empty form.
		$form_builder = new Booking_Payment_Form(
			$payment_item = new Payment_Item
		);

		// Set the default amount.
		$balance_due = abrs_decimal( $booking->get( 'balance_due' ) );
		$form_builder['amount']->set_value( $balance_due->is_positive() ? $balance_due : 0 );

		$page_title = $payment_item->exists()
			? esc_html__( 'Update Payment', 'awebooking' )
			: esc_html__( 'Add Payment', 'awebooking' );

		return $this->response( 'booking/payment-form.php', compact( 'page_title', 'booking', 'payment_item', 'form_builder' ) );
	}

	/**
	 * Handle store new booking payment.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function store( Request $request ) {
		check_admin_referer( 'create_booking_payment', '_wpnonce' );

		if ( ! $request->filled( '_refer' ) || ! $booking = abrs_get_booking( $request['_refer'] ) ) {
			return $this->whoops();
		}

		// Create new empty payment item.
		$payment_item = new Payment_Item;
		$payment_item['booking_id'] = $booking->get_id();

		// Handle the request.
		$sanitized = ( new Booking_Payment_Form( $payment_item ) )->handle( $request );
		$payment_item->fill( $sanitized->get_attributes() );

		if ( $payment_item['amount'] > 0 && $payment_item->save() ) {
			abrs_admin_notices( esc_html__( 'Added new payment successfully!', 'awebooking' ), 'success' )->dialog();
		} else {
			abrs_admin_notices( esc_html__( 'Error when add payment', 'awebooking' ), 'error' )->dialog();
		}

		return $this->redirect()->to( get_edit_post_link( $booking->get_id(), 'raw' ) );
	}

	/**
	 * Show edit for.
	 *
	 * @param  \Awethemes\Http\Request                $request      The current request.
	 * @param  \AweBooking\Model\Booking\Payment_Item $payment_item The booking payment item.
	 * @return \Awethemes\Http\Response
	 */
	public function edit( Request $request, Payment_Item $payment_item ) {
		if ( ! $booking = abrs_get_booking( $payment_item->booking_id ) ) {
			return $this->whoops();
		}

		// Create the booking payment form.
		$form_builder = new Booking_Payment_Form( $payment_item );

		return $this->response( 'booking/payment-form.php', compact( 'booking', 'payment_item', 'form_builder' ) );
	}

	/**
	 * Perform update a payment item.
	 *
	 * @param  \Awethemes\Http\Request                $request      The current request.
	 * @param  \AweBooking\Model\Booking\Payment_Item $payment_item The booking payment item.
	 * @return \Awethemes\Http\Response
	 */
	public function update( Request $request, Payment_Item $payment_item ) {
		check_admin_referer( 'update_payment_' . $payment_item->get_id(), '_wpnonce' );

		if ( ! $booking = abrs_get_booking( $payment_item->booking_id ) ) {
			return $this->whoops();
		}

		$sanitized = ( new Booking_Payment_Form( $payment_item ) )->handle( $request );

		if ( $sanitized->count() > 0 ) {
			$payment_item->fill( $sanitized->get_attributes() );
		}

		$payment_item->save();

		abrs_admin_notices( esc_html__( 'Payment item has been updated successfully!', 'awebooking' ), 'success' )->dialog();

		return $this->redirect()->to( get_edit_post_link( $booking->get_id(), 'raw' ) );
	}

	/**
	 * Perform delete a payment item.
	 *
	 * @param  \Awethemes\Http\Request                $request      The current request.
	 * @param  \AweBooking\Model\Booking\Payment_Item $payment_item The booking payment item.
	 * @return \Awethemes\Http\Response
	 */
	public function destroy( Request $request, Payment_Item $payment_item ) {
		check_admin_referer( 'delete_payment_' . $payment_item->get_id(), '_wpnonce' );

		abrs_delete_booking_item( $payment_item );

		abrs_admin_notices( esc_html__( 'The payment has been destroyed!', 'awebooking' ), 'info' )->dialog();

		return $this->redirect()->back( get_edit_post_link( $payment_item['booking_id'], 'raw' ) );
	}

	/**
	 * Just return a WP_Error for request invalid booking ID.
	 *
	 * @return \WP_Error
	 */
	protected function whoops() {
		return new WP_Error( 404,
			esc_html__( 'You attempted to working with a booking that doesn’t exist. Perhaps it was deleted?', 'awebooking' )
		);
	}
}
