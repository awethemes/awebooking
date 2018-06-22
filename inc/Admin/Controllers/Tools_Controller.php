<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;
use AweBooking\Admin\Admin_Tools;

class Tools_Controller extends Controller {
	/**
	 * Display the tools page.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @param  string                  $tab     The current tab.
	 * @return mixed
	 */
	public function index( Request $request, $tab = 'tools' ) {
		return $this->response( 'tools/index.php', [
			'callback'    => $this->display_callback( $tab ),
			'tabs'        => $this->get_tabs(),
			'current_tab' => $tab,
		]);
	}

	/**
	 * Perform execute tools task.
	 *
	 * @param \Awethemes\Http\Request       $request The http request.
	 * @param \AweBooking\Admin\Admin_Tools $tools   The admin tools instance.
	 *
	 * @return mixed
	 */
	public function execute( Request $request, Admin_Tools $tools ) {
		if ( $request->filled( 'task' ) ) {
			$response = $tools->run( $request->task );

			if ( ! empty( $response['message'] ) ) {
				abrs_admin_notices( $response['message'], 'info' );
			}
		}

		return $this->redirect()->back( abrs_admin_route( '/tools' ) );
	}

	/**
	 * Output the tools content.
	 *
	 * @param \Awethemes\Http\Request       $request The http request.
	 * @param \AweBooking\Admin\Admin_Tools $tools   The admin tools instance.
	 *
	 * @return void
	 */
	public function display_tools( Request $request, Admin_Tools $tools ) {
		abrs_admin_template_part( 'tools/html-tools.php', [
			'tools' => $tools->get_tools(),
		]);
	}

	/**
	 * Output the logs content.
	 *
	 * @return void
	 */
	public function display_logs() {
		// TODO: ...
	}

	/**
	 * Output the status content.
	 *
	 * @return void
	 */
	public function display_status() {
		// TODO: ...
	}

	/**
	 * Return list tools tabs.
	 *
	 * @return array
	 */
	protected function get_tabs() {
		return apply_filters( 'abrs_admin_tools_tabs', [
			'tools'  => esc_html__( 'Tools', 'awebooking' ),
			// 'status' => esc_html__( 'System Status', 'awebooking' ),
			// 'logs'   => esc_html__( 'Logs', 'awebooking' ),
		]);
	}

	/**
	 * Gets the display callback.
	 *
	 * @param  string $current_tab Current tab.
	 * @return array|\Closure
	 */
	protected function display_callback( $current_tab ) {
		switch ( $current_tab ) {
			case '':
			case 'tools':
				return [ $this, 'display_tools' ];
			case 'logs':
				return [ $this, 'display_logs' ];
			case 'status':
				return [ $this, 'display_status' ];
			default:
				if ( array_key_exists( $current_tab, $this->get_tabs() ) && has_action( $action = 'abrs_admin_tools_content_' . $current_tab ) ) {
					return function () use ( $action ) {
						do_action( $action );
					};
				}
				break;
		}
	}
}
