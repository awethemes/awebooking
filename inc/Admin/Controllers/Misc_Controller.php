<?php
namespace AweBooking\Admin\Controllers;

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
	 * @return \Awethemes\Http\Response
	 */
	public function index() {
		return $this->response( 'misc/about.php' );
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
