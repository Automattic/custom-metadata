var formfield;
jQuery(document).ready(function($) {

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
	
	if ( $('.del-multiple').length )	 {
		$('.del-multiple').live('click', function(e) {
			e.preventDefault();
			$(this).parent().fadeOut('normal', function(){
				$(this).remove();
			});
		});
	}

	if ( $('.customEditor').length ) {
		$('.customEditor textarea').each(function(e) {
			var id = $(this).attr('id');
				if (!id) {
				id = 'customEditor-' + i++;
				$(this).attr('id',id);
			}					
			if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
				tinyMCE.settings.theme_advanced_buttons1 += ",|,add_image,add_video,add_audio,add_media";	
				tinyMCE.execCommand("mceAddControl", false, id);
			}
		});	
	} 
	
	if ( $('.editorbuttons > a').length )	{
		$('.editorbuttons > a').click(function(){
			$(this).siblings().removeClass('active');
			$(this).addClass('active');
		});
	}

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
				if (!file_url) { imgurl = jQuery(html).attr('href'); }
				tb_remove();
				jQuery('#'+formfield+' .upload_field').val(file_url);						
			}		
 	}

	if ( $('.datepicker').length ) {
		$( '.datepicker input' ).datepicker();
	}
	
});	