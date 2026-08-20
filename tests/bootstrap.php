<?php
/**
 * PHPUnit bootstrap file for the Custom Metadata Manager plugin.
 *
 * WordPress is only loaded for the `integration` test suite. Unit tests run
 * without a WordPress install, so booting WordPress for them would be needlessly slow.
 *
 * @package Automattic\CustomMetadata
 */

declare( strict_types = 1 );

namespace Automattic\CustomMetadata\Tests;

use Yoast\WPTestUtils\WPIntegration;

require_once dirname( __DIR__ ) . '/vendor/yoast/wp-test-utils/src/WPIntegration/bootstrap-functions.php';

// Detect whether PHPUnit was invoked for the integration test suite.
$argv_local     = $GLOBALS['argv'] ?? array();
$key            = (int) array_search( '--testsuite', $argv_local, true );
$is_integration = false;

// Handle `--testsuite integration` (two separate arguments).
if ( $key && isset( $argv_local[ $key + 1 ] ) && 'integration' === $argv_local[ $key + 1 ] ) {
	$is_integration = true;
}

// Handle `--testsuite=integration` (a single argument).
foreach ( $argv_local as $arg ) {
	if ( '--testsuite=integration' === $arg ) {
		$is_integration = true;
		break;
	}
}

if ( $is_integration ) {
	$_tests_dir = WPIntegration\get_path_to_wp_test_dir();

	// Give access to the tests_add_filter() function.
	require_once $_tests_dir . '/includes/functions.php';

	// Manually load the plugin being tested.
	\tests_add_filter(
		'muplugins_loaded',
		static function (): void {
			require dirname( __DIR__ ) . '/custom_metadata.php';
		}
	);

	// Bootstrap WordPress, the Composer autoloader and the PHPUnit polyfills.
	WPIntegration\bootstrap_it();
} else {
	/*
	 * Unit tests run without WordPress. The manager class lives in its own file with
	 * no load-time side effects — it is instantiated only from custom_metadata.php, the
	 * WordPress-only entry point — so it can be required directly here. Neither
	 * WordPress nor a Brain\Monkey shim is needed simply to define the class.
	 *
	 * Brain\Monkey is still initialised per test by Yoast\WPTestUtils\BrainMonkey\TestCase,
	 * so individual tests can mock WordPress functions when they need to.
	 */
	require_once dirname( __DIR__ ) . '/vendor/autoload.php';
	require_once dirname( __DIR__ ) . '/includes/class-custom-metadata-manager.php';
}
