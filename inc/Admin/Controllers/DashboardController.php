<?php

namespace AweBooking\Admin\Controllers;

class DashboardController extends Controller
{
	/**
	 * Show the dashboard controller.
	 */
	public function __invoke()
	{
		return $this->response('dashboard');
	}
}
