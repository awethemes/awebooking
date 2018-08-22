<?php
namespace AweBooking\Deprecated\Availability;

trait Room_Rate_Deprecated {
	public function get_errors() {
		return new \WP_Error;
	}

	public function has_error( $code = null ) {
		return false;
	}

	public function get_error_message( $code = null ) {
		return '';
	}
}
