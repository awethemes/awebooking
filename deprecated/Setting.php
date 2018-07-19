<?php
namespace AweBooking\Deprecated;

class Setting {
	public function get_setting_key() {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return awebooking()->get_current_option();
	}

	public function get( $key, $default = null ) {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_get_option( $key, $default );
	}

	public function refresh() {
		_abrs_310_deprecated_function( __FUNCTION__ );
	}

	public function get_money_format( $position = null ) {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_get_price_format();
	}

	public function is_children_bookable() {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_children_bookable();
	}

	public function is_infants_bookable() {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_infants_bookable();
	}

	public function is_multi_location() {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_multiple_hotels();
	}

	public function get_default( $key ) {
		_abrs_310_deprecated_function( __FUNCTION__ );
	}

	public function get_date_format() {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_get_date_format();
	}

	public function get_time_format() {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_get_time_format();
	}

	public function get_default_hotel_location() {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return null;
	}

	public function get_admin_notify_emails() {
		_abrs_310_deprecated_function( __FUNCTION__ );
	}

	public function get_room_states() {
		abrs_310_deprecated_function();
		return [];
	}

	public function get_booking_statuses() {
		return abrs_get_booking_statuses();
	}

	public function get_service_operations() {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return [];
	}

	public function get_currency_positions() {
		_abrs_310_deprecated_function( __FUNCTION__ );
	}
}
