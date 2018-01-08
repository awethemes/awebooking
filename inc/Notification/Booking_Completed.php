<?php
namespace AweBooking\Notification;

use AweBooking\AweBooking;
use AweBooking\Hotel\Room_Type;
use AweBooking\Support\Mailable;
use AweBooking\Support\Formatting;
use AweBooking\Support\Carbonate;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Booking\Items\Service_Item;

class Booking_Completed extends Mailable {
	protected $booking;

	public function __construct( $booking ) {
		$this->booking = $booking;

		$this->find_and_replace();
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

		// if ( $this->get_template( 'new-booking' ) ) {
			$content = $this->get_template( 'completed-booking', apply_filters( 'awebooking/completed_email_dummy_data', [
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
		// } else {
		// 	$content = $this->get_content();
		// }

		return $content;
	}

	/**
	 * Build the message.
	 *
	 * @return mixed
	 */
	public function build() {
		if ( $this->get_template( 'completed-booking' ) ) {
			$content = $this->get_template( 'completed-booking', [
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
			] );
		} else {
			$content = $this->get_content();
		}

		return $content;
	}

	/**
	 * Get email content.
	 *
	 * @return void
	 */
	public function get_content() {
		$content = awebooking_option( 'email_complete_content' );
		return $this->format_string( $content );
	}

	/**
	 * Get email subject.
	 *
	 * @return void
	 */
	public function get_subject() {
		$subject = awebooking_option( 'email_complete_subject' );
		return $this->format_string( $subject );
	}

	/**
	 * Get email heading.
	 *
	 * @return void
	 */
	public function get_heading() {}

	/**
	 * Find and replace.
	 */
	public function find_and_replace() {
		// Find/replace.
		$this->find['order_number']    = '{order_number}';
		$this->replace['order_number'] = $this->booking->get_id();

		$this->find['order_date']      = '{order_date}';
		$this->replace['order_date']   = Formatting::date_format( $this->booking->get_booking_date() );

		$this->find['booking_id']    = '{booking_id}';
		$this->replace['booking_id'] = $this->booking->get_id();

		$this->find['total_price']      = '{total_price}';
		$this->replace['total_price']   = (string) $this->booking->get_total();

		$this->find['customer_first_name']      = '{customer_first_name}';
		$this->replace['customer_first_name']   = $this->booking['customer_first_name'];

		$this->find['customer_last_name']      = '{customer_last_name}';
		$this->replace['customer_last_name']   = $this->booking['customer_last_name'];

		$this->find['customer_email']      = '{customer_email}';
		$this->replace['customer_email']   = $this->booking->get_customer_email();

		$this->find['customer_phone']      = '{customer_phone}';
		$this->replace['customer_phone']   = $this->booking['customer_phone'];

		$this->find['customer_company']      = '{customer_company}';
		$this->replace['customer_company']   = $this->booking->get_customer_company();

		$this->find['customer_note']      = '{customer_note}';
		$this->replace['customer_note']   = $this->booking['customer_note'];

		$this->find['customer_details']      = '{customer_details}';
		$this->replace['customer_details']   = $this->get_template( 'customer-details', [ 'booking' => $this->booking ] );

		$this->find['breakdown']      = '{breakdown}';
		$this->replace['breakdown']   = $this->get_template( 'breakdown', [ 'booking' => $this->booking ] );
	}
}
