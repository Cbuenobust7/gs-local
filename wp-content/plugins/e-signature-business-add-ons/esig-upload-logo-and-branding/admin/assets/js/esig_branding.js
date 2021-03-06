(function ($) {

    // this is common js file . 
var custom_uploader;
    /* user clicks button on custom field, runs below code that opens new window */
    $('#esig_logo_upload').click(function () {
    	
    	//e.preventDefault();

		//If the uploader object has already been created, reopen the dialog
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}
		//Extend the wp.media object
		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});

		//When a file is selected, grab the URL and set it as the text field's value
		custom_uploader.on('select', function() {
			attachment = custom_uploader.state().get('selection').first().toJSON();
			$('#esig_branding_header_image').val(attachment.url);
                        $('#esign-success-view').show();
                         $('#esig-cover-page-section').show();
		});

		//Open the uploader dialog
		custom_uploader.open();
        return false;
    });
    // window.send_to_editor(html) is how WP would normally handle the received data. It will deliver image data in HTML format, so you can put them wherever you want.


    window.send_to_editor = function (html) {
    	
        var image_url = $('img', html).attr('src');
      
        if(!image_url)
        {
			var image_url = $('#embed-url-field').val();	
		}
        
       
        $('#esig_branding_header_image').val(image_url);
        $('#esign-success-view').show();
        $('#esig-cover-page-section').show();
        tb_remove(); // calls the tb_remove() of the Thickbox plugin
        //$j('#submit_button').trigger('click');
    }


    // disabled checked 
    $('input[name="esig_brandhing_disable"]').on('change', function () {
        if ($('input[name="esig_brandhing_disable"]').is(":checked")) {

            $("#esig_branding_footer_text_headline").attr('readonly', 'readonly');
            $("#esig_branding_footer_text").attr('readonly', 'readonly');

        } else {
            $("#esig_branding_footer_text_headline").removeAttr('readonly');
            $("#esig_branding_footer_text").removeAttr('readonly');
        }
    });
    
    // enable Display header image on document signing page
    $('input[name="esig_document_head_img"]').on('change', function () {
        if ($('input[name="esig_document_head_img"]').is(":checked")) {

            var header_img = $('#esig_branding_header_image').val();
            if(header_img == ''){
                $('input[name="esig_document_head_img"]').attr('checked',false);
                $('#esig_branding_header_image').css("border","2px red solid");
            }
            else {
                $('#esig_branding_header_image').css("border","0px red solid");
            }
           
        } 
    });
    
    // header display image input change 
    $('#esig_branding_header_image').on('change', function () {
        if ($('input[name="esig_document_head_img"]').is(":checked")) {

            var header_img = $('#esig_branding_header_image').val();
            if(header_img == ''){
                $('input[name="esig_document_head_img"]').attr('checked',false);
               // $('#esig_branding_header_image').css("border","2px red solid");
            }
        } 
    });

})(jQuery);
