<?php
namespace AweBooking\Http\Routing;

use AweBooking\Template;
use AweBooking\Admin\Admin_Template;
use Awethemes\Http\Response;
use Awethemes\Http\Json_Response;

class Response_Factory {
	/**
	 * The redirector instance.
	 *
	 * @var \AweBooking\Http\Routing\Redirector
	 */
	protected $redirector;

	/**
	 * Create a new response factory instance.
	 *
	 * @param  \AweBooking\Http\Routing\Redirector $redirector The Redirector.
	 * @return void
	 */
	public function __construct( Redirector $redirector ) {
		$this->redirector = $redirector;
	}

	/**
	 * Return a new response.
	 *
	 * @param  string  $content  The response content.
	 * @param  integer $status   The response status.
	 * @param  array   $headers  The response headers.
	 * @return \Awethemes\Http\Response
	 */
	public function create( $content = '', $status = 200, array $headers = [] ) {
		return new Response( $content, $status, $headers );
	}

	/**
	 * Return a new view response from the application.
	 *
	 * @param  string $view
	 * @param  array  $data
	 * @param  int    $status
	 * @param  array  $headers
	 * @return \Awethemes\Http\Response
	 */
	public function view( $view, $data = [], $status = 200, array $headers = [] ) {
		$content = awebooking( Template::class )->get( $view, $data );

		return $this->create( $content, $status, $headers );
	}

	public function admin_view( $a ) {
	}

	/**
	 * Return a new JSON response from the application.
	 *
	 * @param  mixed $data
	 * @param  int   $status
	 * @param  array $headers
	 * @param  int   $options
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function json( $data = [], $status = 200, array $headers = [], $options = 0 ) {
		return new Json_Response( $data, $status, $headers, $options );
	}

	/**
	 * Return a new streamed response from the application.
	 *
	 * @param  \Closure $callback
	 * @param  int      $status
	 * @param  array    $headers
	 * @return \Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function stream( $callback, $status = 200, array $headers = [] ) {
		return new StreamedResponse( $callback, $status, $headers );
	}

	/**
	 * Return a new streamed response as a file download from the application.
	 *
	 * @param  \Closure    $callback
	 * @param  string|null $name
	 * @param  array       $headers
	 * @param  string|null $disposition
	 * @return \Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function stream_download( $callback, $name = null, array $headers = [], $disposition = 'attachment' ) {
		$response = new StreamedResponse( $callback, 200, $headers );

		if ( ! is_null( $name ) ) {
			$response->headers->set(
				'Content-Disposition', $response->headers->makeDisposition(
					$disposition,
					$name,
					$this->fallbackName( $name )
				)
			);
		}

		return $response;
	}

	/**
	 * Create a new file download response.
	 *
	 * @param  \SplFileInfo|string $file
	 * @param  string|null         $name
	 * @param  array               $headers
	 * @param  string|null         $disposition
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 */
	public function download( $file, $name = null, array $headers = [], $disposition = 'attachment' ) {
		$response = new BinaryFileResponse( $file, 200, $headers, true, $disposition );

		if ( ! is_null( $name ) ) {
			return $response->setContentDisposition( $disposition, $name, $this->fallbackName( $name ) );
		}

		return $response;
	}

	/**
	 * Convert the string to ASCII characters that are equivalent to the given name.
	 *
	 * @param  string $name
	 * @return string
	 */
	protected function fallbackName( $name ) {
		return str_replace( '%', '', Str::ascii( $name ) );
	}

	/**
	 * Return the raw contents of a binary file.
	 *
	 * @param  \SplFileInfo|string $file
	 * @param  array               $headers
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 */
	public function file( $file, array $headers = [] ) {
		return new BinaryFileResponse( $file, 200, $headers );
	}

	/**
	 * Create a new redirect response to the given path.
	 *
	 * @param  string    $path
	 * @param  int       $status
	 * @param  array     $headers
	 * @param  bool|null $secure
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function redirectTo( $path, $status = 302, $headers = [], $secure = null ) {
		return $this->redirector->to( $path, $status, $headers, $secure );
	}
}
