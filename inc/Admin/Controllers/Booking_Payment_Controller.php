<?php
namespace AweBooking\Admin\Controllers;

use AweBooking\Model\Booking;
use AweBooking\Model\Booking_Item;
use AweBooking\Model\Booking_Payment_Item;
use AweBooking\Admin\Forms\Create_Payment_Form;
use AweBooking\Support\Utils as U;
use Awethemes\Http\Request;

class Booking_Payment_Controller extends Controller {
	/**
	 * Handle store the settings.
	 *
	 * @param  \Awethemes\Http\Request   $request The current request.
	 * @param  \AweBooking\Model\Booking $booking The booking reference.
	 * @return \Awethemes\Http\Response
	 */
	public function create( Request $request, Booking $booking ) {
		get_current_screen()->action = 'create_payment_item';

		$controls = new Create_Payment_Form;
		$controls['amount']->set_value( $booking->get_balance_due() );

		return $this->response_view( 'booking/payment-form.php', compact( 'booking', 'controls' ) );
	}

	/**
	 * Handle store.
	 *
	 * @param  \Awethemes\Http\Request   $request The current request.
	 * @param  \AweBooking\Model\Booking $booking The booking reference.
	 * @return \Awethemes\Http\Response
	 */
	public function store( Request $request, Booking $booking ) {
		$request->verify_nonce( '_wpnonce', 'create_booking_payment' );

		try {
			$input = ( new Create_Payment_Form )->handle( $request->all() );
		} catch ( \Exception $e ) {
			$this->notices( 'error', $e->getMessage() );

			return $this->redirect()->back()
				->only_input( 'amount', 'payment_method', 'comment' );
		}

		// Store the payment item.
		$payment_item = ( new Booking_Payment_Item )->fill(
			$request->only( 'method', 'amount', 'comment', 'transaction_id' )
		);

		// Map the booking ID to the payment_item ID.
		$payment_item['booking_id'] = $booking->get_id();

		if ( $payment_item->save() ) {
			$this->notices()->success( esc_html__( 'Added new payment successfully!', 'awebooking' ) );
		} else {
			$this->notices()->warning( esc_html__( 'Error when add payment', 'awebooking' ) );
		}

		return $this->redirect()->to( $booking->get_edit_url() );
	}

	/**
	 * Show edit for.
	 *
	 * @param  \Awethemes\Http\Request                $request      The current request.
	 * @param  \AweBooking\Model\Booking              $booking      The booking reference.
	 * @param  \AweBooking\Model\Booking_Payment_Item $payment_item The payment item.
	 * @return \Awethemes\Http\Response
	 */
	public function edit( Request $request, Booking $booking, Booking_Payment_Item $payment_item ) {
		static::assert_item_in_booking( $booking, $payment_item );

		$controls = new Create_Payment_Form;
		$controls->fill( $payment_item->only( 'method', 'amount', 'comment' ) );

		get_current_screen()->action = 'edit_payment_item';

		return $this->response_view( 'booking/payment-form.php', compact( 'booking', 'controls', 'payment_item' ) );
	}

	/**
	 * Perform update a payment item.
	 *
	 * @param  \Awethemes\Http\Request                $request      The current request.
	 * @param  \AweBooking\Model\Booking              $booking      The booking reference.
	 * @param  \AweBooking\Model\Booking_Payment_Item $payment_item The payment item.
	 * @return \Awethemes\Http\Response
	 */
	public function update( Request $request, Booking $booking, Booking_Payment_Item $payment_item ) {
		static::assert_item_in_booking( $booking, $payment_item );

		$request->verify_nonce( '_wpnonce', 'update_booking_payment_' . $payment_item->get_id() );

		try {
			$input = ( new Create_Payment_Form )->handle( $request->all() );
		} catch ( \Exception $e ) {
			$this->notices( 'error', $e->getMessage() );

			return $this->redirect()
				->back( $payment_item->get_edit_link() )
				->only_input( 'amount', 'payment_method', 'comment' );
		}

		$payment_item
			->fill( $request->only( 'method', 'amount', 'comment', 'transaction_id' ) )
			->save();

		$this->notices( 'info', esc_html__( 'Payment item has been successfully updated!', 'awebooking' ) );

		return $this->redirect()->to( $booking->get_edit_url() );
	}

	/**
	 * Perform delete a payment item.
	 *
	 * @param  \Awethemes\Http\Request                $request      The current request.
	 * @param  \AweBooking\Model\Booking              $booking      The booking reference.
	 * @param  \AweBooking\Model\Booking_Payment_Item $payment_item The payment item.
	 * @return \Awethemes\Http\Response
	 */
	public function destroy( Request $request, Booking $booking, Booking_Payment_Item $payment_item ) {
		static::assert_item_in_booking( $booking, $payment_item );

		$request->verify_nonce( '_wpnonce', 'delete_payment_item_' . $payment_item->get_id() );

		// TODO: Need more checks before delete item.
		$payment_item->delete();

		$this->notices( 'info', esc_html__( 'The payment item has been deleted', 'awebooking' ) );

		return $this->redirect()->back( $booking->get_edit_url() );
	}

	/**
	 * Assert a payment_item in a booking.
	 *
	 * @param  \AweBooking\Model\Booking              $booking      The booking reference.
	 * @param  \AweBooking\Model\Booking_Payment_Item $payment_item The payment item.
	 * @return true
	 *
	 * @throws \InvalidArgumentException
	 */
	protected static function assert_item_in_booking( Booking $booking, Booking_Payment_Item $payment_item ) {
		if ( $booking->get_id() === $payment_item->get_booking_id() ) {
			return true;
		}

		throw new \InvalidArgumentException( esc_html__( 'Invalid booking data', 'awebooking' ) );
	}
}
