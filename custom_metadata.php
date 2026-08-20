<?php
/**
 * Plugin Name:       Custom Metadata Manager
 * Plugin URI:        https://wordpress.org/plugins/custom-metadata/
 * Description:       An easy way to add custom fields to your object types (posts, pages, custom post types, users, comments).
 * Version:           0.8-dev
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Automattic, Stresslimit & Contributors
 * Author URI:        https://github.com/Automattic/custom-metadata/
 * Text Domain:       custom-metadata
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Automattic\CustomMetadata
 */

/*
Copyright 2010-2013 The Contributors

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set this to true in your wp-config.php file to enable debug/test mode.
 */
if ( ! defined( 'CUSTOM_METADATA_MANAGER_DEBUG' ) ) {
	define( 'CUSTOM_METADATA_MANAGER_DEBUG', false );
}

if ( CUSTOM_METADATA_MANAGER_DEBUG ) {
	require_once 'custom_metadata_examples.php';
}

require_once __DIR__ . '/includes/class-custom-metadata-manager.php';

global $custom_metadata_manager; // for backwards-compatibility we keep the global around, but it shouldn't be used.
$custom_metadata_manager = custom_metadata_manager::instance();

/**
 * Registers a metadata field.
 *
 * @param string       $slug Unique slug for the field.
 * @param array|string $object_types Object type(s) the field applies to.
 * @param array        $args Field arguments.
 * @return void
 */
function x_add_metadata_field( $slug, $object_types = 'post', $args = array() ) {
	custom_metadata_manager::instance()->add_metadata_field( $slug, $object_types, $args );
}

/**
 * Registers a multifield.
 *
 * @param string       $slug Unique slug for the multifield.
 * @param array|string $object_types Object type(s) the multifield applies to.
 * @param array        $args Multifield arguments.
 * @return void
 */
function x_add_metadata_multifield( $slug, $object_types = 'post', $args = array() ) {
	custom_metadata_manager::instance()->add_multifield( $slug, $object_types, $args );
}

/**
 * Registers a metadata group.
 *
 * @param string       $slug Unique slug for the group.
 * @param array|string $object_types Object type(s) the group applies to.
 * @param array        $args Group arguments.
 * @return void
 */
function x_add_metadata_group( $slug, $object_types, $args = array() ) {
	custom_metadata_manager::instance()->add_metadata_group( $slug, $object_types, $args );
}
