<?php
namespace AweBooking\Notification;

use AweBooking\AweBooking;
use AweBooking\Hotel\Room_Type;
use AweBooking\Support\Mail\Mailable;
use AweBooking\Support\Formatting;
use AweBooking\Support\Carbonate;

class Booking_Created extends Mailable {
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
	public function dummy() {
		return $this->get_template( 'new-booking', apply_filters( 'awebooking/new_email_dummy_data', [
			'booking_id'           => 1,
			'room_name'            => esc_html__( 'Dummy Room', 'awebooking' ),
			'check_in'             => Carbonate::create_date( '2017-08-29 07:20:09' )->format( 'Y/m/d' ),
			'check_out'            => Carbonate::create_date( '2017-08-31 07:20:09' )->format( 'Y/m/d' ),
			'nights'               => 3,
			'extra_services_name'  => array( esc_html__( 'Dummy Service 1' ), esc_html__( 'Dummy Service 2' ) ),
			'room_type_price'      => '50 $',
			'extra_services_price' => '10 $',
			'total_price'          => '60 $',
			'customer_first_name'  => 'John',
			'customer_last_name'   => 'Cena',
			'customer_email'       => 'customer@email.com',
			'customer_phone'       => '+84xxxxxxxx',
			'customer_company'     => 'AweThemes',
			'customer_note'        => 'The email preview',
		] ) );
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

		return $this->get_template( 'new-booking', [
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
