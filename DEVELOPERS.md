# Developer Guide

Custom Metadata Manager is a code-only plugin: you register custom fields and groups in code rather than through an admin interface. This guide documents the full API. Registrations are typically added from a theme's `functions.php` or from your own plugin.

## Object types

The main idea behind this plugin is to have a single API to work with regardless of the object type. Custom Metadata Manager works with `user`, `comment` and any built-in or custom post type, such as `post` and `page`.

## Registering your fields

For the sake of performance (and to avoid potential race conditions), always register your custom fields on the `custom_metadata_manager_init_metadata` hook. This keeps your front end free of unnecessary processing and ensures your fields are registered safely.

~~~php
add_action( 'custom_metadata_manager_init_metadata', 'my_theme_init_custom_fields' );

function my_theme_init_custom_fields() {
	x_add_metadata_field( 'my_field', array( 'user', 'post' ) );
}
~~~

## Getting the data

Retrieve values as you normally would with the `get_metadata` function. Custom Metadata Manager stores all data through the WordPress metadata APIs under the slug you provide, so your data stays safe and accessible even if you deactivate the plugin.

~~~php
// Returns the 'featured' metadata value for the current post.
$value = get_metadata( 'post', get_the_ID(), 'featured', true );
~~~

## Adding metadata groups

A group is essentially a metabox that groups several fields together. Register the group before adding any fields to it.

~~~php
x_add_metadata_group( $slug, $object_types, $args );
~~~

### Parameters

* `$slug` (string) The key under which the group is registered.
* `$object_types` (string|array) The object types the group should be added to. Supported: `post`, `page`, any custom post type, `user` and `comment`.

### Options and overrides

~~~php
$args = array(
	'label'    => $group_slug, // Label for the group.
	'context'  => 'normal',    // (post only)
	'priority' => 'default',   // (post only)
	'autosave' => false,       // (post only) Should the group be saved in autosave? NOT IMPLEMENTED YET!
	'exclude'  => '',          // See "Include and exclude" below.
	'include'  => '',          // See "Include and exclude" below.
);
~~~

## Adding metadata fields

~~~php
x_add_metadata_field( $slug, $object_types, $args );
~~~

### Parameters

* `$slug` (string) The key under which the metadata is stored. For post types, prefix the slug with an underscore (for example, `_hidden`) to hide it from the Custom Fields box.
* `$object_types` (string|array) The object types the field should be added to. Supported: `post`, `page`, any custom post type, `user` and `comment`.

### Options and overrides

~~~php
$args = array(
	'group'                   => '',     // Slug of the group to add the field to. Register it with x_add_metadata_group first.
	'field_type'              => 'text', // 'text', 'textarea', 'password', 'checkbox', 'radio', 'select', 'upload', 'wysiwyg', 'datepicker', 'taxonomy_select', 'taxonomy_radio'.
	'label'                   => '',      // Label for the field.
	'description'             => '',      // Description shown below the input.
	'values'                  => array(), // Values for select and radio fields, as an associative array.
	'display_callback'        => '',      // Callback to render the field.
	'sanitize_callback'       => '',      // Callback to sanitize data before it is saved. When empty, a safe field-type default is applied — see "Sanitization and escaping".
	'display_column'          => false,   // Add the field as a column when viewing all posts.
	'display_column_callback' => '',      // Callback to render output for the custom column.
	'required_cap'            => '',      // The capability required to view and edit the field.
	'exclude'                 => '',      // See "Include and exclude" below.
	'include'                 => '',      // See "Include and exclude" below.
	'multiple'                => false,   // Can the field be duplicated with the click of a button?
	'readonly'                => false,   // Make the field read-only (works with text, textarea, password, upload and datepicker fields).
);
~~~

## Sanitization and escaping

Custom Metadata Manager sanitizes values on input and escapes them on output, so untrusted field values cannot become stored cross-site scripting (XSS). Both happen automatically; you only need to think about them when you supply your own callbacks.

### Input sanitization

When a field has no `sanitize_callback`, the plugin applies a safe default based on the field type before saving:

| Field type | Default sanitizer |
| --- | --- |
| `text`, `tel`, `checkbox`, `radio`, `select` (and any unrecognized type) | `sanitize_text_field()` |
| `textarea` | `sanitize_textarea_field()` (newlines preserved) |
| `wysiwyg` | `wp_kses_post()` (post-safe HTML kept; scripts, event handlers and bad protocols removed) |
| `email` | `sanitize_email()` |
| `number` | numeric cast (floats and negatives preserved) |
| `link`, `upload` | `esc_url_raw()` |
| `colorpicker` | `sanitize_hex_color()` |
| `datepicker`, `datetimepicker`, `timepicker` | stored as a Unix timestamp |
| `password` | stored verbatim |

Multi-value fields (such as `multi_select` and the `taxonomy_*` types) are sanitized element by element.

A consequence of this is that **plain-text fields no longer store raw HTML**. If you need to store markup, use a `wysiwyg` field, register your own `sanitize_callback`, or disable the default entirely:

~~~php
// Turn the built-in default sanitizer off globally (not recommended).
add_filter( 'custom_metadata_manager_apply_default_sanitize', '__return_false' );
~~~

A field's own `sanitize_callback` always takes precedence over the default.

### Output escaping

Values shown in an admin list-table column (`display_column => true`) are escaped for you. If you render a column with a `display_column_callback`, or a field with a `display_callback`, **you are responsible for escaping your own output** — escape late, using `esc_html()`, `esc_attr()`, `esc_url()` or `wp_kses_post()` as appropriate for the context. When you read values elsewhere (for example in a theme template via `get_metadata()`), escape them on output as you would any stored value.

### Who can save

A metadata write requires both a valid nonce from the field's metabox **and** the current user being able to edit the object being saved (`edit_post`, `edit_comment` or `edit_user` as appropriate). The nonce proves where the request came from; it is not treated as authorization on its own.

## Include and exclude

You can exclude fields and groups from specific objects. For example, the following shows `field-1` for all posts except post 123:

~~~php
$args = array(
	'exclude' => 123,
);
x_add_metadata_field( 'field-1', 'post', $args );
~~~

Alternatively, you can limit ("include") fields and groups to specific objects. The following shows `group-1` only on post 456:

~~~php
$args = array(
	'include' => 456,
);
x_add_metadata_group( 'group-1', 'post', $args );
~~~

You can pass an array of IDs:

~~~php
$args = array(
	'include' => array( 123, 456, 789 ),
);
~~~

With multiple object types, pass an associative array:

~~~php
$args = array(
	'exclude' => array(
		'post' => 123,
		'user' => array( 123, 456, 789 ),
	),
);
~~~

You can also pass a callback to include or exclude objects programmatically:

~~~php
$args = array(
	'exclude' => function( $thing_slug, $thing, $object_type, $object_id, $object_slug ) {
		// Exclude from all posts in the "aside" category.
		return in_category( 'aside', $object_id );
	},
);
~~~

~~~php
$args = array(
	'include' => function( $thing_slug, $thing, $object_type, $object_id, $object_slug ) {
		// Include for posts that are not yet published.
		$post = get_post( $object_id );
		return 'publish' !== $post->post_status;
	},
);
~~~

## Grouping fields as a multifield

Use `x_add_metadata_multifield()` to group several fields together as a single repeatable unit. See `custom_metadata_examples.php` for a full example.

## Examples

For worked examples, see the `custom_metadata_examples.php` file included with the plugin. Add the following constant to your `wp-config.php` to see it in action:

~~~php
define( 'CUSTOM_METADATA_MANAGER_DEBUG', true );
~~~
