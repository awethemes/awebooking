<?php
namespace AweBooking\Notification;

use AweBooking\Support\Markdown;
use AweBooking\Support\Mailable;

use AweBooking\Booking\Booking;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Booking\Items\Service_Item;

abstract class Booking_Notification extends Mailable {
	/**
	 * The Booking instance.
	 *
	 * @var \AweBooking\Booking\Booking
	 */
	protected $booking;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Booking\Booking $booking The Booking instance.
	 */
	public function __construct( Booking $booking ) {
		$this->booking = $booking;
	}

	/**
	 * Get markdown contents.
	 *
	 * @return string
	 */
	abstract protected function get_markdown_contents();

	/**
	 * {@inheritdoc}
	 */
	public function build() {
		return $this->get_mail_contents();
	}

	/**
	 * {@inheritdoc}
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

		$this->booking['customer_first_name']  = 'John';
		$this->booking['customer_last_name']   = 'Cena';
		$this->booking['customer_email']       = 'customer@email.com';
		$this->booking['customer_phone']       = '+84xxxxxxxx';
		$this->booking['customer_company']     = 'AweThemes';
		$this->booking['customer_note']        = 'Demo customer note';

		return $this->get_mail_contents();
	}

	/**
	 * Set the replacements.
	 *
	 * @return void
	 */
	public function set_replacements() {
		$replacements = [
			'booking_id'           => $this->booking->get_id(),
			'created_date'         => $this->booking['date_created'] ? $this->booking->get_booking_date()->to_wp_datetime_string() : '',
			'contents'             => $this->get_template( 'breakdown', [ 'booking' => $this->booking ] ),
			'total'                => (string) $this->booking->get_total(),
			'customer_details'     => $this->get_template( 'customer-details', [ 'booking' => $this->booking ] ),
			'customer_name'        => $this->booking->get_customer_name(),
			'customer name'        => $this->booking->get_customer_name(),
			'customer first_name'  => $this->booking->get_customer_first_name(),
			'customer last_name'   => $this->booking->get_customer_last_name(),
			'customer address'     => $this->booking->get_customer_address(),
			'customer address2'    => $this->booking->get_customer_address2(),
			'customer city'        => $this->booking->get_customer_city(),
			'customer state'       => $this->booking->get_customer_state(),
			'customer postal_code' => $this->booking->get_customer_postal_code(),
			'customer country'     => $this->booking->get_customer_country(),
			'customer company'     => $this->booking->get_customer_company(),
			'customer phone'       => $this->booking->get_customer_phone(),
			'customer email'       => $this->booking->get_customer_email(),
		];

		$replacements = apply_filters( 'awebooking/notification/email_fields', $replacements, $this->booking, $this );

		// Back-compat.
		$replacements['order_date']   = $replacements['created_date'];
		$replacements['order_number'] = $replacements['booking_id'];

		foreach ( $replacements as $key => $value ) {
			$this->find[ $key ] = '{' . $key . '}';
			$this->replace[ $key ] = $value;
		}
	}

	/**
	 * Get email contents.
	 *
	 * @return string
	 */
	protected function get_mail_contents() {
		$contents = Markdown::parse( $this->get_markdown_contents() );

		$contents = apply_filters( 'the_content', $contents );
		$contents = str_replace( ']]>', ']]&gt;', $contents );

		return $this->format_string( $contents );
	}
}
