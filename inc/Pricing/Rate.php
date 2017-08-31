<?php
namespace AweBooking\Pricing;

use Roomify\Bat\Unit\Unit;
use AweBooking\Hotel\Room_Type;

class Rate extends Unit {
	/**
	 * //
	 *
	 * @param int $id The room-type ID.
	 */
	public function __construct( $id = 0, $default_price = 0 ) {
		$this->unit_id       =& $id;
		$this->default_value = $default_price;
		$this->constraints   = array();
	}
}
