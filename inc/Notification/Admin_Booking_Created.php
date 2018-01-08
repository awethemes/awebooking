<?php
namespace AweBooking\Notification;

use AweBooking\AweBooking;
use AweBooking\Hotel\Room_Type;
use AweBooking\Support\Mailable;
use AweBooking\Support\Formatting;
use AweBooking\Support\Carbonate;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Booking\Items\Service_Item;

class Admin_Booking_Created extends Mailable {
	protected $booking;

	public function __construct( $booking ) {
		$this->booking = $booking;
		// Find/replace.
		$this->find['order_number']    = '{order_number}';
		$this->find['order_date']      = '{order_date}';

		$this->replace['order_number'] = $this->booking->get_id();
		$this->replace['order_date']   = Formatting::date_format( $this->booking->get_booking_date() );
	}

	/**
	 * Get dumy data for email preview.
	 */
	public function dummy() {}

	/**
	 * Build the message.
	 *
	 * @return mixed
	 */
	public function build() {
		return $this->get_template( 'admin-new-booking', [
			'booking_id'           => $this->booking->get_id(),
			'booking'              => $this->booking,
			'booking_room_units'   => $this->booking->get_line_items(),
			'total_price'          => (string) $this->booking->get_total(),
			'customer_first_name'  => $this->booking['customer_first_name'],
			'customer_last_name'   => $this->booking['customer_last_name'],
			'customer_email'       => $this->booking->get_customer_email(),
			'customer_phone'       => $this->booking['customer_phone'],
			'customer_company'     => $this->booking->get_customer_company(),
			'customer_note'        => $this->booking['customer_note'],
		]);
	}

	/**
	 * Get email subject.
	 *
	 * @return void
	 */
	public function get_subject() {
		$subject = awebooking_option( 'email_new_subject' );

		return $this->format_string( $subject );
	}

	/**
	 * Get email heading.
	 *
	 * @return void
	 */
	public function get_heading() {}
}
