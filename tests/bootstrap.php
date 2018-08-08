<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Awebooking
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
tests_add_filter( 'muplugins_loaded', function () {
	require_once dirname( __DIR__ ) . '/vendor/autoload.php';

	WP_Mock::bootstrap();

	add_action( 'abrs_pre_option_price_decimal_separator', function() {
		return ',';
	});

	require dirname( __DIR__ ) . '/awebooking.php';
});

/**
 * Install AweBooking.
 */
tests_add_filter( 'setup_theme', function () {
	// Clean existing install first.
	define( 'WP_UNINSTALL_PLUGIN', true );

	define( 'AWEBOOKING_REMOVE_ALL_DATA', true );

	require dirname( __DIR__ ) . '/uninstall.php';
});

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
