<?php
namespace AweBooking\Frontend\Shortcodes;

use AweBooking\Reservation\Reservation;

class Checkout_Shortcode extends Shortcode_Abstract {
	/**
	 * The reservation instance.
	 *
	 * @var \AweBooking\Reservation\Reservation
	 */
	protected $reservation;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 */
	public function __construct( Reservation $reservation ) {
		$this->reservation = $reservation;
	}

	/**
	 * {@inheritdoc}
	 */
	public function output( $request ) {
		if ( $request->filled( 'booking-received' ) ) {
			$this->output_received( $request );
		} else {
			$this->output_checkout( $request );
		}
	}

	/**
	 * Show the booking received.
	 *
	 * @param \Awethemes\Http\Request $request Current http request.
	 * @return void
	 */
	public function output_received( $request ) {
		$booking_id = apply_filters( 'abrs_thankyou_booking_id', absint( $request->get( 'booking-received' ) ) );

		$booking = abrs_get_booking( $booking_id );

		// Empty the awaiting payment in the session.
		abrs_session()->remove( 'booking_awaiting_payment' );

		// Flush the current reservation.
		$this->reservation->flush();

		abrs_get_template( 'checkout/thankyou.php', compact( 'booking' ) );
	}

	/**
	 * Show the checkout.
	 *
	 * @param \Awethemes\Http\Request $request Current http request.
	 * @return void
	 */
	public function output_checkout( $request ) {
		if ( $this->reservation->is_empty() ) {
			return;
		}

		// TODO: ...

		abrs_get_template( 'checkout/checkout.php', compact( 'request' ) );
	}
}
