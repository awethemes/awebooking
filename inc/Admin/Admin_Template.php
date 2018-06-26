<?php
namespace AweBooking\Admin;

class Admin_Template {
	/**
	 * Returns a template contents.
	 *
	 * @param  string $template The template name.
	 * @param  array  $vars     The data inject to template.
	 * @return string
	 */
	public function get( $template, array $vars = [] ) {
		return $this->evaluate_template(
			$this->locale_template( $template ), $vars, false
		);
	}

	/**
	 * Display a partial template.
	 *
	 * @param  string $partial The partial template.
	 * @param  array  $vars    The data inject to template.
	 * @return void
	 */
	public function partial( $partial, array $vars = [] ) {
		print $this->evaluate_template( // @codingStandardsIgnoreLine
			$this->locale_template( $partial ), $vars, false // @codingStandardsIgnoreLine
		);
	}

	/**
	 * Returns a full page template.
	 *
	 * @param  string $page The template page.
	 * @param  array  $vars The data inject to template.
	 * @return string
	 */
	public function page( $page, array $vars = [] ) {
		return $this->evaluate_template(
			$this->locale_template( $page ), $vars, true
		);
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
	protected function evaluate_template( $template, $vars, $admin_template = false ) {
		$ob_level = ob_get_level();

		// Turn on output buffering.
		ob_start();

		// @codingStandardsIgnoreLine
		extract( $vars, EXTR_SKIP );

		// We'll evaluate the contents of the view inside a try/catch block so we can
		// flush out any stray output that might get out before an error occurs or
		// an exception is thrown. This prevents any partial views from leaking.
		try {
			if ( $admin_template ) {
				// If see the $page_title in $vars, set the admin_title.
				if ( isset( $page_title ) && ! empty( $page_title ) ) {
					$this->modify_admin_title( $page_title );
				}

				require_once ABSPATH . 'wp-admin/admin-header.php';
			}

			// Include the template.
			include $template;

			if ( $admin_template ) {
				include ABSPATH . 'wp-admin/admin-footer.php';
			}
		} catch ( \Exception $e ) {
			awebooking()->handle_buffering_exception( $e, $ob_level );
		} catch ( \Throwable $e ) {
			awebooking()->handle_buffering_exception( $e, $ob_level );
		}

		return ltrim( ob_get_clean() );
	}

	/**
	 * Add a filter to modify the admin title.
	 *
	 * @param  string $page_title The page title.
	 * @return void
	 */
	protected function modify_admin_title( $page_title ) {
		add_filter( 'admin_title', function( $admin_title ) use ( $page_title ) {
			return $page_title . $admin_title;
		});
	}
}
