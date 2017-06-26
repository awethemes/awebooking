<?php
namespace AweBooking;

use Roomify\Bat\Unit\Unit;

class Rate extends Unit {
	/**
	 * Create new room object.
	 *
	 * @param int $id The room ID.
	 */
	public function __construct( $id = 0, $default_price = 0 ) {
		$this->unit_id =& $id;
		$this->default_value = $default_price;

		$this->constraints = array();
	}
}
