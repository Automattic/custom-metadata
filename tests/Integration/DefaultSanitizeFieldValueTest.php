<?php
/**
 * Tests for the field-type-aware default input sanitizer.
 *
 * @package Automattic\CustomMetadata
 */

declare( strict_types = 1 );

namespace Automattic\CustomMetadata\Tests\Integration;

use Yoast\WPTestUtils\WPIntegration\TestCase;

/**
 * Covers custom_metadata_manager::_default_sanitize_field_value(), and its use from
 * _sanitize_field_value() when a field has no explicit sanitize_callback.
 */
final class DefaultSanitizeFieldValueTest extends TestCase {

	/**
	 * The plugin instance under test.
	 *
	 * @return \custom_metadata_manager
	 */
	private function manager(): \custom_metadata_manager {
		return \custom_metadata_manager::instance();
	}

	/**
	 * Build a minimal field object of a given type.
	 *
	 * @param string $field_type The field type.
	 * @return object
	 */
	private function field( string $field_type ): object {
		return (object) array(
			'field_type'        => $field_type,
			'sanitize_callback' => '',
		);
	}

	/**
	 * A plain text field strips markup entirely.
	 */
	public function test_text_field_strips_tags(): void {
		$result = $this->manager()->_default_sanitize_field_value( $this->field( 'text' ), '<script>alert(1)</script>Hello' );
		$this->assertSame( 'Hello', $result );
	}

	/**
	 * A plain text field collapses newlines (single-line semantics).
	 */
	public function test_text_field_collapses_newlines(): void {
		$result = $this->manager()->_default_sanitize_field_value( $this->field( 'text' ), "one\ntwo" );
		$this->assertStringNotContainsString( "\n", $result );
	}

	/**
	 * A textarea preserves newlines but still strips scripts.
	 */
	public function test_textarea_field_preserves_newlines_but_strips_scripts(): void {
		$result = $this->manager()->_default_sanitize_field_value( $this->field( 'textarea' ), "line1\nline2 <script>x</script>" );
		$this->assertStringContainsString( "\n", $result );
		$this->assertStringNotContainsString( '<script>', $result );
	}

	/**
	 * A wysiwyg field keeps post-safe HTML but drops scripts.
	 */
	public function test_wysiwyg_field_keeps_safe_html_but_strips_scripts(): void {
		$result = $this->manager()->_default_sanitize_field_value( $this->field( 'wysiwyg' ), '<p>ok</p><script>alert(1)</script>' );
		$this->assertStringContainsString( '<p>ok</p>', $result );
		$this->assertStringNotContainsString( '<script>', $result );
	}

	/**
	 * A valid email is returned unchanged.
	 */
	public function test_email_field_returns_valid_email_unchanged(): void {
		$result = $this->manager()->_default_sanitize_field_value( $this->field( 'email' ), 'test@example.com' );
		$this->assertSame( 'test@example.com', $result );
	}

	/**
	 * URL fields neutralize the javascript: protocol.
	 *
	 * @dataProvider data_url_fields
	 *
	 * @param string $field_type A URL-bearing field type.
	 */
	public function test_url_field_neutralizes_javascript_protocol( string $field_type ): void {
		$result = $this->manager()->_default_sanitize_field_value( $this->field( $field_type ), 'javascript:alert(1)' );
		$this->assertStringNotContainsString( 'javascript', $result );
	}

	/**
	 * Data provider for URL-bearing field types.
	 *
	 * @return array<string, array{string}>
	 */
	public function data_url_fields(): array {
		return array(
			'link'   => array( 'link' ),
			'upload' => array( 'upload' ),
		);
	}

	/**
	 * A colorpicker accepts a valid hex color.
	 */
	public function test_colorpicker_field_accepts_valid_hex(): void {
		$result = $this->manager()->_default_sanitize_field_value( $this->field( 'colorpicker' ), '#ff0000' );
		$this->assertSame( '#ff0000', $result );
	}

	/**
	 * A colorpicker rejects markup.
	 */
	public function test_colorpicker_field_rejects_markup(): void {
		$result = $this->manager()->_default_sanitize_field_value( $this->field( 'colorpicker' ), '<script>alert(1)</script>' );
		$this->assertStringNotContainsString( '<script>', $result );
	}

	/**
	 * Number fields keep numeric values (including floats and negatives) and reject the rest.
	 *
	 * @dataProvider data_numbers
	 *
	 * @param string $input    The submitted value.
	 * @param mixed  $expected The expected stored value.
	 */
	public function test_number_field_keeps_numeric_values( string $input, $expected ): void {
		$this->assertSame( $expected, $this->manager()->_default_sanitize_field_value( $this->field( 'number' ), $input ) );
	}

	/**
	 * Data provider for number handling.
	 *
	 * @return array<string, array{string, mixed}>
	 */
	public function data_numbers(): array {
		return array(
			'float'        => array( '3.5', 3.5 ),
			'negative'     => array( '-3', -3 ),
			'leading zero' => array( '007', 7 ),
			'non-numeric'  => array( 'abc', '' ),
			'empty'        => array( '', '' ),
		);
	}

	/**
	 * Password fields are stored verbatim (never mangled).
	 */
	public function test_password_field_is_not_mangled(): void {
		$secret = '<b>p@ss & word</b>';
		$this->assertSame( $secret, $this->manager()->_default_sanitize_field_value( $this->field( 'password' ), $secret ) );
	}

	/**
	 * Date fields pass through unchanged (already a timestamp by this point).
	 *
	 * @dataProvider data_date_fields
	 *
	 * @param string $field_type A date/time field type.
	 */
	public function test_date_fields_pass_through_unchanged( string $field_type ): void {
		$this->assertSame( 1234567890, $this->manager()->_default_sanitize_field_value( $this->field( $field_type ), 1234567890 ) );
	}

	/**
	 * Data provider for date/time field types.
	 *
	 * @return array<string, array{string}>
	 */
	public function data_date_fields(): array {
		return array(
			'datepicker'     => array( 'datepicker' ),
			'datetimepicker' => array( 'datetimepicker' ),
			'timepicker'     => array( 'timepicker' ),
		);
	}

	/**
	 * An unrecognized field type falls back to plain-text sanitization.
	 */
	public function test_unknown_field_type_falls_back_to_text_sanitization(): void {
		$result = $this->manager()->_default_sanitize_field_value( $this->field( 'something_unrecognized' ), '<script>x</script>keep' );
		$this->assertSame( 'keep', $result );
	}

	/**
	 * Array (multi-value) fields are sanitized element by element.
	 */
	public function test_array_values_are_sanitized_recursively(): void {
		$result = $this->manager()->_default_sanitize_field_value(
			$this->field( 'multi_select' ),
			array( '<b>a</b>', 'b<script>x</script>' )
		);
		$this->assertSame( array( 'a', 'b' ), $result );
	}

	/**
	 * Array keys are preserved during recursive sanitization.
	 */
	public function test_array_preserves_keys(): void {
		$result = $this->manager()->_default_sanitize_field_value(
			$this->field( 'taxonomy_checkbox' ),
			array( 3 => 'termA', 5 => '<i>termB</i>' )
		);
		$this->assertSame( array( 3 => 'termA', 5 => 'termB' ), $result );
	}

	/**
	 * _sanitize_field_value() applies the default sanitizer when no callback is set.
	 */
	public function test_sanitize_field_value_applies_default_when_no_callback(): void {
		$result = $this->manager()->_sanitize_field_value( 'x_slug', $this->field( 'text' ), 'post', 1, '<script>alert(1)</script>Hi' );
		$this->assertSame( 'Hi', $result );
	}

	/**
	 * The default sanitizer can be switched off globally via its filter.
	 */
	public function test_default_sanitizer_can_be_disabled_via_filter(): void {
		add_filter( 'custom_metadata_manager_apply_default_sanitize', '__return_false' );
		$raw    = '<script>alert(1)</script>';
		$result = $this->manager()->_sanitize_field_value( 'x_slug', $this->field( 'text' ), 'post', 1, $raw );
		remove_filter( 'custom_metadata_manager_apply_default_sanitize', '__return_false' );

		$this->assertSame( $raw, $result );
	}
}
