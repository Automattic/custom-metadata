(function($){
	$(document).ready(function($) {

		var $custom_metadata_field = $( '.custom-metadata-field' );

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
			$clone.children().not('span,a').each( function() {
				var old_name = $(this).attr( 'name' );
				if ( old_name.indexOf( '['+(instance_num-1)+']' ) != -1 ) {
					var new_name = old_name.replace( '['+(instance_num-1)+']', '['+instance_num+']' );
					$(this).attr( 'name', new_name );
				}
  			} );
			$clone.insertAfter( $last ).hide().fadeIn().find( ':input' ).val(''); // todo: figure out if default value
		});

		// deleting fields
		$custom_metadata_field.on( 'click.custom_metadata', '.del-multiple', function(e){
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

			// if the media frame already exists, reopen it.
			if ( custom_metadata_file_frame ) {
				custom_metadata_file_frame.open();
				return;
			}

			custom_metadata_file_frame = wp.media.frames.file_frame = wp.media({
				title: $this.data( 'uploader-title' ),
				button: {
					text: $this.data( 'uploader-button-text' )
				},
				multiple: false
			});

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
			$(this).select2();
		});
		
		// sorting multiple fields
		$('.x-sortable').sortable({
			handle: '.drag-handle',
		});
		
		$('.x-sortable').bind( 'sortupdate', function() {
			var rowCount = $(this).children('div.cloneable').length;
			var fieldName = $(this).children('label').attr('for');
			$(this).children('div.cloneable').each(function(index) {
				var thisIndex = index+1;
				if(index===0) {
					$(this).attr('id',fieldName+'-'+thisIndex);
					$(this).children('a.del-multiple').remove();
				} else {
					$(this).attr('id',fieldName+'-'+thisIndex);
					if ( !$(this).children('a.del-multiple').length ) {
						$(this).append('<a href="#" class="del-multiple hide-if-no-js">Delete</a>');
					}
				}
			});
	});


	});
})(jQuery);