<?php

namespace AweBooking\Admin\Controllers;

use WPLibs\Http\Request;
use AweBooking\Admin\Admin_Settings;

class Settings_Controller extends Controller {
	/**
	 * The admin settings.
	 *
	 * @var \AweBooking\Admin\Admin_Settings
	 */
	protected $admin_settings;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Admin\Admin_Settings $admin_settings The admin settings.
	 */
	public function __construct( Admin_Settings $admin_settings ) {
		$this->require_capability( 'manage_awebooking_settings' );

		$this->admin_settings = $admin_settings;
	}

	/**
	 * Display the settings.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @return \WPLibs\Http\Response
	 */
	public function index( Request $request ) {
		$settings = $this->admin_settings;

		return $this->response( 'settings/index.php', compact( 'settings', 'request' ) );
	}

	/**
	 * Handle store the settings.
	 *
	 * @param  \WPLibs\Http\Request $request The current request.
	 * @return \WPLibs\Http\Response|mixed
	 */
	public function store( Request $request ) {
		check_admin_referer( 'awebooking-settings', '_wpnonce' );

		// Handle save the settings.
		if ( $tab = $request->get( '_setting' ) ) {
			$this->admin_settings->save( $tab, $request );
		}

		return $this->redirect()->back( abrs_admin_route( '/settings' ) );
	}
}
