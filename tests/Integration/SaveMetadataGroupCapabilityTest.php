<?php
/**
 * Tests for the capability gate added to save_metadata_group().
 *
 * @package Automattic\CustomMetadata
 */

declare( strict_types = 1 );

namespace Automattic\CustomMetadata\Tests\Integration;

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * A valid nonce alone must not authorize a metadata write. save_metadata_group()
 * additionally requires the current user to be able to edit the target object.
 *
 * The test double below neutralizes the nonce gate and records which fields were
 * saved, so the only thing deciding whether the save proceeds is the capability
 * check under test (which uses the real WordPress capability system).
 */
final class SaveMetadataGroupCapabilityTest extends TestCase {

	/**
	 * Build a spy that reports whether save_metadata_group() reached the save step.
	 *
	 * @return \custom_metadata_manager
	 */
	private function make_spy() {
		return new class() extends \custom_metadata_manager {

			/**
			 * Slugs of fields that were saved.
			 *
			 * @var array<int, string>
			 */
			public $saved_fields = array();

			/**
			 * Pretend the nonce always verifies, so only the capability gate matters.
			 *
			 * @param string $group_slug  Group slug.
			 * @param string $object_type Object type.
			 * @return bool
			 */
			public function verify_group_nonce( $group_slug, $object_type ) {
				return true;
			}

			/**
			 * Provide a single, non-multifield field to attempt to save.
			 *
			 * @param string $group_slug  Group slug.
			 * @param string $object_type Object type.
			 * @return array<string, object>
			 */
			public function get_fields_in_group( $group_slug, $object_type ) {
				return array( 'x_spy_field' => (object) array( 'multifield' => false ) );
			}

			/**
			 * Record the save instead of touching the database.
			 *
			 * @param string $field_slug  Field slug.
			 * @param object $field       Field object.
			 * @param string $object_type Object type.
			 * @param int    $object_id   Object ID.
			 */
			public function save_metadata_field( $field_slug, $field, $object_type, $object_id ) {
				$this->saved_fields[] = $field_slug;
			}
		};
	}

	/**
	 * A subscriber cannot save post metadata on a post they cannot edit.
	 */
	public function test_post_save_blocked_for_user_without_edit_post(): void {
		$author_id  = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$post_id    = self::factory()->post->create( array( 'post_author' => $author_id ) );
		$subscriber = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber );

		$spy = $this->make_spy();
		$spy->save_metadata_group( 'x_group', (object) array(), 'post', $post_id );

		$this->assertSame( array(), $spy->saved_fields );
	}

	/**
	 * An administrator can save post metadata.
	 */
	public function test_post_save_allowed_for_admin(): void {
		$admin   = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$post_id = self::factory()->post->create( array( 'post_author' => $admin ) );
		wp_set_current_user( $admin );

		$spy = $this->make_spy();
		$spy->save_metadata_group( 'x_group', (object) array(), 'post', $post_id );

		$this->assertSame( array( 'x_spy_field' ), $spy->saved_fields );
	}

	/**
	 * A user may save custom fields on their own profile (edit_user allows self-edit).
	 */
	public function test_user_profile_save_allowed_for_own_profile(): void {
		$subscriber = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber );

		$spy = $this->make_spy();
		$spy->save_metadata_group( 'x_group', (object) array(), 'user', $subscriber );

		$this->assertSame( array( 'x_spy_field' ), $spy->saved_fields );
	}

	/**
	 * A subscriber must not save custom fields on another user's profile.
	 */
	public function test_user_profile_save_blocked_for_other_users_profile(): void {
		$subscriber = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		$other      = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber );

		$spy = $this->make_spy();
		$spy->save_metadata_group( 'x_group', (object) array(), 'user', $other );

		$this->assertSame( array(), $spy->saved_fields );
	}

	/**
	 * A subscriber cannot save comment metadata.
	 */
	public function test_comment_save_blocked_for_subscriber(): void {
		$post_id    = self::factory()->post->create();
		$comment_id = self::factory()->comment->create( array( 'comment_post_ID' => $post_id ) );
		$subscriber = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber );

		$spy = $this->make_spy();
		$spy->save_metadata_group( 'x_group', (object) array(), 'comment', $comment_id );

		$this->assertSame( array(), $spy->saved_fields );
	}
}
