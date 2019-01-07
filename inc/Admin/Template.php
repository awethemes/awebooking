<?php

namespace AweBooking\Admin;

use WPLibs\View\Factory;

class Template {
	/**
	 * The view factory instance.
	 *
	 * @var \WPLibs\View\View_Factory
	 */
	protected $factory;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->factory = Factory::create( [
			'paths' => [ __DIR__ . '/views/' ],
		] );
	}

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
			: str_replace( '/', '.', rtrim( $template, '.php' ) );
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
		// Turn on output buffering.
		ob_start();

		if ( $admin_template ) {
			// If see the $page_title in $vars, set the admin_title.
			if ( ! empty( $vars['page_title'] ) ) {
				$this->modify_admin_title( $vars['page_title'] );
			}

			require_once ABSPATH . 'wp-admin/admin-header.php';
		}

		// Include the template.
		if ( file_exists( $template ) ) {
			echo $this->factory->file( $template, $vars )->render(); // @WPCS: XSS OK.
		} else {
			echo $this->factory->make( $template, $vars )->render(); // @WPCS: XSS OK.
		}

		if ( $admin_template ) {
			include ABSPATH . 'wp-admin/admin-footer.php';
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
		add_filter( 'admin_title', function ( $admin_title ) use ( $page_title ) {
			return $page_title . $admin_title;
		} );
	}
}
