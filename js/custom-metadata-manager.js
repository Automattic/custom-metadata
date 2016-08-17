(function($){
	$(document).ready(function($) {

		var $custom_metadata_field = $( '.custom-metadata-field' ),
			$custom_metadata_multifield = $( '.custom-metadata-multifield' );

		var incrementor = function(v) {
			return v.replace( /[0-9]+(?!.*[0-9])/ , function( match ) {
				return parseInt(match, 10) + 1;
			});
		};

		// duplicating fields
		$custom_metadata_field.on( 'click.custom_metadata', '.add-multiple', function(e){
			e.preventDefault();
			var $this = $( this ),
				$last = $this.parent().prev( '.cloneable' ),
				$clone = $last.clone(),
				id_name = $clone.attr('id'),
				split_id = id_name.split( '-' ),
				instance_num = parseInt( split_id[1] ) + 1;

			id_name = split_id[0] + '-' + instance_num;
			$clone.attr( 'id', id_name );
			$clone.insertAfter( $last ).hide().fadeIn().find( ':input' ).not( 'type="button"' ).val(''); // todo: figure out if default value
		});

		// deleting fields
		$custom_metadata_field.on( 'click.custom_metadata', '.del-multiple', function(e){
			e.preventDefault();
			var $this = $( this );
			$this.parent().fadeOut('normal', function(){
				$(this).remove();
			});
		});

		// cloning multifields
		$custom_metadata_multifield.on( 'click.custom_metadata', '.custom-metadata-multifield-clone', function(e){
			e.preventDefault();
			var $this = $( this ),
				$parent = $this.parent().parent(),
				$slug = $parent.attr( 'data-slug' ),
				$last = $this.parent(),
				$clone = $last.clone();

			$clone.find( ':input:not(:button)' ).val('');
			$clone.insertAfter( $last ).hide();

			$groupings = $parent.find('.custom-metadata-multifield-grouping');

			$.each( $groupings, function( i, grouping ){

				var $grouping = $( grouping ),
					$fields = $grouping.find('.custom-metadata-field'),
					num = i + 1;

				$grouping.attr( 'id', $slug + '-' + num );

				$.each( $fields, function( j, field ){
					var $field = $( field ),
						$field_slug = $field.attr( 'data-slug' ),
						$label = $field.find( 'label' ),
						$div = $field.find( 'div' ),
						$field_inputs = $field.find( ':input:not(:button)' ),
						$field_id = $field_slug + '_' + num;
					$label.attr( 'for', $field_id );
					$div.attr( 'id', $field_id + '_1' ).attr( 'class', $field_id );
					$.each( $field_inputs, function( k, field_input ){

						var $field_input = $( field_input );
						if ( ! _.isEmpty( $field_input.attr( 'id' ) ) ) {
							$field_input.attr( 'id', $field_id );
						}

						if ( ! _.isEmpty( $field_input.attr( 'name' ) ) ) {
							$field_input.attr( 'name', $slug + '[' + i + '][' + $field_slug + ']');
						}

						if($field_input.is('.textarea_wysiwyg')) {
							var $textarea = $field_input;
							var $textarea_parent = $field_input.parent();
							$textarea_parent.html('');
							$textarea.appendTo($textarea_parent).show();
						}
					});
				});

			});

			startTinyMCE('.custom-metadata-field .textarea_wysiwyg');
			$clone.fadeIn();
		});

		// deleting multifields
		$custom_metadata_multifield.on( 'click.custom_metadata', '.custom-metadata-multifield-delete', function(e){
			e.preventDefault();
			var $this = $( this );
			$this.parent().fadeOut('normal', function(){
				$(this).remove();
			});
		});

		// init upload fields
		var custom_metadata_file_frame;
		$custom_metadata_field.on( 'click.custom_metadata', '.custom-metadata-upload-button', function(e) {
			e.preventDefault();

			var $this = $(this),
				$this_field = $this.parent();

			// if the media frame doesn't exist yet, create it
			if ( ! custom_metadata_file_frame ) {
				custom_metadata_file_frame = wp.media.frames.file_frame = wp.media({
					title: $this.data( 'uploader-title' ),
					button: {
						text: $this.data( 'uploader-button-text' )
					},
					multiple: false
				});
			}

			// unbind prior events first
			custom_metadata_file_frame.off( 'select' );

			custom_metadata_file_frame.on( 'select', function() {
				attachment = custom_metadata_file_frame.state().get( 'selection' ).first().toJSON();
				$this_field.find( '.custom-metadata-upload-url' ).val( attachment.url );
				$this_field.find( '.custom-metadata-upload-id' ).val( attachment.id );
			});

			custom_metadata_file_frame.open();
		});

		$custom_metadata_field.on( 'click.custom_metadata', '.custom-metadata-clear-button', function(e){
			e.preventDefault();
			var $this = $(this),
			$this_field = $this.parent();
			$this_field.find( 'input:not( [type=button] )' ).val( '' );
		});

		// init link fields
		var custom_metadata_link_selector_is_open = false;
		var custom_metadata_link_selector_target = null;
		$custom_metadata_field.on( 'click.custom_metadata', '.custom-metadata-link-button', function(e){
			e.preventDefault();
			custom_metadata_link_selector_is_open = true;
			custom_metadata_link_selector_target = $(this).parent().find( 'input[type="text"]' );
			wpActiveEditor = true;
			wpLink.open();
			var $wp_link = $( '#wp-link' );
			wpLink.textarea = custom_metadata_link_selector_target;
			$wp_link.find( '.link-target' ).remove(); // remove the "new tab" checkbox
			$wp_link.find( '#link-title-field' ).parents( '#link-options div' ).remove(); // remove the "title" field
		});

		$(document).on( 'click.custom_metadata', '#wp-link-submit', function(e){
			e.preventDefault();
			if ( null === custom_metadata_link_selector_target)
				return;

			var linkAtts = wpLink.getAttrs();
			custom_metadata_link_selector_target.val(linkAtts.href);
			wpLink.textarea = custom_metadata_link_selector_target;
			wpLink.close();
			custom_metadata_link_selector_target = null;
		});

		$(document).on( 'click.custom_metadata', '#wp-link-cancel, .ui-dialog-titlebar-close', function(e){
			e.preventDefault();
			if ( null === custom_metadata_link_selector_target)
				return;

			wpLink.textarea = custom_metadata_link_selector_target;
			wpLink.close();
			custom_metadata_link_selector_target = null;
			custom_metadata_link_selector_is_open = false;
		});

	 	// init the datepicker fields
		$( '.custom-metadata-field.datepicker' ).find( 'input' ).datepicker({
			changeMonth: true,
			changeYear: true
		});

		// init the datetimepicker fields
		$( '.custom-metadata-field.datetimepicker' ).find( 'input' ).datetimepicker({
			changeMonth: true,
			changeYear: true
		});

		// init the timepicker fields
		$( '.custom-metadata-field.timepicker' ).find( 'input' ).timepicker({
			changeMonth: true,
			changeYear: true
		});

		// select2
		$custom_metadata_field.find( '.custom-metadata-select2' ).each(function(index) {
			$(this).select2({ placeholder : $(this).attr('data-placeholder'), allowClear : true });
		});

		function startTinyMCE(id) {
			selector = id;
			if(typeof(id) == 'undefined') {
				selector = 'textarea';
			}
			tinyMCE.init({
				selector: selector,
				toolbar1: "bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv",
				forced_root_block: false,
				remove_linebreaks: false,
				gecko_spellcheck: false,
				keep_styles: true,
				accessibility_focus: true,
				tabfocus_elements: 'major-publishing-actions',
				media_strict: false,
				wpeditimage_disable_captions: true,
				wpautop: true,
				apply_source_formatting: false,
				block_formats: "Paragraph=p, Heading 3=h3, Heading 4=h4",
				menubar: false,
				setup: function (editor) {
	        editor.on('change', function () {
		        editor.save();
		      });
		    }
			});
		}

		jQuery(document).ready(function(){
			startTinyMCE('.custom-metadata-field .textarea_wysiwyg');
		});

	});
})(jQuery);
