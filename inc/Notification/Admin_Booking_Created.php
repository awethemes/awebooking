<?php
namespace AweBooking\Notification;

use AweBooking\AweBooking;
use AweBooking\Support\Mail\Mailable;
use AweBooking\Support\Formatting;

class Admin_Booking_Created extends Mailable {
	protected $booking;
	protected $avai;

	public function __construct( $booking, $avai ) {
		$this->booking = $booking;
		$this->avai = $avai;
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
		$extra_services = $this->avai->get_request()->get_request( 'extra_services' );
		$extra_services_name = [];
		if ( $extra_services ) {
			foreach ( $extra_services as $key => $id ) {
				$term = get_term( $id, AweBooking::HOTEL_SERVICE );
				$extra_services_name[] = $term->name;
			}
		}

		return $this->get_template( 'admin-new-booking', [
			'booking_id'           => $this->booking->get_id(),
			'room_name'            => $this->avai->get_room_type()->get_title(),
			'check_in'             => $this->avai->get_check_in()->format( 'Y-m-d' ),
			'check_out'            => $this->avai->get_check_out()->format( 'Y-m-d' ),
			'nights'               => $this->avai->get_nights(),
			'extra_services_name'  => $extra_services_name,
			'room_type_price'      => (string) $this->avai->get_price(),
			'extra_services_price' => (string) $this->avai->get_extra_services_price(),
			'total_price'          => (string) $this->avai->get_total_price(),
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
