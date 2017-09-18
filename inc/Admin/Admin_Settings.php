<?php
namespace AweBooking\Admin;

use AweBooking\Admin\Pages\Admin_Settings as Base_Admin_Settings;

class Admin_Settings extends Base_Admin_Settings {
	/**
	 * TODO: Remove this class in next release!
	 */
	public function __construct() {
		parent::__construct();

		do_action( 'awebooking/admin_settings/register', $this );
	}
}
