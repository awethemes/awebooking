<?php
namespace AweBooking\Admin\Settings;

use AweBooking\Admin\Admin_Settings;

abstract class Abstract_Setting {
	/**
	 * Register the sections, panels or fields.
	 *
	 * @param \AweBooking\Admin\Admin_Settings $settings The Admin_Settings instance.
	 * @return void
	 */
	abstract public function registers( Admin_Settings $settings );
}
