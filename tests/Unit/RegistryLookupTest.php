<?php
/**
 * Unit tests for the registry read layer: how the plugin looks up registered
 * object types, groups and fields from its in-memory metadata structure.
 *
 * The registry is populated here by hand rather than through the public
 * registration API, because the registration path calls WordPress functions
 * (sanitize_key(), wp_parse_args(), apply_filters(), get_post_types()). Those
 * belong in the integration suite; the lookups below are pure array navigation.
 *
 * @package Automattic\CustomMetadata
 */

declare( strict_types = 1 );

namespace Automattic\CustomMetadata\Tests\Unit;

use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * Covers the is_registered_*(), get_group(), get_field(s)_* and
 * _multifield_exists_for_group_object() lookups.
 */
final class RegistryLookupTest extends TestCase {

	/**
	 * A manager whose registry contains two post groups (one with a multifield)
	 * and an empty user object type.
	 *
	 * @return \custom_metadata_manager
	 */
	private function manager(): \custom_metadata_manager {
		$manager = new \custom_metadata_manager();

		$details = (object) array(
			'label'  => 'Details',
			'fields' => array(
				'subtitle'             => (object) array( 'label' => 'Subtitle' ),
				'byline'               => (object) array( 'label' => 'Byline' ),
				'_x_multifield_authors' => (object) array(
					'label'      => 'Authors',
					'multifield' => true,
				),
			),
		);

		$seo = (object) array(
			'label'  => 'SEO',
			'fields' => array(
				'meta_desc' => (object) array( 'label' => 'Meta description' ),
			),
		);

		$manager->metadata = array(
			'post' => array(
				'details' => $details,
				'seo'     => $seo,
			),
			'user' => array(),
		);

		return $manager;
	}

	/**
	 * Object types present in the registry are recognised; unknown ones are not.
	 */
	public function test_is_registered_object_type(): void {
		$manager = $this->manager();

		$this->assertTrue( $manager->is_registered_object_type( 'post' ) );
		$this->assertTrue( $manager->is_registered_object_type( 'user' ), 'An empty object type is still registered.' );
		$this->assertFalse( $manager->is_registered_object_type( 'comment' ) );
	}

	/**
	 * get_object_types() returns the registered object type keys.
	 */
	public function test_get_object_types(): void {
		$this->assertSame( array( 'post', 'user' ), $this->manager()->get_object_types() );
	}

	/**
	 * Group membership is reported per object type.
	 */
	public function test_is_group_in_object_type(): void {
		$manager = $this->manager();

		$this->assertTrue( $manager->is_group_in_object_type( 'details', 'post' ) );
		$this->assertFalse( $manager->is_group_in_object_type( 'details', 'user' ) );
		$this->assertFalse( $manager->is_group_in_object_type( 'missing', 'post' ) );
	}

	/**
	 * is_registered_group() requires both a known object type and a known group.
	 */
	public function test_is_registered_group(): void {
		$manager = $this->manager();

		$this->assertTrue( $manager->is_registered_group( 'seo', 'post' ) );
		$this->assertFalse( $manager->is_registered_group( 'seo', 'user' ) );
		$this->assertFalse( $manager->is_registered_group( 'seo', 'comment' ), 'Unknown object type means unregistered.' );
	}

	/**
	 * get_group() returns the stored group object, or null when absent.
	 */
	public function test_get_group(): void {
		$manager = $this->manager();

		$group = $manager->get_group( 'details', 'post' );
		$this->assertIsObject( $group );
		$this->assertSame( 'Details', $group->label );

		$this->assertNull( $manager->get_group( 'missing', 'post' ) );
		$this->assertNull( $manager->get_group( 'details', 'comment' ) );
	}

	/**
	 * get_groups_in_object_type() returns the groups, and an empty array for
	 * unknown object types.
	 */
	public function test_get_groups_in_object_type(): void {
		$manager = $this->manager();

		$this->assertSame( array( 'details', 'seo' ), array_keys( $manager->get_groups_in_object_type( 'post' ) ) );
		$this->assertSame( array(), $manager->get_groups_in_object_type( 'comment' ) );
	}

	/**
	 * get_fields_in_group() returns that group's fields, keyed by slug.
	 */
	public function test_get_fields_in_group(): void {
		$manager = $this->manager();

		$fields = $manager->get_fields_in_group( 'details', 'post' );
		$this->assertSame( array( 'subtitle', 'byline', '_x_multifield_authors' ), array_keys( $fields ) );

		$this->assertSame( array(), $manager->get_fields_in_group( 'missing', 'post' ) );
	}

	/**
	 * get_fields_in_object_type() merges the fields from every group in the type.
	 */
	public function test_get_fields_in_object_type_merges_groups(): void {
		$manager = $this->manager();

		$fields = $manager->get_fields_in_object_type( 'post' );
		$this->assertSame( array( 'subtitle', 'byline', '_x_multifield_authors', 'meta_desc' ), array_keys( $fields ) );

		$this->assertSame( array(), $manager->get_fields_in_object_type( 'user' ) );
	}

	/**
	 * Single-field getters return the stored object, or null when absent.
	 */
	public function test_get_single_field_getters(): void {
		$manager = $this->manager();

		$this->assertSame( 'Subtitle', $manager->get_single_field_in_group( 'subtitle', 'details', 'post' )->label );
		$this->assertNull( $manager->get_single_field_in_group( 'missing', 'details', 'post' ) );

		$this->assertSame( 'Meta description', $manager->get_single_field_in_object_type( 'meta_desc', 'post' )->label );
		$this->assertNull( $manager->get_single_field_in_object_type( 'missing', 'post' ) );
	}

	/**
	 * is_registered_field() scoped to a group checks that exact group.
	 */
	public function test_is_registered_field_within_group(): void {
		$manager = $this->manager();

		$this->assertTrue( $manager->is_registered_field( 'subtitle', 'details', 'post' ) );
		$this->assertFalse( $manager->is_registered_field( 'subtitle', 'seo', 'post' ), 'Field lives in a different group.' );
		$this->assertFalse( $manager->is_registered_field( 'missing', 'details', 'post' ) );
	}

	/**
	 * is_registered_field() without a group searches the whole object type.
	 */
	public function test_is_registered_field_across_object_type(): void {
		$manager = $this->manager();

		$this->assertTrue( $manager->is_registered_field( 'meta_desc', '', 'post' ) );
		$this->assertFalse( $manager->is_registered_field( 'missing', '', 'post' ) );
	}

	/**
	 * is_registered_field() rejects an empty object type (enforced since #158).
	 */
	public function test_is_registered_field_requires_an_object_type(): void {
		$this->expectException( \InvalidArgumentException::class );

		// An empty object type is rejected, even though the signature defaults it to ''.
		$this->manager()->is_registered_field( 'meta_desc', '', '' );
	}

	/**
	 * _multifield_exists_for_group_object() finds a multifield by its bare slug.
	 */
	public function test_multifield_exists_for_group_object(): void {
		$manager = $this->manager();

		$this->assertTrue( $manager->_multifield_exists_for_group_object( 'authors', 'details', 'post' ) );
		$this->assertFalse( $manager->_multifield_exists_for_group_object( 'authors', 'seo', 'post' ) );
		$this->assertFalse( $manager->_multifield_exists_for_group_object( 'missing', 'details', 'post' ) );
	}
}
