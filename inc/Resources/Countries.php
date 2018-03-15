<?php
namespace AweBooking\Resources;

use AweBooking\Support\Traits\Singleton;

class Countries {
	use Singleton;

	/**
	 * Constructor the countries.
	 */
	private function __construct() {
		static::$instance = $this;
	}
}
