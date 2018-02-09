<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Response;
use AweBooking\Http\Controllers\Controller as Base_Controller;
use Awethemes\Http\Exception\AccessDeniedHttpException;
use AweBooking\Model\Exceptions\Model_Not_Found_Exception;

abstract class Controller extends Base_Controller {
	/**
	 * Get the `admin_notices` instance.
	 *
	 * @param  string $type    Optional, the notice type if provided: 'updated', 'success', 'info', 'error', 'warning'.
	 * @param  string $message Optional, the notice message.
	 * @return \AweBooking\Support\Flash_Message
	 */
	protected function notices( $type = null, $message = '' ) {
		$notices = awebooking( 'admin_notices' );

		if ( is_null( $type ) ) {
			return $notices;
		}

		return $notices->add_message( $message, $type );
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

	/**
	 * Assert a given object must be exists.
	 *
	 * @param  \AweBooking\Model\WP_Object $object WP_Object implementation.
	 * @return void
	 *
	 * @throws Model_Not_Found_Exception
	 */
	protected static function assert_object_exists( $object ) {
		if ( is_null( $object ) ) {
			throw new Model_Not_Found_Exception( esc_html__( 'Resource not found', 'awebooking' ) );
		}

		if ( ! $object->exists() ) {
			throw (new Model_Not_Found_Exception)->set_model( get_class( $object ) );
		}
	}
}
