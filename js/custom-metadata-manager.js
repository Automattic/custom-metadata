var formfield;
jQuery(document).ready(function($) {

	// duplicating fields
	if ( $('.add-multiple').length ) {
		$('.add-multiple').live('click', function(e) {
			e.preventDefault();
			var $this = $(this);
			var $container = $this.closest('.custom-metadata-field');
			$clone = $( $container.find('script.clonetemplate').html() );

			// Create a unique ID for other JS to hook to, use current time (in milliseconds) and random number to create unique
			var idName = $clone.attr('id');
			idName = idName.split('-')[0]+'-'+((new Date()).getTime())+Math.floor(Math.random()*1000);
			$clone.attr('id',idName);

			// insert
			$clone.appendTo( $container.find('.x-sortable') ).hide().fadeIn().find(':input').not('input[type="button"], input[type="submit"], input[type="reset"]').val('');
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

	// sorting multiple fields
	$('.x-sortable').sortable({
		handle: '.drag-handle',
	});



	// init the upload fields
	if ( $('.upload_button').length ) {
		$('.upload_button').live('click', function(e) {
			formfield = $(this).parent().attr('id');
			window.send_to_editor=window.send_to_editor_clone;
			tb_show('','media-upload.php?post_id='+numb+'&TB_iframe=true');
			return false;
		});
		window.original_send_to_editor = window.send_to_editor;
		window.send_to_editor_clone = function(html){
				file_url = jQuery('img',html).attr('src');
				if (!file_url) { file_url = jQuery(html).attr('href'); }
				tb_remove();
				jQuery('#'+formfield+' .upload_field').val(file_url);
			}
 	}

 	// init the datepicker fields
	if ( $('.datepicker').length ) {
		$( '.datepicker input' ).datepicker({changeMonth: true, changeYear: true});
	}

	// chosen
	$("select.chosen").each(function(index) { 
		$(this).chosen();
	});

});