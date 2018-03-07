<?php
namespace AweBooking\Support\Traits;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use BadMethodCallException;

/**
 * Original from: Illuminate\Support\Traits\Macroable
 *
 * @link https://github.com/illuminate/support/blob/master/Traits/Macroable.php
 */
trait Macroable {
	/**
	 * The registered string macros.
	 *
	 * @var array
	 */
	protected static $macros = [];

	/**
	 * Register a custom macro.
	 *
	 * @param  string          $name  The macro name.
	 * @param  object|callable $macro The macro callback.
	 * @return void
	 */
	public static function macro( $name, $macro ) {
		static::$macros[ $name ] = $macro;
	}

	/**
	 * Mix another object into the class.
	 *
	 * @param  object $mixin The mixin class.
	 * @return void
	 */
	public static function mixin( $mixin ) {
		$methods = ( new ReflectionClass( $mixin ) )->getMethods(
			ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
		);

		foreach ( $methods as $method ) {
			$method->setAccessible( true );

			static::macro( $method->name, $method->invoke( $mixin ) );
		}
	}

	/**
	 * Checks if macro is registered.
	 *
	 * @param  string $name The macro name.
	 * @return bool
	 */
	public static function has_macro( $name ) {
		return isset( static::$macros[ $name ] );
	}

	/**
	 * Dynamically handle calls to the class.
	 *
	 * @param  string $method     The method name.
	 * @param  array  $parameters The method parameters.
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public static function __callStatic( $method, $parameters ) {
		if ( ! static::has_macro( $method ) ) {
			throw new BadMethodCallException( sprintf( 'Method %s::%s does not exist.', static::class, $method ) );
		}

		if ( static::$macros[ $method ] instanceof Closure ) {
			return call_user_func_array( Closure::bind( static::$macros[ $method ], null, static::class ), $parameters );
		}

		return call_user_func_array( static::$macros[ $method ], $parameters );
	}

	/**
	 * Dynamically handle calls to the class.
	 *
	 * @param  string $method     The method name.
	 * @param  array  $parameters The method parameters.
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call( $method, $parameters ) {
		if ( ! static::has_macro( $method ) ) {
			throw new BadMethodCallException( sprintf( 'Method %s::%s does not exist.', static::class, $method ) );
		}

		$macro = static::$macros[ $method ];

		if ( $macro instanceof Closure ) {
			return call_user_func_array( $macro->bindTo( $this, static::class ), $parameters );
		}

		return call_user_func_array( $macro, $parameters );
	}
}
