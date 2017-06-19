<?php
/**
 * PHPUnit bootstrap file
 *
 * @package AweBooking
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	date_default_timezone_set( 'Asia/Ho_Chi_Minh' );

	require_once dirname( __DIR__ ) . '/skeleton/libs/cmb2/bootstrap.php';

	require dirname( __DIR__ ) . '/awebooking.php';

	// AweBooking\Installer::create_tables();
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
