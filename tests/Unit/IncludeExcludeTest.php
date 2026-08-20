<?php
/**
 * Unit tests for the include/exclude engine that decides whether a field or group
 * is shown for a particular object.
 *
 * These methods are pure PHP (no WordPress calls), so they run as fast unit tests.
 *
 * @package Automattic\CustomMetadata
 */

declare( strict_types = 1 );

namespace Automattic\CustomMetadata\Tests\Unit;

use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * Covers custom_metadata_manager::is_thing_added_to_object() and the id-matching
 * helpers it delegates to: does_id_array_match_object() and does_id_match_object().
 */
final class IncludeExcludeTest extends TestCase {

	/**
	 * A fresh manager instance (the constructor registers no hooks).
	 *
	 * @return \custom_metadata_manager
	 */
	private function manager(): \custom_metadata_manager {
		return new \custom_metadata_manager();
	}

	/**
	 * Build a field/group "thing" object from a set of properties.
	 *
	 * @param array<string, mixed> $props Properties such as `include` or `exclude`.
	 * @return object
	 */
	private function thing( array $props = array() ): object {
		return (object) $props;
	}

	/**
	 * With neither include nor exclude set, a thing is added to every object.
	 */
	public function test_added_by_default_when_no_rules(): void {
		$this->assertTrue(
			$this->manager()->is_thing_added_to_object( 'field', $this->thing(), 'post', 123 )
		);
	}

	/**
	 * An exclude rule hides the thing from the matching object.
	 */
	public function test_exclude_id_blocks_matching_object(): void {
		$thing = $this->thing( array( 'exclude' => 123 ) );
		$this->assertFalse(
			$this->manager()->is_thing_added_to_object( 'field', $thing, 'post', 123 )
		);
	}

	/**
	 * An exclude rule leaves other objects untouched.
	 */
	public function test_exclude_id_allows_non_matching_object(): void {
		$thing = $this->thing( array( 'exclude' => 123 ) );
		$this->assertTrue(
			$this->manager()->is_thing_added_to_object( 'field', $thing, 'post', 456 )
		);
	}

	/**
	 * An include rule shows the thing only on the matching object.
	 */
	public function test_include_id_allows_matching_object(): void {
		$thing = $this->thing( array( 'include' => 123 ) );
		$this->assertTrue(
			$this->manager()->is_thing_added_to_object( 'field', $thing, 'post', 123 )
		);
	}

	/**
	 * An include rule hides the thing from every other object.
	 */
	public function test_include_id_blocks_non_matching_object(): void {
		$thing = $this->thing( array( 'include' => 123 ) );
		$this->assertFalse(
			$this->manager()->is_thing_added_to_object( 'field', $thing, 'post', 456 )
		);
	}

	/**
	 * When both rules are present, exclude is evaluated and wins on a match.
	 */
	public function test_exclude_takes_precedence_when_matching(): void {
		$thing = $this->thing(
			array(
				'exclude' => 123,
				'include' => 123,
			)
		);
		$this->assertFalse(
			$this->manager()->is_thing_added_to_object( 'field', $thing, 'post', 123 )
		);
	}

	/**
	 * When exclude is present, include is never consulted, even if include would block.
	 */
	public function test_include_ignored_when_exclude_present(): void {
		// exclude 999 does not match object 123, so the exclude branch returns "added".
		// include 999 would block object 123 if it were consulted, so a true result
		// proves include was skipped entirely.
		$thing = $this->thing(
			array(
				'exclude' => 999,
				'include' => 999,
			)
		);
		$this->assertTrue(
			$this->manager()->is_thing_added_to_object( 'field', $thing, 'post', 123 )
		);
	}

	/**
	 * A callable exclude that returns true hides the thing.
	 */
	public function test_exclude_callable_returning_true_blocks(): void {
		$thing = $this->thing(
			array(
				'exclude' => static function () {
					return true;
				},
			)
		);
		$this->assertFalse(
			$this->manager()->is_thing_added_to_object( 'field', $thing, 'post', 1 )
		);
	}

	/**
	 * A callable exclude that returns false leaves the thing in place.
	 */
	public function test_exclude_callable_returning_false_allows(): void {
		$thing = $this->thing(
			array(
				'exclude' => static function () {
					return false;
				},
			)
		);
		$this->assertTrue(
			$this->manager()->is_thing_added_to_object( 'field', $thing, 'post', 1 )
		);
	}

	/**
	 * A callable include that returns true shows the thing.
	 */
	public function test_include_callable_returning_true_allows(): void {
		$thing = $this->thing(
			array(
				'include' => static function () {
					return true;
				},
			)
		);
		$this->assertTrue(
			$this->manager()->is_thing_added_to_object( 'field', $thing, 'post', 1 )
		);
	}

	/**
	 * A callable include that returns false hides the thing.
	 */
	public function test_include_callable_returning_false_blocks(): void {
		$thing = $this->thing(
			array(
				'include' => static function () {
					return false;
				},
			)
		);
		$this->assertFalse(
			$this->manager()->is_thing_added_to_object( 'field', $thing, 'post', 1 )
		);
	}

	/**
	 * The callback receives the documented five arguments, in order.
	 */
	public function test_callable_receives_expected_arguments(): void {
		$received = array();
		$thing    = $this->thing(
			array(
				'include' => static function ( $thing_slug, $thing, $object_type, $object_id, $object_slug ) use ( &$received ) {
					$received = array( $thing_slug, $thing, $object_type, $object_id, $object_slug );
					return true;
				},
			)
		);

		$result = $this->manager()->is_thing_added_to_object( 'my_field', $thing, 'post', 42, 'hello-world' );

		$this->assertTrue( $result );
		$this->assertSame( array( 'my_field', $thing, 'post', 42, 'hello-world' ), $received );
	}

	/**
	 * does_id_array_match_object() understands scalars, flat lists, and per-type maps.
	 *
	 * @dataProvider data_id_array_matches
	 *
	 * @param mixed  $id_array    The include/exclude definition.
	 * @param string $object_type The object type being checked.
	 * @param int    $object_id   The object ID being checked.
	 * @param string $object_slug The object slug being checked.
	 * @param bool   $expected    Whether a match is expected.
	 */
	public function test_does_id_array_match_object( $id_array, string $object_type, int $object_id, string $object_slug, bool $expected ): void {
		$this->assertSame(
			$expected,
			$this->manager()->does_id_array_match_object( $id_array, $object_type, $object_id, $object_slug )
		);
	}

	/**
	 * Data provider for does_id_array_match_object().
	 *
	 * @return array<string, array{mixed, string, int, string, bool}>
	 */
	public function data_id_array_matches(): array {
		return array(
			'scalar id matches'             => array( 123, 'post', 123, '', true ),
			'scalar id does not match'      => array( 123, 'post', 999, '', false ),
			'flat list contains id'         => array( array( 1, 2, 3 ), 'post', 2, '', true ),
			'flat list missing id'          => array( array( 1, 2, 3 ), 'post', 9, '', false ),
			'map keyed by type matches'     => array( array( 'post' => 7, 'user' => 9 ), 'post', 7, '', true ),
			'map keyed by type no match'    => array( array( 'post' => 7 ), 'post', 8, '', false ),
			'map of nested list matches'    => array( array( 'user' => array( 4, 5, 6 ) ), 'user', 5, '', true ),
			'map of nested list no match'   => array( array( 'user' => array( 4, 5, 6 ) ), 'user', 9, '', false ),
			'string matches slug'           => array( 'hello', 'post', 0, 'hello', true ),
			'string does not match slug'    => array( 'hello', 'post', 0, 'world', false ),
		);
	}

	/**
	 * does_id_match_object() matches ints against the ID and strings against the slug.
	 *
	 * @dataProvider data_id_matches
	 *
	 * @param mixed  $id          The candidate id/slug.
	 * @param mixed  $object_id   The object ID being checked.
	 * @param string $object_slug The object slug being checked.
	 * @param bool   $expected    Whether a match is expected.
	 */
	public function test_does_id_match_object( $id, $object_id, string $object_slug, bool $expected ): void {
		$this->assertSame(
			$expected,
			$this->manager()->does_id_match_object( $id, $object_id, $object_slug )
		);
	}

	/**
	 * Data provider for does_id_match_object().
	 *
	 * Documents two non-obvious behaviours: integer comparison is loose (so a numeric
	 * string ID target still matches), and a numeric *string* is treated as a slug,
	 * not an ID.
	 *
	 * @return array<string, array{mixed, mixed, string, bool}>
	 */
	public function data_id_matches(): array {
		return array(
			'int equals id'                    => array( 5, 5, '', true ),
			'int does not equal id'            => array( 5, 6, '', false ),
			'int loosely equals numeric id'    => array( 5, '5', '', true ),
			'string equals slug'               => array( 'foo', 0, 'foo', true ),
			'string does not equal slug'       => array( 'foo', 0, 'bar', false ),
			'numeric string is a slug not id'  => array( '5', 5, '', false ),
			'numeric string matches slug'      => array( '5', 0, '5', true ),
			'float never matches'              => array( 1.5, 1.5, '', false ),
			'null never matches'               => array( null, null, '', false ),
		);
	}
}
