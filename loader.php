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
class_alias( \WPLibs\Http\Request::class, \Awethemes\Http\Request::class );
class_alias( \WPLibs\Http\Response::class, \Awethemes\Http\Response::class );
class_alias( \WPLibs\Http\Json_Response::class, \Awethemes\Http\Json_Response::class );
class_alias( \WPLibs\Http\Exception\HttpException::class, \Awethemes\Http\Exception\HttpException::class );
class_alias( \WPLibs\Http\Exception\BadRequestException::class, \Awethemes\Http\Exception\BadRequestException::class );

class_alias( \WPLibs\Rules\Rule::class, \AweBooking\Component\Ruler\Rule::class );
class_alias( \WPLibs\Rules\Context::class, AweBooking\Component\Ruler\Context::class );
class_alias( \WPLibs\Rules\Variable::class, AweBooking\Component\Ruler\Variable::class );
class_alias( \WPLibs\Rules\Operator\Equal::class, \AweBooking\Component\Ruler\Operator\Equal::class );
class_alias( \WPLibs\Rules\Operator\Not_Equal::class, \AweBooking\Component\Ruler\Operator\Not_Equal::class );
class_alias( \WPLibs\Rules\Operator\In::class, \AweBooking\Component\Ruler\Operator\In::class );
class_alias( \WPLibs\Rules\Operator\Not_In::class, \AweBooking\Component\Ruler\Operator\Not_In::class );
class_alias( \WPLibs\Rules\Operator\Less_Than::class, \AweBooking\Component\Ruler\Operator\Less_Than::class );
class_alias( \WPLibs\Rules\Operator\Less_Than_Or_Equal::class, \AweBooking\Component\Ruler\Operator\Less_Than_Or_Equal::class );
class_alias( \WPLibs\Rules\Operator\Greater_Than::class, \AweBooking\Component\Ruler\Operator\Greater_Than::class );
class_alias( \WPLibs\Rules\Operator\Greater_Than_Or_Equal::class, \AweBooking\Component\Ruler\Operator\Greater_Than_Or_Equal::class );
class_alias( \WPLibs\Rules\Operator\Between::class, \AweBooking\Component\Ruler\Operator\Between::class );
class_alias( \WPLibs\Rules\Operator\Not_Between::class, \AweBooking\Component\Ruler\Operator\Not_Between::class );
class_alias( \WPLibs\Rules\Operator\Starts_With::class, \AweBooking\Component\Ruler\Operator\Starts_With::class );
class_alias( \WPLibs\Rules\Operator\Not_Starts_With::class, \AweBooking\Component\Ruler\Operator\Not_Starts_With::class );
class_alias( \WPLibs\Rules\Operator\String_Contains::class, \AweBooking\Component\Ruler\Operator\String_Contains::class );
class_alias( \WPLibs\Rules\Operator\String_Does_Not_Contain::class, \AweBooking\Component\Ruler\Operator\String_Does_Not_Contain::class );
class_alias( \WPLibs\Rules\Operator\Ends_With::class, \AweBooking\Component\Ruler\Operator\Ends_With::class );
class_alias( \WPLibs\Rules\Operator\Not_Ends_With::class, \AweBooking\Component\Ruler\Operator\Not_Ends_With::class );
class_alias( \WPLibs\Rules\Operator\Is_Empty::class, \AweBooking\Component\Ruler\Operator\Is_Empty::class );
class_alias( \WPLibs\Rules\Operator\Is_Not_Empty::class, \AweBooking\Component\Ruler\Operator\Is_Not_Empty::class );
class_alias( \WPLibs\Rules\Operator\Is_Null::class, \AweBooking\Component\Ruler\Operator\Is_Null::class );
class_alias( \WPLibs\Rules\Operator\Is_Not_Null::class, \AweBooking\Component\Ruler\Operator\Is_Not_Null::class );
