<?php
/**
 * The loader file.
 *
 * @package AweBooking
 */

/**
 * First, we need autoload via Composer to make everything works.
 */
require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/vendor/wplibs/form/functions.php';
require_once __DIR__ . '/vendor/wplibs/http/functions.php';
require_once __DIR__ . '/vendor/cmb2/cmb2/init.php';

// For dev only, will be remove in the future when packages stable.
$_dev_packages = [
	__DIR__ . '/awethemes/relationships/vendor/autoload.php',
];

foreach ( $_dev_packages as $_package ) {
	if ( file_exists( $_package ) ) {
		require_once $_package;
	}
}

// Require helpers & functions.
require trailingslashit( __DIR__ ) . 'inc/Support/helpers.php';
require trailingslashit( __DIR__ ) . 'inc/Core/sanitizer.php';

/**
 * Then, require the main class.
 */
require_once trailingslashit( __DIR__ ) . 'inc/Plugin.php';

/**
 * Alias the class "AweBooking\Plugin" to "AweBooking".
 */
class_alias( \AweBooking\Plugin::class, 'AweBooking', false );

// Back-compat.
class_alias( \WPLibs\Http\Request::class, 'Awethemes\\Http\\Request' );
class_alias( \WPLibs\Http\Response::class, 'Awethemes\\Http\\Response' );
class_alias( \WPLibs\Http\Json_Response::class, 'Awethemes\\Http\\Json_Response' );
class_alias( \WPLibs\Http\Exception\HttpException::class, 'Awethemes\\Http\\Exception\\HttpException' );
class_alias( \WPLibs\Http\Exception\BadRequestException::class, 'Awethemes\\Http\\Exception\\BadRequestException' );

class_alias( \WPLibs\Rules\Rule::class, 'AweBooking\\Component\\Ruler\\Rule' );
class_alias( \WPLibs\Rules\Context::class, 'AweBooking\\Component\\Ruler\\Context' );
class_alias( \WPLibs\Rules\Variable::class, 'AweBooking\\Component\\Ruler\\Variable' );
