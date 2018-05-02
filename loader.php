<?php
/**
 * The loader file.
 *
 * @package AweBooking
 */

/**
 * First, we need autoload via Composer to make everything works.
 */
require trailingslashit( __DIR__ ) . 'vendor/autoload.php';
require trailingslashit( __DIR__ ) . 'vendor/webdevstudios/cmb2/init.php';

/**
 * Then, require the main class.
 */
require_once trailingslashit( __DIR__ ) . 'inc/Plugin.php';

// Require helpers & functions.
require trailingslashit( __DIR__ ) . 'inc/Support/helpers.php';

// Deprecated classes & functions.
require_once dirname( __FILE__ ) . '/deprecated/deprecated.php';

/**
 * Alias the class "AweBooking\Plugin" to "AweBooking".
 */
class_alias( 'AweBooking\Plugin', 'AweBooking', false );
