var aioscupload = null;
jQuery(document).ready(function($) {
    if(aiosc_tinymce_enabled()) {
        $("input[type='submit'], button[type='submit']").on("mousedown",function() {
            tinyMCE.triggerSave(); //must save before submitting in order to pass data to request
        });
    }
    $("#aiosc-form").ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            $("#aiosc-form-submit").removeAttr('disabled');
            aioscupload.upload_end();
            aiosc_log(data);
            var res = $.parseJSON(data);
            var response = $(".aiosc-form-response");
            response.removeClass("updated").removeClass("error");
            if(res.result != 1) {
                response.addClass("error");
            }
            else {
                aioscupload.reset();
                response.addClass("updated");
                if(res.data.url != "undefined") {
                    window.location.href = res.data.url;
                }
                try {
                    if(aiosc_tinymce_enabled())
                        tinymce.get('aiosc-content').setContent('');
                    else
                        $("textarea#aiosc-content").val('');
                }
                catch (e) {
                    $("textarea#aiosc-content").val('');
                }
            }

            response.html("<p>"+res.message+"</p>");
            response.fadeIn();
            $("html, body").animate({ scrollTop: response.position().top - 50 }, "slow");
        },
        beforeSubmit: function() {
            aioscupload.upload_start();
            $("#aiosc-form-submit").attr('disabled','disabled');
        }
    })
});