# Custom Metada Manager for WordPress

This code-only developer WordPress plugin allows you to add custom fields to your object types (post, pages, custom post types, users)

NOTE: This is a WordPress Plugin. We will sync changes between github and the [WordPress.org plugin repository](http://wordpress.org/extend/plugins/custom-metadata/).

# Installation

1. Install through the WordPress admin or upload the plugin folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the necessary code to register your custom groups and fields to your functions.php or plugin.
4. Enjoy!

# Frequently Asked Questions


#### Why a code-based approach instead of a UI?

Because the UI thing has [been](http://wordpress.org/extend/plugins/verve-meta-boxes/) [done](http://wordpress.org/extend/plugins/fresh-page/) [before](http://wordpress.org/extend/plugins/pods/). And this more closely aligns with the existing WordPress approach of registering new types of content (post types, taxonomies, etc.)

This is also a developer feature, aimed towards site builders. And real developers don't need UIs ;)

(But really, though, the main benefit of this fact comes into play when you're working with multiple environments, i.e. development/local, qa/staging, production. This approach makes it easy to replicate UIs and features without having to worry about database synchronization and other crazy things.)

For another really well-done, really powerful code-based plugin for managing custom fields, check out [Easy Custom Fields](http://wordpress.org/extend/plugins/easy-custom-fields/).


#### Why isn't the function just `add_metadata_field`? Do you really need the stupid `x_`? =

We're being good and ["namespacing" our public functions](http://andrewnacin.com/2010/05/11/in-wordpress-prefix-everything/). You should too.


#### How do I use this plugin?

There are usage instructions in the readme.txt file

# Changelog

= 0.5.3 =
* removed php opening shorttags `<?` in favor of regular `<?php` tags, which caused parse errors on some servers

= 0.5.2 =
* better tiny mce implementation and added html/visual switch
* small css fixes and added inline documentation
* moved DEFINEs in to admin_init() so that they can be filtered more easily

= 0.5.1 =
* Bug fix with group context on add meta box 
* Remove few lines of old code left-over from 0.4

= 0.5 =

* Making the changes from 0.4 public
* Removed ability to generate option pages; after further consideration this is out of scope for this project
* Removed attachment_list field, useless
* Dates now save as unix timestamp
* Taxonomy fields now save as both a custom field and as their proper taxonomy (will consider adding the ability to enable/disable this in a future version)
* Multiplied fields no longer save as a serialized array, instead they save as multiple metadata with the same key (metadata api supports multiples!) - remember to set the last param to false to get multiple values. 
* NOTE: currently multiplied fields will display out of order after saving, however this should not affect anything else other than the admin, should be fixed soon
* Other small improvements

= 0.4 =

* Enhanced the code which generates the different field types
* Added new types: password, upload, wysiwyg, datepicker, taxonomy_select, taxonomy_radio, attachment_list
* Added field multiplication ability
* Metadata is now deleted if a value is empty
* Can now also generate option pages which use a metabox interface

= 0.3 =

* Can now limit or exclude fields or groups from specific ids
* Added updated screenshots and new code samples!
* Bug fix: the custom display examples weren't working well
* Bug fix: fields not showing on "Add New" page. Thanks Jan Fabry!
* Bug fix: fields not showing on "My Profile" page. Thanks Mike Tew!

= 0.2 =

* Added a textarea field type
* Added support for comments (you can now specify comments as an object type)
* Added basic styling for fields so that they look nice

= 0.1 =

* Initial release

# Credits

This plugin is built and maintained by [Mo' Jangda](http://digitalize.ca/ "Mo' Jangda"), [Stresslimit Design](http://stresslimitdesign.com/about-our-wordpress-expertise "Stresslimit Design") & [Joachim Kudish](http://jkudish.com "Joachim Kudish")


# License

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to:

Free Software Foundation, Inc.  
51 Franklin Street, Fifth Floor,   
Boston, MA  
02110-1301, USA.