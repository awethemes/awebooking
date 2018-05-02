<?php
namespace AweBooking\Frontend\Shortcodes;

abstract class Shortcode_Abstract {
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
	 * Default attributes.
	 *
	 * @var array
	 */
	protected $defaults = [];

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
	abstract public function output( $request );

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
			awebooking()->handle_buffering_exception( $e, $ob_level );
		} catch ( \Throwable $e ) {
			awebooking()->handle_buffering_exception( $e, $ob_level );
		}

		return ltrim( ob_get_clean() );
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
	protected function get_defaults() {
		return $this->defaults;
	}

	/**
	 * Resolve the http_request.
	 *
	 * @return \Awethemes\Http\Request
	 */
	protected function resolve_http_request() {
		$awebooking = awebooking()->get_instance();

		if ( ! $awebooking->resolved( 'request' ) ) {
			$awebooking->instance( 'request', $request = $awebooking->make( 'request' ) );
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
		return shortcode_atts( $this->get_defaults(), $atts );
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
		printf( '<div class="awebooking-shortcode-error">%s</div>', wp_kses_post( wpautop( $message ) ) );
	}
}
