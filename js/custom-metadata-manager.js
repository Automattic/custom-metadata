(function($){
	$(document).ready(function($) {

		// duplicating fields
		$( '.custom-metadata-field' ).on( 'click.custom_metada', '.add-multiple', function(e){
			e.preventDefault();
			var $this = $( this ),
				$last = $this.parent().prev( '.cloneable' ),
				$clone = $last.clone(),
				id_name = $clone.attr('id'),
				split_id = id_name.split( '-' ),
				instance_num = parseInt( split_id[1] ) + 1;

			id_name = split_id[0] + '-' + instance_num;
			$clone.attr( 'id', id_name );
			$clone.insertAfter( $last ).hide().fadeIn().find( ':input' ).val(''); // todo: figure out if default value
		});

		// deleting fields
		$( '.custom-metadata-field' ).on( 'click.custom_metada', '.del-multiple', function(e){
			e.preventDefault();
			var $this = $( this );
			$this.parent().fadeOut('normal', function(){
				$(this).remove();
			});
		});

		// init upload fields
		var custom_metadata_file_frame;
		$( '.custom-metadata-field' ).on( 'click.custom_metadata', '.custom-metadata-upload-button', function(e) {
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


	 	// init the datepicker fields
		$( '.custom-metadata-field' ).find( '.datepicker input' ).datepicker({
			changeMonth: true,
			changeYear: true
		});

		// select2
		$( '.custom-metadata-field' ).find( '.custom-metadata-select2' ).each(function(index) {
			$(this).select2();
		});

	});
})(jQuery);