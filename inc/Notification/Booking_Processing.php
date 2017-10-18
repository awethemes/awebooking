<?php
namespace AweBooking\Notification;

use AweBooking\AweBooking;
use AweBooking\Hotel\Room_Type;
use AweBooking\Support\Mailable;
use AweBooking\Support\Formatting;
use AweBooking\Support\Carbonate;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Booking\Items\Service_Item;

class Booking_Processing extends Mailable {
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
		$line_item = new Line_Item;
		$line_item['name'] = 'Dummy Room';
		$line_item['price'] = 80;
		$line_item['adults'] = 2;
		$line_item['children'] = 1;
		$line_item['check_in'] = '2017-08-30';
		$line_item['check_out'] = '2017-09-01';
		$service_item = new Service_Item;
		$service_item['name'] = 'Breakfast';
		$service_item['price'] = 10;
		$this->booking->add_item( $line_item );
		$this->booking->add_item( $service_item );
		$booking_room_units = $this->booking->get_line_items();

		return $this->get_template( 'processing-booking', apply_filters( 'awebooking/new_email_dummy_data', [
			'booking_id'           => 1,
			'booking'              => $this->booking,
			'booking_room_units'   => $booking_room_units,
			'total_price'          => (string) $this->booking->get_total(),
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
		return $this->get_template( 'processing-booking', [
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
		$subject = awebooking_option( 'email_processing_subject' );
		return $this->format_string( $subject );
	}

	/**
	 * Get email heading.
	 *
	 * @return void
	 */
	public function get_heading() {}
}
