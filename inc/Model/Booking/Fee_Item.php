<?php
namespace AweBooking\Model\Booking;

use AweBooking\Dropdown;
use AweBooking\Gateway\Manager;

class Fee_Item extends Item {
	/**
	 * Name of object type.
	 *
	 * @var string
	 */
	protected $object_type = 'payment_item';

	/**
	 * Name of item type.
	 *
	 * @var string
	 */
	protected $type = 'payment_item';
}
