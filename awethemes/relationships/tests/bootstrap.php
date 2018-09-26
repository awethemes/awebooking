<?php
/**
 * PHPUnit bootstrap file
 *
 * @package WP_Object
 */

use Awethemes\Relationships\Manager;
use Awethemes\Relationships\Storage;

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

	_get_rel_test();

	_get_rel_test()->get_storage()->install();
});

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

function _get_rel_test() {
	static $rel;

	if ( ! $rel ) {
		$rel = new Manager( new Storage );
		$rel->init();
	}

	return $rel;
}
