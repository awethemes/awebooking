<?php
namespace AweBooking\Notification;

use AweBooking\Mail\Mailable;

class Booking_Created extends Mailable {
	protected $booking;
	protected $avai;

	public function __construct( $booking, $avai ) {
		$this->booking = $booking;
		$this->avai = $avai;
	}

	/**
	 * Build the message.
	 *
	 * @return mixed
	 */
	public function build() {
		return $this->template( 'new-booking', [
			'booking_id' => $this->booking,
			'room_name'  => $this->avai->get_room_type()->get_title(),
			'check_in'   => $this->avai->get_check_in()->format( 'Y-m-d' ),
			'check_out'  => $this->avai->get_check_out()->format( 'Y-m-d' ),
			'price'      => (string) $this->avai->get_total_price(),
		]);
	}
}
