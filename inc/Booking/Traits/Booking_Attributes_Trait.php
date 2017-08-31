<?php
namespace AweBooking\Booking\Traits;

use AweBooking\Pricing\Price;
use AweBooking\Currency\Currency;
use AweBooking\Support\Carbonate;

trait Booking_Attributes_Trait {
	/**
	 * Gets the booking status.
	 *
	 * @return string
	 */
	public function get_status() {
		return apply_filters( $this->prefix( 'get_status' ), $this['status'], $this );
	}

	/**
	 * Gets the AweBooking version of current booking.
	 *
	 * @return string
	 */
	public function get_version() {
		return apply_filters( $this->prefix( 'get_version' ), $this['version'], $this );
	}

	/**
	 * Gets the booking currency.
	 *
	 * @return Currency
	 */
	public function get_currency() {
		$currency = $this['currency'] ? new Currency( $this['currency'] ) : awebooking()->make( 'currency' );

		return apply_filters( $this->prefix( 'get_currency' ), $currency, $this );
	}

	/**
	 * Gets the booking date.
	 *
	 * @return Carbonate
	 */
	public function get_booking_date() {
		return apply_filters( $this->prefix( 'get_booking_date' ), Carbonate::create_datetime( $this['date_created'] ), $this );
	}

	/**
	 * Gets the date modified.
	 *
	 * @return Carbonate
	 */
	public function get_date_modified() {
		return apply_filters( $this->prefix( 'get_date_modified' ), Carbonate::create_datetime( $this['date_modified'] ), $this );
	}

	/**
	 * Returns whether or not the booking is featured.
	 *
	 * @return bool
	 */
	public function is_featured() {
		return apply_filters( $this->prefix( 'is_featured' ), $this['featured'], $this );
	}

	/**
	 * Determines if this booking has been checked-in.
	 *
	 * @return boolean
	 */
	public function is_checked_in() {
		return apply_filters( $this->prefix( 'is_checked_in' ), $this['checked_in'], $this );
	}

	/**
	 * Determines if this booking has been checked-out.
	 *
	 * @return boolean
	 */
	public function is_checked_out() {
		return apply_filters( $this->prefix( 'is_checked_out' ), $this['checked_out'], $this );
	}

	/**
	 * By logic, if checked-out is set, checked-in is set too.
	 *
	 * @param  string|bool $value Set value.
	 * @return $this
	 */
	public function set_checked_out( $value ) {
		$checked = (bool) $value;
		$this->attributes['checked_out'] = $checked;

		if ( $checked && ! $this->is_checked_in() ) {
			$this->attributes['checked_in'] = true;
		}

		return $this;
	}

	/**
	 * Gets discount total.
	 *
	 * @return Price
	 */
	public function get_discount_total() {
		$discount_total = new Price( $this['discount_total'], $this->get_currency() );

		return apply_filters( $this->prefix( 'get_discount_total' ), $discount_total, $this );
	}

	/**
	 * Gets grand total.
	 *
	 * @return Price
	 */
	public function get_total() {
		$total = new Price( $this['total'], $this->get_currency() );

		return apply_filters( $this->prefix( 'get_total' ), $total, $this );
	}

	/*
	| ----------------------------------------------------------
	| Getter customer attributes.
	| ---------------------------------------------------------
	*/

	/**
	 * Gets the customer ID.
	 *
	 * If it is "0" we'll consider as Guest.
	 *
	 * @return int
	 */
	public function get_customer_id() {
		return $this->get_attribute( 'customer_id' );
	}

	/**
	 * Gets the customer title.
	 *
	 * @see awebooking_get_common_titles()
	 *
	 * @return string
	 */
	public function get_customer_title() {
		return apply_filters( $this->prefix( 'get_customer_title' ), $this['customer_title'], $this );
	}

	/**
	 * Gets the customer first name.
	 *
	 * @return string
	 */
	public function get_customer_first_name() {
		return apply_filters( $this->prefix( 'get_customer_first_name' ), $this['customer_first_name'], $this );
	}

	/**
	 * Gets the customer last name.
	 *
	 * @return string
	 */
	public function get_customer_last_name() {
		return apply_filters( $this->prefix( 'get_customer_last_name' ), $this['customer_last_name'], $this );
	}

	/**
	 * Gets the customer main address.
	 *
	 * @return string
	 */
	public function get_customer_address() {
		return apply_filters( $this->prefix( 'get_customer_address' ), $this['customer_address'], $this );
	}

	/**
	 * Gets the customer address 2.
	 *
	 * @return string
	 */
	public function get_customer_address2() {
		return apply_filters( $this->prefix( 'get_customer_address2' ), $this['customer_address2'], $this );
	}

	/**
	 * Gets the customer living city.
	 *
	 * @return string
	 */
	public function get_customer_city() {
		return apply_filters( $this->prefix( 'get_customer_city' ), $this['customer_city'], $this );
	}

	/**
	 * Gets the customer state of country.
	 *
	 * @return string
	 */
	public function get_customer_state() {
		return apply_filters( $this->prefix( 'get_customer_state' ), $this['customer_state'], $this );
	}

	/**
	 * Gets the customer postal code.
	 *
	 * @return string
	 */
	public function get_customer_postal_code() {
		return apply_filters( $this->prefix( 'get_customer_postal_code' ), $this['customer_postal_code'], $this );
	}

	/**
	 * Gets the customer country.
	 *
	 * @return string
	 */
	public function get_customer_country() {
		return apply_filters( $this->prefix( 'get_customer_country' ), $this['customer_country'], $this );
	}

	/**
	 * Gets the customer company name.
	 *
	 * @return string
	 */
	public function get_customer_company() {
		return apply_filters( $this->prefix( 'get_customer_company' ), $this['customer_company'], $this );
	}

	/**
	 * Gets the customer phone.
	 *
	 * @return string
	 */
	public function get_customer_phone() {
		return apply_filters( $this->prefix( 'get_customer_phone' ), $this['customer_phone'], $this );
	}

	/**
	 * Gets the customer email address.
	 *
	 * @return string
	 */
	public function get_customer_email() {
		return apply_filters( $this->prefix( 'get_customer_email' ), $this['customer_email'], $this );
	}

	/*
	| ----------------------------------------------------------
	| Getter payment attributes.
	| ---------------------------------------------------------
	*/

	/**
	 * Gets the payment method.
	 *
	 * @return string
	 */
	public function get_payment_method() {
		return apply_filters( $this->prefix( 'get_payment_method' ), $this['payment_method'], $this );
	}

	/**
	 * Gets the payment method title.
	 *
	 * @return string
	 */
	public function get_payment_method_title() {
		return apply_filters( $this->prefix( 'get_payment_method_title' ), $this['payment_method_title'], $this );
	}

	/**
	 * Gets the transaction ID.
	 *
	 * @return string
	 */
	public function get_transaction_id() {
		return apply_filters( $this->prefix( 'get_transaction_id' ), $this['transaction_id'], $this );
	}

	/**
	 * Gets "created_via" attribute.
	 *
	 * Use for booking create via WooCommerce or via API.
	 *
	 * @return string
	 */
	public function get_created_via() {
		return apply_filters( $this->prefix( 'get_created_via' ), $this['created_via'], $this );
	}

	/**
	 * Gets the date transaction was paid successful.
	 *
	 * @return Carbonate|null
	 */
	public function get_date_paid() {
		$date_paid = null;

		if ( $this['date_paid'] ) {
			$date_paid = Carbonate::create_datetime( $this['date_paid'] );
		}

		return apply_filters( $this->prefix( 'get_date_paid' ), $date_paid, $this );
	}

	/**
	 * Gets the customer note.
	 *
	 * @return string
	 */
	public function get_customer_note() {
		return apply_filters( $this->prefix( 'get_customer_note' ), $this['customer_note'], $this );
	}

	/**
	 * Gets the customer IP address.
	 *
	 * @return string
	 */
	public function get_customer_ip_address() {
		return apply_filters( $this->prefix( 'get_customer_ip_address' ), $this['customer_ip_address'], $this );
	}

	/**
	 * Gets the customer user agent from browser.
	 *
	 * @return string
	 */
	public function get_customer_user_agent() {
		return apply_filters( $this->prefix( 'get_customer_user_agent' ), $this['customer_user_agent'], $this );
	}
}
