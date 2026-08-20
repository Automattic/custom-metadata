<?php
/**
 * Unit tests for the small, pure predicate helpers.
 *
 * These methods are pure PHP (no WordPress calls), so they run as fast unit tests.
 *
 * @package Automattic\CustomMetadata
 */

declare( strict_types = 1 );

namespace Automattic\CustomMetadata\Tests\Unit;

use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * Covers custom_metadata_manager::build_nonce_key(), _is_multifield(),
 * is_field_addable_to_columns() and is_restricted_field().
 */
final class FieldPredicatesTest extends TestCase {

	/**
	 * A fresh manager instance (the constructor registers no hooks).
	 *
	 * @return \custom_metadata_manager
	 */
	private function manager(): \custom_metadata_manager {
		return new \custom_metadata_manager();
	}

	/**
	 * The nonce key is "metadata-{object_type}-{group_slug}".
	 *
	 * This is the contract shared by _display_group_nonce() and verify_group_nonce();
	 * if the format ever drifts, saved metadata would silently stop verifying.
	 *
	 * @dataProvider data_nonce_keys
	 *
	 * @param string $group_slug  The group slug.
	 * @param string $object_type The object type.
	 * @param string $expected    The expected nonce key.
	 */
	public function test_build_nonce_key( string $group_slug, string $object_type, string $expected ): void {
		$this->assertSame( $expected, $this->manager()->build_nonce_key( $group_slug, $object_type ) );
	}

	/**
	 * Data provider for build_nonce_key().
	 *
	 * @return array<string, array{string, string, string}>
	 */
	public function data_nonce_keys(): array {
		return array(
			'post group'   => array( 'featured', 'post', 'metadata-post-featured' ),
			'user group'   => array( 'seo', 'user', 'metadata-user-seo' ),
			'custom type'  => array( 'my-group', 'my_cpt', 'metadata-my_cpt-my-group' ),
		);
	}

	/**
	 * _is_multifield() is true only when the slug begins with the reserved prefix.
	 *
	 * @dataProvider data_multifield_slugs
	 *
	 * @param string $slug     The slug to test.
	 * @param bool   $expected Whether it should be recognised as a multifield.
	 */
	public function test_is_multifield( string $slug, bool $expected ): void {
		$this->assertSame( $expected, $this->manager()->_is_multifield( $slug ) );
	}

	/**
	 * Data provider for _is_multifield().
	 *
	 * @return array<string, array{string, bool}>
	 */
	public function data_multifield_slugs(): array {
		return array(
			'prefixed slug'            => array( '_x_multifield_authors', true ),
			'bare prefix'              => array( '_x_multifield', true ),
			'unprefixed slug'          => array( 'authors', false ),
			'empty slug'               => array( '', false ),
			'prefix without underscore' => array( 'x_multifield_authors', false ),
			'prefix not at start'      => array( 'prefixed_x_multifield_authors', false ),
		);
	}

	/**
	 * A field appears as a column when display_column is a string label or boolean true.
	 *
	 * @dataProvider data_display_column
	 *
	 * @param mixed $display_column The display_column value.
	 * @param bool  $expected       Whether the field is addable to columns.
	 */
	public function test_is_field_addable_to_columns( $display_column, bool $expected ): void {
		$field = (object) array( 'display_column' => $display_column );
		$this->assertSame( $expected, $this->manager()->is_field_addable_to_columns( 'field', $field ) );
	}

	/**
	 * Data provider for is_field_addable_to_columns().
	 *
	 * Note the empty-string case: any string counts as a custom column label, so an
	 * empty string is (perhaps surprisingly) still "addable".
	 *
	 * @return array<string, array{mixed, bool}>
	 */
	public function data_display_column(): array {
		return array(
			'boolean true'      => array( true, true ),
			'custom label'      => array( 'Custom label', true ),
			'empty string'      => array( '', true ),
			'boolean false'     => array( false, false ),
			'integer'           => array( 0, false ),
			'null'              => array( null, false ),
		);
	}

	/**
	 * is_restricted_field() protects a few reserved post fields but nothing on users.
	 *
	 * @dataProvider data_restricted_fields
	 *
	 * @param string $field_slug  The field slug.
	 * @param string $object_type The object type.
	 * @param bool   $expected    Whether the field is restricted.
	 */
	public function test_is_restricted_field( string $field_slug, string $object_type, bool $expected ): void {
		$this->assertSame( $expected, $this->manager()->is_restricted_field( $field_slug, $object_type ) );
	}

	/**
	 * Data provider for is_restricted_field().
	 *
	 * @return array<string, array{string, string, bool}>
	 */
	public function data_restricted_fields(): array {
		return array(
			'post title on post'      => array( 'post_title', 'post', true ),
			'post author on post'     => array( 'post_author', 'post', true ),
			'ordinary field on post'  => array( 'featured', 'post', false ),
			'post title on page'      => array( 'post_title', 'page', true ),
			'post author on page'     => array( 'post_author', 'page', true ),
			'ordinary field on page'  => array( 'featured', 'page', false ),
			'post title on user'      => array( 'post_title', 'user', false ),
			'post author on user'     => array( 'post_author', 'user', false ),
			'post title on custom cpt' => array( 'post_title', 'book', true ),
		);
	}
}
