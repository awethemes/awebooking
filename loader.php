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

// Require helpers & functions.
require trailingslashit( __DIR__ ) . 'inc/Support/helpers.php';
require trailingslashit( __DIR__ ) . 'inc/Core/sanitizer.php';

// Load deprecated.
require trailingslashit( __DIR__ ) . 'deprecated/deprecated.php';

/**
 * Then, require the main class.
 */
require_once trailingslashit( __DIR__ ) . 'inc/Plugin.php';

/**
 * Alias the class "AweBooking\Plugin" to "AweBooking".
 */
class_alias( \AweBooking\Plugin::class, 'AweBooking', false );
