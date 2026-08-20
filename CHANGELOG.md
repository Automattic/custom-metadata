# Changelog for Custom Metadata Manager

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- Security: escape stored metadata values when they are output in admin list-table columns, fixing a stored cross-site scripting (XSS) issue. Output rendered by a custom `display_column_callback` must be escaped by that callback.
- Security: sanitize field values on save with a safe, field-type-aware default when a field has no `sanitize_callback`. Enabled by default; as a result, plain-text fields (such as `text` and `textarea`) no longer store raw HTML. Use a `wysiwyg` field for HTML, register a `sanitize_callback`, or filter `custom_metadata_manager_apply_default_sanitize` to opt out. See DEVELOPERS.md.
- Security: require the current user to be able to edit the object (`edit_post`, `edit_comment` or `edit_user`) before saving its metadata, in addition to the existing nonce check.
- Added the ability to group several fields as a `multifield`; see `x_add_metadata_multifield()`, props @greatislander, @rinatkhaziev and @PhilippSchreiber for their contributions there.
- Allow field types that save as multiples but do not display as cloneable or multiples.
- Added the `taxonomy_checkbox` and `taxonomy_multi_select` field types, props @greatislander.
- Made use of the `selected()` and `checked()` functions in WordPress instead of clumsy if statements.
- Limit or exclude groups and fields using a custom callback.
- Adjusted the copyright to include 2013 and to list "The Contributors" instead of specific individuals.
- Adjusted the list of contributors in the plugin.
- Adjusted the plugin URL and removed the donate URL.
- Adjusted files for code standards.
- Fixed PHP warning with empty values for date fields.
- Moved filtering of instance vars to `init` instead of on `construct` which runs too early.
- Added new field types: `number`, `email`, `telephone`, `datetimepicker`, `timepicker` and `link` (which uses the WP link manager).
- Added the ability to add a default value for certain field types.
- Added the ability to set a placeholder for certain fields.
- Updated the examples file.
- Rewrote the `upload` field to use the media manager from WordPress 3.5+. Note the `upload` field is now `readonly` by default (but can be set to `false` when you set up the field).
- Updated JavaScript to be up to standard with coding standards and be fully compatible with jQuery 1.9+.
- Replaced chosen.js with select2.js.
- Reformatted and cleaned up the CSS file.
- Added the ability for groups to display a description.
- Added the ability to limit capabilities for entire groups using `required_cap`.
- Converted the plugin class to a singleton.

## [0.7] - 2012-02-15

- Added the ability to have readonly fields with the new `readonly` parameter.

## [0.6] - 2012-01-13

- Note: the plugin now requires WordPress 3.3+ (chiefly for the wysiwyg and datepicker fields).
- Update and clean up the examples file.
- Properly enqueue admin CSS for WP 3.3+.
- Added a filter for the `CUSTOM_METADATA_MANAGER_URL` constant.
- Fix fields not appearing when editing users in WP 3.3+ (props @FolioVision).
- Now passing the `$value` for a `display_callback` (props @FolioVision).
- Use the new `wp_editor()` function (since WP 3.3+) instead of `the_editor()` (now deprecated).
- Wysiwyg fields are no longer cloneable (may be revisited in a future version).
- Note: metaboxes that have a wysiwyg field will break when moved; this is not a bug per se (may be revisited in a future version).
- Password fields are now cloneable.
- Added filters for most of the plugin's internal variables.
- Now using WordPress' built-in jQuery UI for the datepicker field.
- Updated the screenshots.
- Updated the instructions in readme.txt.

## [0.5.7] - 2012-01-13

- Pass additional params for `display_callback`.

## 0.5.6

- Fix bugs with datepicker.

## [0.5.5] - 2011-11-16

- Remove all whitespace.
- Fix some bugs with the tinymce field.

## [0.5.4] - 2011-10-23

- Fix display_callback for fields.

## [0.5.3] - 2011-09-11

- Removed PHP opening shorttags `<?` in favour of regular `<?php` tags, which caused parse errors on some servers.

## [0.5.2] - 2011-09-11

- Better TinyMCE implementation and added html/visual switch.
- Small CSS fixes and added inline documentation.
- Moved `DEFINE`s into `admin_init` so that they can be filtered more easily.

## [0.5.1] - 2011-09-06

- Bug fix with group context on add meta box.
- Remove few lines of old code left over from 0.4.

## [0.5] - 2011-09-01

- Making the changes from 0.4 public.
- Removed the ability to generate option pages; after further consideration this is out of scope for this project.
- Removed the attachment_list field, useless.
- Dates now save as unix timestamp.
- Taxonomy fields now save as both a custom field and as their proper taxonomy (will consider adding the ability to enable/disable this in a future version).
- Multiplied fields no longer save as a serialised array; instead they save as multiple metadata with the same key (metadata API supports multiples!) — remember to set the last param to false to get multiple values.
- Note: currently multiplied fields will display out of order after saving, however this should not affect anything else other than the admin, and should be fixed soon.
- Other small improvements.

## 0.4

- Enhanced the code which generates the different field types.
- Added new types: `password`, `upload`, `wysiwyg`, `datepicker`, `taxonomy_select`, `taxonomy_radio`, `attachment_list`.
- Added field multiplication ability.
- Metadata is now deleted if a value is empty.
- Can now also generate option pages which use a metabox interface.

## 0.3

- Can now limit or exclude fields or groups from specific ids.
- Added updated screenshots and new code samples!
- Bug fix: the custom display examples were not working well.
- Bug fix: fields not showing on "Add New" page. Thanks Jan Fabry!
- Bug fix: fields not showing on "My Profile" page. Thanks Mike Tew!

## 0.2

- Added a textarea field type.
- Added support for comments (you can now specify comments as an object type).
- Added basic styling for fields so that they look nice.

## 0.1

- Initial release.

[Unreleased]: https://github.com/Automattic/custom-metadata/compare/0.7...HEAD
[0.7]: https://github.com/Automattic/custom-metadata/compare/0.6...0.7
[0.6]: https://github.com/Automattic/custom-metadata/compare/0.5.7...0.6
[0.5.7]: https://github.com/Automattic/custom-metadata/compare/0.5.5...0.5.7
[0.5.5]: https://github.com/Automattic/custom-metadata/compare/0.5.4...0.5.5
[0.5.4]: https://github.com/Automattic/custom-metadata/compare/0.5.3...0.5.4
[0.5.3]: https://github.com/Automattic/custom-metadata/compare/0.5.2...0.5.3
[0.5.2]: https://github.com/Automattic/custom-metadata/compare/0.5.1...0.5.2
[0.5.1]: https://github.com/Automattic/custom-metadata/compare/0.5...0.5.1
[0.5]: https://github.com/Automattic/custom-metadata/releases/tag/0.5
