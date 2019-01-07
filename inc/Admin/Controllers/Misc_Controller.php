<?php

namespace AweBooking\Admin\Controllers;

use AweBooking\Premium;
use AweBooking\Email\Templates\Sample_Email;

class Misc_Controller extends Controller {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->require_capability( 'manage_awebooking' );
	}

	/**
	 * Output the about page.
	 *
	 * @return \WPLibs\Http\Response
	 */
	public function about() {
		// delete_transient( 'awebooking_premium_themes' );
		// delete_transient( 'awebooking_premium_addons' );

		$available_addons = Premium::get_premium_plugins();
		$available_themes = Premium::get_premium_themes();

		return $this->response( 'misc/about.php', compact( 'available_addons', 'available_themes' ) );
	}

	/**
	 * Preview a email template.
	 *
	 * @return mixed
	 */
	public function preview_email() {
		return ( new Sample_Email )->get_content();
	}
}
