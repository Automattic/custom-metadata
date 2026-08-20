# Custom Metadata Manager

Stable tag: 0.8.0  
Requires at least: 6.4  
Tested up to: 7.1  
Requires PHP: 7.4  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  
Tags: custom fields, metadata, meta boxes, custom post types  
Contributors: automattic, GaryJ

A code-only developer plugin for registering custom fields on posts, pages, custom post types, users and comments.

## Description

Custom Metadata Manager gives developers a single, consistent API for adding custom fields to any object type in WordPress: `post`, `page`, any custom post type, `user` and `comment`. Rather than offering a point-and-click interface, it follows the WordPress approach of registering content in code, so your fields live alongside the rest of your theme or plugin.

Fields are grouped into metaboxes, stored through the standard WordPress metadata APIs, and remain available even if the plugin is later deactivated. You can render fields with the built-in field types or supply your own display and sanitisation callbacks, and you can include or exclude fields and groups for specific objects, either by ID or through a callback.

This approach is particularly useful when working across multiple environments (local, staging and production), because your field definitions travel with your code and need no database synchronisation.

## Installation

1. Install through the WordPress admin, or upload the plugin folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins menu in WordPress.
3. Add the code to register your custom groups and fields to your theme's `functions.php` or your own plugin.
4. Enjoy.

## Usage

Custom Metadata Manager is a developer-focused plugin: you register your fields and groups in code, typically from a theme's `functions.php` or your own plugin. The full API reference — object types, registering fields and groups, include and exclude rules, multifields and worked examples — is documented in [DEVELOPERS.md](DEVELOPERS.md).

## Screenshots

1. A custom metabox added to the post editing screen.
2. Custom fields registered against a user profile.
3. Grouped fields displayed in a metabox with descriptions.

## Frequently Asked Questions

### Why a code-based approach instead of a UI?

Because the UI approach has been done many times before, and a code-based approach aligns more closely with the existing WordPress model for registering content, such as post types and taxonomies.

This is a developer feature aimed at site builders. The main benefit becomes clear when you work across multiple environments (development, staging and production): you can replicate interfaces and features without worrying about database synchronisation.

### Why isn't the function just `add_metadata_field`? Why the `x_` prefix?

We are [namespacing our public functions](https://andrewnacin.com/2010/05/11/in-wordpress-prefix-everything/), and you should too. The `x_` prefix keeps the plugin's functions from clashing with anything else in WordPress core, themes or other plugins.

### How do I use this plugin?

See [DEVELOPERS.md](DEVELOPERS.md) for full instructions, and the `custom_metadata_examples.php` file for worked examples.

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) for how to set up a local environment, run the tests and submit a pull request.

## Support

Please report bugs and request features through the [GitHub issue tracker](https://github.com/Automattic/custom-metadata/issues).

## Credits

Custom Metadata Manager is maintained by [Automattic](https://automattic.com), and was originally created by [Stresslimit](https://stresslimit.com). Thank you to the many [community contributors](https://github.com/Automattic/custom-metadata/graphs/contributors) who have helped shape the plugin.

## Changelog

The full version history is recorded in [CHANGELOG.md](CHANGELOG.md).

## License

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2 or later, as published by the Free Software Foundation. It is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the [LICENSE](LICENSE) file for the full licence text.
