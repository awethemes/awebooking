<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;

class About_Controller extends Controller {
	/**
	 * Output the about page.
	 *
	 * @return \Awethemes\Http\Response
	 */
	public function about() {
		return $this->response_view( 'about/about.php' );
	}
}
