<?php
namespace AweBooking\Deprecated;

trait AweBooking_Deprecated {
	public function is_multi_location() {
		return (bool) $this['setting']->get( 'enable_location' );
	}

	public function is_multi_language() {
		return $this->is_running_multilanguage();
	}
}
