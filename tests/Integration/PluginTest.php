<?php
/**
 * Smoke tests confirming the plugin bootstraps inside a WordPress environment.
 *
 * @package Automattic\CustomMetadata
 */

declare( strict_types = 1 );

namespace Automattic\CustomMetadata\Tests\Integration;

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * Confirms the plugin loads and exposes its public API.
 */
final class PluginTest extends TestCase {

	/**
	 * The main plugin class should be available once the plugin is loaded.
	 */
	public function test_plugin_class_is_loaded(): void {
		$this->assertTrue( class_exists( \custom_metadata_manager::class ) );
	}

	/**
	 * The documented public helper functions should be registered.
	 *
	 * @dataProvider data_public_api_functions
	 *
	 * @param string $function_name Name of a public API function.
	 */
	public function test_public_api_functions_exist( string $function_name ): void {
		$this->assertTrue( function_exists( $function_name ) );
	}

	/**
	 * Data provider for the public API functions.
	 *
	 * @return array<string, array{string}>
	 */
	public function data_public_api_functions(): array {
		return array(
			'add field'      => array( 'x_add_metadata_field' ),
			'add multifield' => array( 'x_add_metadata_multifield' ),
			'add group'      => array( 'x_add_metadata_group' ),
		);
	}
}
