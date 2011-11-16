<?php
/*
Copyright 2010 Mohammad Jangda

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

add_action( 'init', 'init_my_custom_post_types' );

function init_my_custom_post_types() {

	register_post_type( 'custom-metatadata-milestone', array(
		'labels' => array(
			'name' => 'Milestones'
			, 'singular_name' => 'Milestone'
			, 'add_new' => 'Add New'
			, 'add_new_item' => 'Add New Milestone'
			, 'edit_item' => 'Edit Milestone'
			, 'new_item' => 'Add Milestone'
			, 'view_item' => 'View Milestone'
			, 'search_items' => 'Search Milestones'
			, 'not_found' => 'No Milestones found'
			, 'not_found_in_trash' => 'No Milestones found in Trash'
		),
		'public'  => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => true,
		'query_var' => false,
		'taxonomies' => array( 'milestone-category' ),
		'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ),
	) );
}

add_action( 'admin_init', 'init_my_custom_fields' );

function init_my_custom_fields() {

	if( function_exists( 'x_add_metadata_group' ) && function_exists( 'x_add_metadata_field' ) ) {

		x_add_metadata_group( 'x_metaBox1', 'post', $args = array(
			'label' => 'Group with Multiple Fields'
			, 'include' => 2476
		) );

		x_add_metadata_group( 'x_metaBox2', array( 'post', 'user' ), $args = array(
			'label' => 'Group for Post and User'
			, 'exclude' => array( 'user' => array( 67, 35 ) )
		) );

		x_add_metadata_field('x_fieldName1', 'post', array(
			'group' => 'x_metaBox1'
			, 'description' => 'This is field #1. It\'s a simple text field.'
			, 'label' => 'Field #1'
			, 'display_column' => true
		));

		x_add_metadata_field('x_fieldName2', 'post', array(
			'group' => 'x_metaBox1'
			, 'display_column' => 'My Column (with Custom Callback)'
			, 'display_column_callback' => 'fieldName2_columnCallback'
		));

		function fieldName2_columnCallback( $field_slug, $field, $object_type, $object_id, $value ) {
			echo sprintf( 'The value of field "%s" is %s. <br /><a href="http://icanhascheezburger.files.wordpress.com/2010/10/04dc84b6-3dde-45db-88ef-f7c242731ce3.jpg">Here\'s a LOLCat</a>', $field_slug, $value ? $value : 'not set' );
		}

		x_add_metadata_field('x_fieldTextarea1', 'post', array(
			'group' => 'x_metaBox1'
			, 'field_type' => 'textarea'
		));

		x_add_metadata_field('x_fieldCheckbox1', 'post', array(
			'group' => 'x_metaBox1'
			, 'field_type' => 'checkbox'
		));

		x_add_metadata_field('x_fieldRadio1', 'post', array(
			'group' => 'x_metaBox1'
			, 'field_type' => 'radio'
			, 'values' => array(
				'option1' => 'Option #1'
				, 'option2' => 'Option #2'
			)
		));

		x_add_metadata_field('x_fieldSelect1', 'post', array(
			'group' => 'x_metaBox1'
			, 'field_type' => 'select'
			, 'values' => array(
				'option1' => 'Option #1'
				, 'option2' => 'Option #2'
			)
		));

		x_add_metadata_field('x_fieldName2', array( 'post', 'user' ), array(
			'group' => 'x_metaBox2'
		));

		x_add_metadata_field('x_fieldName3', 'post', array(

		));

		x_add_metadata_field('x_fieldCustomHidden1', 'post', array(
			'group' => 'x_metaBox1'
			, 'display_callback' => 'fieldCustomHidden1_display'
		));

		function fieldCustomHidden1_display( $field_slug, $field, $value ) {
			if( ! $value ) $value = 'This is a secret hidden value! Don\'t tell anyone!';
			?>
			<hr />
			<p>This is a hidden field rendered with a custom callback. The value is "<?php echo $value; ?>".</p>
			<input type="hidden" name="<?php echo $field_slug; ?>" value="<?php echo $value; ?>" />
			<hr />
			<?php
		}

		x_add_metadata_field('x_fieldCustomList1', array( 'post', 'user' ), array(
			'label' => 'Post Action Items'
			, 'display_callback' => 'fieldCustomList1_display'

		));

		function fieldCustomList1_display( $field_slug, $field, $object_type, $object_id, $value ) {
			$value = (array)$value;
			$field_class = sprintf( 'field-%s', $field_slug );
			$count = 0;
			?>
			<p>This is an example field rendered with a custom display_callback. All done with about 40 lines of code!</p>

			<?php if( empty( $value ) ) $value = array(); ?>
				<?php foreach( $value as $v ) : ?>
					<div class="f1_my-list-item">
						<input type="text" name="<?php echo $field_slug; ?>[]" value="<?php echo esc_attr( $v ); ?>" />
						<?php if( $count > 0 ) : ?>
							<a href="#" class="f1_btn-del-list-item hide-if-no-js" style="color:red;">Delete</a>
						<?php endif; ?>
					</div>
					<?php $count++; ?>
				<?php endforeach; ?>
			<p><a href="#" class="f1_btn-add-list-item hide-if-no-js">+ Add New</a></p>

			<script>
			;(function($) {
				$('.f1_btn-add-list-item').click(function(e) {
					e.preventDefault();
					var $last = $('.f1_my-list-item:last');
					var $clone = $last.clone();

					$clone
						.insertAfter($last)
						.find(':input')
							.val('')
						;
				});
				$('.f1_btn-del-list-item').live('click', function(e) {
					e.preventDefault();
					$(this).parent().remove();
				});
			})(jQuery);
			</script>
			<?php
		}

		x_add_metadata_field('x_fieldCustomList2', array( 'post' ), array(
			'label' => 'Post Action Items (With Links!)'
			, 'display_callback' => 'fieldCustomList2_display'
			, 'sanitize_callback' => 'fieldCustomList2_sanitize'
		));

		function fieldCustomList2_display( $field_slug, $field, $object_type, $object_id, $value ) {
			$value = (array) $value;
			$field_class = sprintf( 'field-%s', $field_slug );
			$count = 0;
			?>
			<p>This is an example field rendered with a custom display_callback (renders multiple fields and js) and a custom sanitize_callback (aggregates the submitted data into a single array).</p>

			<?php if( empty( $value ) ) array_push( $value, array() ); ?>

			<?php foreach( $value as $v ) : ?>
				<?php
				$text = isset( $v['text'] ) ? $v['text'] : '';
				$url = isset( $v['url'] ) ? $v['url'] : '';
				?>
				<div class="f2_my-list-item">
					<label>Text</label>
					<input type="text" name="<?php echo $field_slug; ?>_text[]" value="<?php echo esc_attr( $text ); ?>" />

					<label>URL</label>
					<input type="text" name="<?php echo $field_slug; ?>_url[]" value="<?php echo esc_attr( $url ); ?>" />

					<?php if( $count > 0 ) : ?>
						<a href="#" class="f2_btn-del-list-item hide-if-no-js" style="color:red;">Delete</a>
					<?php endif; ?>
					<?php $count++; ?>
				</div>
			<?php endforeach; ?>

			<p><a href="#" class="f2_btn-add-list-item hide-if-no-js">+ Add New</a></p>

			<script>
			;(function($) {
				$('.f2_btn-add-list-item').click(function(e) {
					e.preventDefault();
					var $last = $('.f2_my-list-item:last');
					var $clone = $last.clone();

					$clone
						.insertAfter($last)
						.find(':input')
							.val('')
						;
				});
				$('.f2_btn-del-list-item').live('click', function(e) {
					e.preventDefault();
					$(this).parent().remove();
				});
			})(jQuery);
			</script>
			<?php
		}

		function fieldCustomList2_sanitize( $field_slug, $field, $object_type, $object_id, $value ) {
			$values = array();
			$text_key = $field_slug . '_text';
			$url_key = $field_slug . '_url';

			if( isset( $_POST[$text_key] ) ) {
				$count = 0;
				foreach( (array) $_POST[$text_key] as $text ) {
					$url = isset( $_POST[$url_key][$count] ) ? $_POST[$url_key][$count] : '';
					if( $text || $url ) {
						array_push( $values, array(
							'text' => $text
							, 'url' => $url
						) );
					}
					$count++;
				}
			}

			return $values;
		}

		x_add_metadata_field( 'x_search-engine', 'post', array(
			'field_type' => 'radio'
			, 'label' => 'Preferred Search Engine'
			, 'values' => array(
				'google' => 'Google!'
				, 'bing' => 'Bing!'
			)
			, 'display_column' => 'Blog Links'
			, 'display_column_callback' => 'blog_links_column_callback'
		));

		function blog_links_column_callback( $field_slug, $field, $object_type, $object_id, $value ) {
			switch( $value ) {
				case 'google':
					$url = 'http://google.com';
					break;
				case 'bing':
					$url = 'http://bing.com';
					break;
				default:
					$url = '';
					break;
			}
			if( $url )
				return sprintf( '<a href="%s" target="_blank">Go to search</a>', $url );
			return __( 'Search Engine not selected' );
		}

		x_add_metadata_field('x_pageField1', 'page', array(
			'display_column' => 'My Page Field'
		));

		x_add_metadata_field('x_userField1', 'user', array(
			'display_column' => true
			, 'description' => 'This is a field for a user. Enter information in here!'
		));

		x_add_metadata_field('x_userCheckboxField1', 'user', array(
			'label' => 'Checkbox says what?'
			, 'field_type' => 'checkbox'
		));

		x_add_metadata_field('x_userAndPostField1', array( 'post', 'user' ), array(

		));

		x_add_metadata_field('x_cap-limited-field', 'post', array(
			'label' => 'Cap Limited Field (edit_posts)'
			, 'required_cap' => 'edit_posts' // limit to users who can edit posts
		));

		x_add_metadata_field('x_user-cap-limited-field', 'user', array(
			'label' => 'Cap Limited Field (edit_user)'
			, 'required_cap' => 'edit_user' // limit to users who can edit other users
		));

		x_add_metadata_field('x_author-cap-limited-field', 'user', array(
			'label' => 'Cap Limited Field (author)'
			, 'required_cap' => 'author' // limit to authors
		));

		x_add_metadata_group( 'x_milestone-info', 'milestone', $args = array(
			'label' => 'Milestone Info'
		) );

		x_add_metadata_field('x_year', 'milestone', array(
			'group' => 'x_milestone-info'
			, 'description' => 'Enter the year for this milestone, e.g. 1991'
			, 'label' => 'Year'
			, 'display_column' => true
		));

		x_add_metadata_field('x_commentField1', 'comment', array(
			'label' => 'Field for Comment'
			, 'display_column' => true
		));

		x_add_metadata_field('x_fieldNameExcluded1', 'post', array(
			'description' => 'This field is excluded from Post ID#2476'
			, 'label' => 'Excluded Field'
			, 'exclude' => 2476
		));

		x_add_metadata_field('x_fieldNameIncluded1', 'post', array(
			'description' => 'This field is only included on Post ID#2476'
			, 'label' => 'Included Field'
			, 'include' => 2476
		));

	}
}