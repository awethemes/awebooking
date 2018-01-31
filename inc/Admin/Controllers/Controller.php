<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Response;
use AweBooking\Http\Controllers\Controller as Base_Controller;
use Awethemes\Http\Exception\AccessDeniedHttpException;

abstract class Controller extends Base_Controller {
	/**
	 * Get the `admin_notices` instance.
	 *
	 * @return \AweBooking\Support\Flash_Message
	 */
	protected function notices() {
		return awebooking( 'admin_notices' );
	}

	/**
	 * Create a view response.
	 *
	 * @param  string  $template The template name.
	 * @param  array   $vars     The data inject to template.
	 * @param  integer $status   The response status.
	 * @param  array   $headers  The response headers.
	 * @return \Awethemes\Http\Response
	 */
	protected function response_view( $template, $vars = [], $status = 200, $headers = [] ) {
		$contents = awebooking( 'admin_template' )->get( $template, $vars );

		return Response::create( $contents, $status, $headers );
	}

	/**
	 * Check the current user has a specific capability.
	 *
	 * @param  string $capability Capability name.
	 * @return void
	 *
	 * @throws AccessDeniedHttpException
	 */
	protected function check_capability( $capability ) {
		if ( ! current_user_can( $capability ) ) {
			throw new AccessDeniedHttpException( esc_html__( 'Sorry, you are not allowed to access this page.', 'awebooking' ) );
		}
	}
}
