<?php
namespace AweBooking\Admin\Controllers;

class About_Controller extends Controller {
	/**
	 * Output the about page.
	 *
	 * @return \Awethemes\Http\Response
	 */
	public function index() {
		return $this->response( 'about/about.php' );
	}
}
