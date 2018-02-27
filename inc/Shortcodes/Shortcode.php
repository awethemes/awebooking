<?php
namespace AweBooking\Shortcodes;

use AweBooking\Template;
use Awethemes\Http\Request;
use Symfony\Component\Debug\Exception\FatalThrowableError;

abstract class Shortcode {
	/**
	 * The shortcode attributes.
	 *
	 * @var array
	 */
	protected $atts;

	/**
	 * The shortcode contentes.
	 *
	 * @var string
	 */
	protected $contents;

	/**
	 * Default shortcode attributes.
	 *
	 * @var array
	 */
	protected $default_atts = [];

	/**
	 * Constructor.
	 *
	 * @param array  $atts     The shortcode attributes.
	 * @param string $contents The shortcode content (if any).
	 */
	public function __construct( $atts, $contents = '' ) {
		$this->atts = $this->parse_atts( $atts );
		$this->contents = $contents;
	}

	/**
	 * Output the shortcode.
	 *
	 * @param \Awethemes\Http\Request $request Current http request.
	 * @return void
	 */
	abstract public function output( Request $request );

	/**
	 * Build the shortcode.
	 *
	 * @return string
	 */
	public function build() {
		$ob_level = ob_get_level();

		// Turn on output buffering.
		ob_start();

		try {
			$this->output( $this->resolve_http_request() );
		} catch ( \Exception $e ) {
			$this->handle_exception( $e, $ob_level );
		} catch ( \Throwable $e ) {
			$this->handle_exception( $e, $ob_level );
		}

		return ltrim( ob_get_clean() );
	}

	/**
	 * Print a template.
	 *
	 * @param  string $template The template file path.
	 * @param  array  $vars     Optional, variables inject to template.
	 * @return mixed
	 */
	protected function template( $template, array $vars = [] ) {
		return awebooking( Template::class )->get_template( $template, $vars );
	}

	/**
	 * Get the shortcode attribute.
	 *
	 * @param  string $key     The find key.
	 * @param  mixed  $default The default value if not found.
	 * @return mixed
	 */
	public function get_atts( $key, $default = null ) {
		return Arr::get( $this->atts, $key, $default );
	}

	/**
	 * Get the shortcode contents.
	 *
	 * @return string
	 */
	public function get_contents() {
		return $this->contents;
	}

	/**
	 * Get default shortcode attributes.
	 *
	 * @return array
	 */
	protected function get_default_atts() {
		return $this->default_atts;
	}

	/**
	 * Resolve the http_request.
	 *
	 * @return \Awethemes\Http\Request
	 */
	protected function resolve_http_request() {
		$awebooking = awebooking()->get_instance();

		if ( ! $awebooking->resolved( Request::class ) ) {
			$awebooking->instance( 'request', $request = $awebooking->make( Request::class ) );
		} else {
			$request = $awebooking->make( 'request' );
		}

		return $request;
	}

	/**
	 * Perform parse shortcode attributes.
	 *
	 * @param  array $atts The shortcode attributes.
	 * @return array
	 */
	protected function parse_atts( $atts ) {
		return shortcode_atts( $this->get_default_atts(), $atts );
	}

	/**
	 * Print the error message.
	 *
	 * @param  Exception|WP_Error|string $error The error message.
	 * @return void
	 */
	protected function print_error( $error ) {
		if ( $error instanceof \Exception ) {
			$message = esc_html__( 'Sorry, a fatal error occurred.', 'awebooking' );
		} elseif ( is_wp_error( $error ) ) {
			$message = $error->get_error_message();
		} else {
			$message = (string) $error;
		}

		// Print the error message.
		printf( '<div class="awebooking-notice awebooking-notice--error">%s</div>', wp_kses_post( wpautop( $message ) ) );
	}

	/**
	 * Handle shortcode exception.
	 *
	 * @param  \Exception $e        The exception.
	 * @param  int        $ob_level The ob_get_level().
	 * @return void
	 *
	 * @throws \Exception
	 */
	protected function handle_exception( $e, $ob_level ) {
		// In PHP7+, throw a FatalThrowableError when we catch an Error.
		if ( $e instanceof \Error && class_exists( FatalThrowableError::class ) ) {
			$e = new FatalThrowableError( $e );
		}

		while ( ob_get_level() > $ob_level ) {
			ob_end_clean();
		}

		// When current site in DEBUG mode, just throw that exception.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			throw $e;
		}

		// Print the error message.
		echo '<div class="awebooking-notice awebooking-notice--error">',
				esc_html__( 'Sorry, a server error occurred. Please contact the administrator', 'awebooking' ), '<br>',
				esc_html( $e->getMessage() ),
			'</div>';
	}
}
