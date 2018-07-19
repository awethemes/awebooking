<?php
namespace AweBooking\Deprecated;

trait AweBooking {
	public function is_multi_language() {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_running_on_multilanguage();
	}

	public function is_multi_location() {
		_abrs_310_deprecated_function( __FUNCTION__ );
		return abrs_multiple_hotels();
	}
}
