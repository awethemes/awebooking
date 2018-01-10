<?php
/**
 * PHPUnit bootstrap file
 *
 * @package AweBooking
 */

ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

// Ensure server variable is set for WP email functions.
if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
	$_SERVER['SERVER_NAME'] = 'localhost';
}

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
tests_add_filter( 'muplugins_loaded', function () {
	require_once dirname( __DIR__ ) . '/vendor/autoload.php';

	WP_Mock::bootstrap();

	require dirname( __DIR__ ) . '/awebooking.php';
});

/**
 * Install AweBooking.
 */
tests_add_filter( 'setup_theme', function () {
	AweBooking\Support\Decimal::set_default_scale( 4 );

	// Clean existing install first.
	define( 'WP_UNINSTALL_PLUGIN', true );
	define( 'AWEBOOKING_REMOVE_ALL_DATA', true );

	require dirname( __DIR__ ) . '/uninstall.php';
});

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
