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

	/**
	 * @deprecated
	 */
	public function handle_buffering_exception( $e, $ob_level, $callback = null ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '3.1.10', 'abrs_handle_buffering_exception' );
		abrs_handle_buffering_exception( $e, $ob_level, $callback );
	}
}
