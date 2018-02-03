<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;
use AweBooking\Model\Booking;
use AweBooking\Model\Booking_Item;
use AweBooking\Support\Utils as U;
use AweBooking\Admin\Forms\Create_Payment_Form;
use AweBooking\Model\Booking_Payment_Item;

class Booking_Payment_Controller extends Controller {
	/**
	 * Handle store the settings.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function create( Request $request, Booking $booking ) {
		$controls = new Create_Payment_Form;

		return $this->response_view( 'booking/create-payment.php', compact( 'booking', 'controls' ) );
	}

	/**
	 * Handle store the settings.
	 *
	 * @param  \Awethemes\Http\Request   $request The current request.
	 * @param  \AweBooking\Model\Booking $booking      The booking reference.
	 * @return \Awethemes\Http\Response
	 */
	public function store( Request $request, Booking $booking ) {
		$request->verify_nonce( '_wpnonce', 'create_booking_payment' );

		try {
			$input = ( new Create_Payment_Form )->handle( $request->all() );
		} catch ( \Exception $e ) {
			$this->notices( 'error', $e->getMessage() );
			return $this->redirect()->back( $this->fallback )->only_input( 'amount', 'payment_method' );
		}

		// Store the payment item.
		$payment_item = ( new Booking_Payment_Item )->fill(
			$request->only( 'method', 'amount', 'comment' )
		);

		if ( $request->filled( 'transaction_id' ) ) {
			$payment_item['transaction_id'] = $request['transaction_id'];
		}

		$payment_item['booking_id'] = $booking->get_id();
		$inserted = $payment_item->save();

		if ( $inserted ) {
			$this->notices()->success( esc_html__( 'Added new payment successfully!', 'awebooking' ) );
		} else {
			$this->notices()->warning( esc_html__( 'Error when add payment', 'awebooking' ) );
		}

		return $this->redirect()->to( $booking->get_edit_url() );
	}

	/**
	 * Perform update a payment item.
	 *
	 * @param  \Awethemes\Http\Request                $request      The current request.
	 * @param  \AweBooking\Model\Booking              $booking      The booking reference.
	 * @param  \AweBooking\Model\Booking_Payment_Item $payment_item The payment item.
	 * @return \Awethemes\Http\Response
	 */
	public function edit( Request $request, Booking $booking, Booking_Payment_Item $payment_item ) {
		static::assert_item_in_booking( $booking, $payment_item );

		return $this->redirect()->back( $booking->get_edit_url() );
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

		$request->verify_nonce( '_wpnonce', 'delete_payment_item' );

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
