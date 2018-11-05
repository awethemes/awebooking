<?php

namespace AweBooking\Component\View\Engines;

use AweBooking\Component\View\Engine;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class Php_Engine implements Engine {
	/**
	 * Get the evaluated contents of the view.
	 *
	 * @param  string $path
	 * @param  array  $data
	 * @return string
	 *
	 * @throws \Exception
	 */
	public function get( $path, array $data = [] ) {
		return $this->evaluate_path( $path, $data );
	}

	/**
	 * Get the evaluated contents of the view at the given path.
	 *
	 * @param  string $__path
	 * @param  array  $__data
	 * @return string
	 *
	 * @throws \Exception
	 */
	protected function evaluate_path( $__path, $__data ) {
		$ob_level = ob_get_level();

		ob_start();

		// @codingStandardsIgnoreLine
		extract( $__data, EXTR_SKIP );

		// We'll evaluate the contents of the view inside a try/catch block so we can
		// flush out any stray output that might get out before an error occurs or
		// an exception is thrown. This prevents any partial views from leaking.
		try {
			include $__path;
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
	 * @param  \Exception|\Throwable $e
	 * @param  int                   $ob_level
	 * @return void
	 *
	 * @throws mixed
	 */
	protected function handle_view_exception( \Exception $e, $ob_level ) {
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
