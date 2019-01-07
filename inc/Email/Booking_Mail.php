<?php

namespace AweBooking\Email;

abstract class Booking_Mail extends Mailable {
	/**
	 * The booking instance.
	 *
	 * @var \AweBooking\Model\Booking
	 */
	protected $booking;

	/**
	 * Prepare data for sending.
	 *
	 * @param  \AweBooking\Model\Booking $booking The booking instance.
	 * @return void
	 */
	protected function prepare_data( $booking ) {
		$this->booking = $booking;
		$this->placeholders = $this->apply_placeholders();

		if ( $this->customer_email ) {
			$this->recipient = $booking->get( 'customer_email' );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_content_plain() {
		return $this->format_string( $this->get_option( 'content' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_content_html() {
		return $this->get_template( 'content-booking', str_replace( [ '_booking', 'booking_' ], '', $this->id ), [
			'booking' => $this->booking,
			'content' => $this->format_string( $this->get_option( 'content' ) ),
		]);
	}

	/**
	 * Set the replacements.
	 *
	 * TODO: ...
	 *
	 * @return array
	 */
	public function apply_placeholders() {
		$placeholders = $this->placeholders;

		$new_placeholders = [
			'{booking_id}'              => $this->booking->get_id(),
			'{booking_number}'          => $this->booking->get_id(),
			'{status}'                  => $this->booking->get( 'status' ),
			'{source}'                  => $this->booking->get( 'source' ),
			'{created_via}'             => $this->booking->get( 'created_via' ),
			'{date_created}'            => $this->booking->get( 'date_created' ),
			'{booking_date}'            => $this->booking->get( 'date_created' ),
			'{date_modified}'           => $this->booking->get( 'date_modified' ),
			'{arrival_time}'            => $this->booking->get( 'arrival_time' ),
			'{customer_note}'           => $this->booking->get( 'customer_note' ),
			'{check_in_date}'           => $this->booking->get( 'check_in_date' ),
			'{check_out_date}'          => $this->booking->get( 'check_out_date' ),
			'{discount_tax}'            => $this->booking->get( 'discount_tax' ),
			'{total_tax}'               => $this->booking->get( 'total_tax' ),
			'{discount_total}'          => $this->booking->get( 'discount_total' ),
			'{total}'                   => (string) $this->booking->get_total(),
			'{paid}'                    => $this->booking->get( 'paid' ),
			'{balance_due}'             => $this->booking->get( 'balance_due' ),
			'{customer_id}'             => $this->booking->get( 'customer_id' ),
			'{customer_title}'          => $this->booking->get( 'customer_title' ),
			'{customer_first_name}'     => $this->booking->get( 'customer_first_name' ),
			'{customer_last_name}'      => $this->booking->get( 'customer_last_name' ),
			'{customer_address}'        => $this->booking->get( 'customer_address' ),
			'{customer_address_2}'      => $this->booking->get( 'customer_address_2' ),
			'{customer_city}'           => $this->booking->get( 'customer_city' ),
			'{customer_state}'          => $this->booking->get( 'customer_state' ),
			'{customer_postal_code}'    => $this->booking->get( 'customer_postal_code' ),
			'{customer_country}'        => $this->booking->get( 'customer_country' ),
			'{customer_company}'        => $this->booking->get( 'customer_company' ),
			'{customer_phone}'          => $this->booking->get( 'customer_phone' ),
			'{customer_email}'          => $this->booking->get( 'customer_email' ),
		];

		return array_merge( $placeholders, $new_placeholders );
	}
}
