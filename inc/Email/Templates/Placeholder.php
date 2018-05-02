<?php
namespace AweBooking\Email\Templates;

trait Booking_Placeholder {
	/**
	 * Set the replacements.
	 *
	 * @return void
	 */
	public function apply( $booking ) {
		$replacements = [
			'booking_id'           => $booking->get_id(),
			'created_date'         => $booking['date_created'] ? $booking->get_booking_date()->to_wp_datetime_string() : '',
			'contents'             => abrs_get_template_content( 'emails/breakdown.php', [ 'booking' => $booking ] ),
			'total'                => (string) $booking->get_total(),
			'customer_details'     => abrs_get_template_content( 'emails/customer-details.php', [ 'booking' => $booking ] ),
			'customer_name'        => $booking->get_customer_name(),
			'customer name'        => $booking->get_customer_name(),
			'customer first_name'  => $booking->get_customer_first_name(),
			'customer last_name'   => $booking->get_customer_last_name(),
			'customer address'     => $booking->get_customer_address(),
			'customer address2'    => $booking->get_customer_address2(),
			'customer city'        => $booking->get_customer_city(),
			'customer state'       => $booking->get_customer_state(),
			'customer postal_code' => $booking->get_customer_postal_code(),
			'customer country'     => $booking->get_customer_country(),
			'customer company'     => $booking->get_customer_company(),
			'customer phone'       => $booking->get_customer_phone(),
			'customer email'       => $booking->get_customer_email(),
		];

		$replacements = apply_filters( 'awebooking/email/placeholder', $replacements, $booking, $this );

		// Back-compat.
		$replacements['order_date']   = $replacements['created_date'];
		$replacements['order_number'] = $replacements['booking_id'];

		return $replacements;
	}
}
