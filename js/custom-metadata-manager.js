jQuery(document).ready(function($) {

	// duplicating fields
	if ( $('.add-multiple').length ) {
		$('.add-multiple').live('click', function(e) {
			e.preventDefault();
			var parent = $(this).parent().prev('.cloneable').attr('id');
			var $last = $('#'+parent);
			var $clone = $last.clone();
			var idName = $clone.attr('id');
			var instanceNum = parseInt(idName.split('-')[1])+1;
			idName = idName.split('-')[0]+'-'+instanceNum;
			$clone.attr('id',idName);
			$clone.insertAfter($last).hide().fadeIn().find(':input[type=text]').val('');
		});
	}

	// deleting fields
	if ( $('.del-multiple').length )	 {
		$('.del-multiple').live('click', function(e) {
			e.preventDefault();
			$(this).parent().fadeOut('normal', function(){
				$(this).remove();
			});
		});
	}

	// init upload fields
	var custom_metadata_file_frame;
	$('.custom-metadata-field').on( 'click.custom_metadata', '.custom-metadata-upload-button', function(e) {
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
	if ( $('.datepicker').length ) {
		$( '.datepicker input' ).datepicker({changeMonth: true, changeYear: true});
	}

	// chosen
	$("select.chosen").each(function(index) {
		$(this).chosen();
	});

});