<?php

namespace AweBooking\Component\View\Engines;

use AweBooking\Component\View\Engine;

class Php_Engine implements Engine {
	/**
	 * Get the evaluated contents of the view.
	 *
	 * @param  string $path
	 * @param  array  $data
	 * @return string
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
	 */
	protected function evaluate_path( $__path, $__data ) {
		$ob_level = ob_get_level();

		ob_start();

		extract( $__data, EXTR_SKIP );

		// We'll evaluate the contents of the view inside a try/catch block so we can
		// flush out any stray output that might get out before an error occurs or
		// an exception is thrown. This prevents any partial views from leaking.
		try {
			include $__path;
		} catch ( Exception $e ) {
			$this->handle_view_exception( $e, $ob_level );
		} catch ( Throwable $e ) {
			$this->handle_view_exception( new FatalThrowableError( $e ), $ob_level );
		}

		return ltrim( ob_get_clean() );
	}

	/**
	 * Handle a view exception.
	 *
	 * @param  \Exception $e
	 * @param  int        $obLevel
	 * @return void
	 * @throws \Exception
	 */
	protected function handle_view_exception( Exception $e, $obLevel ) {
		while ( ob_get_level() > $obLevel ) {
			ob_end_clean();
		}

		throw $e;
	}
}
