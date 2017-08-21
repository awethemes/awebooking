<?php
namespace AweBooking\Notification;

use AweBooking\AweBooking;
use AweBooking\Hotel\Room_Type;
use AweBooking\Support\Mailable;
use AweBooking\Support\Formatting;
use AweBooking\Support\Carbonate;

class Booking_Completed extends Mailable {
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
	 * Build the message.
	 *
	 * @return mixed
	 */
	public function build() {
		$extra_services = $this->booking['request_services'];
		$extra_services_name = [];
		if ( $extra_services ) {
			foreach ( $extra_services as $id => $quantity ) {
				$term = get_term( $id, AweBooking::HOTEL_SERVICE );
				$extra_services_name[] = $term->name;
			}
		}

		$room_type = new Room_Type( $this->booking['room_type_id'] );

		return $this->get_template( 'completed-booking', [
			'booking_id'           => $this->booking->get_id(),
			'room_name'            => $room_type->get_title(),
			'check_in'             => Carbonate::create_date( $this->booking['check_in'] )->format( 'Y/m/d' ),
			'check_out'            => Carbonate::create_date( $this->booking['check_out'] )->format( 'Y/m/d' ),
			'nights'               => $this->booking->get_nights(),
			'extra_services_name'  => $extra_services_name,
			'room_type_price'      => (string) $this->booking['room_total'],
			'extra_services_price' => (string) $this->booking['services_total'],
			'total_price'          => (string) $this->booking->get_total_price(),
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
