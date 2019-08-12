<?php

namespace AweBooking\Admin\Controllers;

class DashboardController extends Controller
{
	/**
	 * Show the dashboard controller.
	 */
	public function __invoke()
	{
		add_action('admin_enqueue_scripts', function () {
			$deps = ['wp-element', 'wp-components'];

			wp_enqueue_script('awebooking-dashboard', abrs_asset_url('js/dashboard.js'), $deps, time(), true);
		});

		return $this->response('dashboard');
	}
}
