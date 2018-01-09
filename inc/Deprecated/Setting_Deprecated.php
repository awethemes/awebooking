<?php
namespace AweBooking\Deprecated;

trait Setting_Deprecated {
	public function get_children_bookable() {
		return $this->is_children_bookable();
	}

	public function get_infants_bookable() {
		return $this->is_infants_bookable();
	}
}
