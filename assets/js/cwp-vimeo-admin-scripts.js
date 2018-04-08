jQuery(function($){
	var selected;

	$(document).on('change', '#integration-select-type', function(){
        if($(this).val() == 'vimeo') {
        	var app_token = $(this).find(':selected').attr('data-key');
        	var select_album = $('#integration-select-folder');
            select_album.html('<option selected="true" disabled="disabled">Select Album</option>');
            select_album.attr('disabled', 'disabled');

        	$.post(
                cwpv_admin_script.ajaxurl,
                { 
                action : 'vimeo_list_albums'
                }, 
                function( result, textStatus, xhr ) {
                	var result = JSON.parse(result); 
                	var albums = result['body']['data'];

                	$.each(albums, function(){
                		var link = this['link'];
                		var album_id = link.split('/album/');
                        var name = this['name'];
                        var option = '<option class="root-folder" value="'+album_id[1]+'">'+name+'</option>';
                        select_album.append(option);
                	});
                    
                }).fail(function(error) {
                    console.log(error);
                }).done(function() {
                	$('#integration-select-folder').find('option').each(function(){
						if($(this).text() === '') {
							$(this).remove();
						}
					});

					if(selected) {
						$('#integration-select-folder option[value="'+selected+'"]').attr('selected','selected');
					}

                    select_album.removeAttr('disabled');
                }
            );
        }
    });

    $(document).ready(function() {
        if($('#integration-select-type').val() == 'vimeo') {
    		selected = $('#integration-select-folder').val();
    		$('#integration-select-type').trigger('change');
        }
	});
});