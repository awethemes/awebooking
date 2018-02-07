<?php
namespace AweBooking\Admin;

use Symfony\Component\Debug\Exception\FatalThrowableError;

class Admin_Template {
	/**
	 * Create a full template.
	 *
	 * @param  string $template The template name.
	 * @param  array  $vars     The data inject to template.
	 * @return string
	 */
	public function get( $template, array $vars = [] ) {
		return $this->evaluate_template(
			$this->locale_template( $template ), $vars
		);
	}

	/**
	 * Display a partial template.
	 *
	 * @param  string $template The template name.
	 * @param  array  $vars     The data inject to template.
	 * @return void
	 */
	public function partial( $template, array $vars = [] ) {
		print $this->evaluate_template( // @codingStandardsIgnoreLine
			$this->locale_template( $template ), $vars, false
		);
	}

	/**
	 * Create a callback display a partial template.
	 *
	 * @param  string $template The template name.
	 * @param  array  $vars     The data inject to template.
	 * @return \Closure
	 */
	public function partial_callback( $template, array $vars = [] ) {
		return function () use ( $template, $vars ) {
			$this->partial( $template, $vars );
		};
	}

	/**
	 * Locale the template path.
	 *
	 * @param  string $template Template name.
	 * @return string
	 */
	protected function locale_template( $template ) {
		return file_exists( realpath( $template ) )
			? realpath( $template )
			: trailingslashit( __DIR__ ) . 'views/' . $template;
	}

	/**
	 * Get the evaluated contents of the view at the given path.
	 *
	 * @param  string $template       The template path.
	 * @param  array  $vars           The data inject to template.
	 * @param  bool   $admin_template Should be include admin template?.
	 * @return string
	 */
	protected function evaluate_template( $template, $vars, $admin_template = true ) {
		$ob_level = ob_get_level();

		// Turn on output buffering.
		ob_start();

		// @codingStandardsIgnoreLine, Okay, just fine!
		extract( $vars, EXTR_SKIP );

		// We'll evaluate the contents of the view inside a try/catch block so we can
		// flush out any stray output that might get out before an error occurs or
		// an exception is thrown. This prevents any partial views from leaking.
		try {
			if ( $admin_template ) {
				// If see the $page_title in $vars, set the admin_title.
				if ( isset( $page_title ) && ! empty( $page_title ) ) {
					add_filter( 'admin_title', function( $admin_title ) use ( $page_title ) {
						return $page_title . $admin_title;
					});
				}

				require_once ABSPATH . 'wp-admin/admin-header.php';
			}

			// Include the template.
			include $template;

			if ( $admin_template ) {
				include ABSPATH . 'wp-admin/admin-footer.php';
			}
		} catch ( \Exception $e ) {
			$this->handle_view_exception( $e, $ob_level );
		} catch ( \Throwable $e ) {
			$this->handle_view_exception( $e, $ob_level );
		}

		return ltrim( ob_get_clean() );
	}

	/**
	 * Handle a view exception.
	 *
	 * @param  \Exception $e        The exception.
	 * @param  int        $ob_level The ob_get_level().
	 * @return void
	 *
	 * @throws \Exception
	 */
	protected function handle_view_exception( $e, $ob_level ) {
		// In PHP7+, throw a FatalThrowableError when we catch an Error.
		if ( $e instanceof \Error && class_exists( FatalThrowableError::class ) ) {
			$e = new FatalThrowableError( $e );
		}

		while ( ob_get_level() > $ob_level ) {
			ob_end_clean();
		}

		throw $e;
	}
}
