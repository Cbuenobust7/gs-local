function aiosc_log(str) {
    if(typeof AIOSC_JS_DEBUG != "undefined" && AIOSC_JS_DEBUG == true)
        console.log(str);
}
function aiosc_tinymce_enabled() {
    return typeof tinymce != "undefined";
}
jQuery(document).ready(function($) {
    $(document).on('click', '.button-list-clear', function(e) {
        e.preventDefault();
        var lst = $(this).attr('data-list');
        if($('select#'+lst).length) {
            $('select#'+lst).val('');
        }
    })
    $(document).on('click','#cb-select-all-1, #cb-select-all-2',function() {
        var list = $(this).parents('table').find('tbody');
        if($(this).is(":checked")) {
            list.find('.check-column input[type="checkbox"]').prop('checked',true);
            $("#cb-select-all-1, #cb-select-all-2").prop('checked',true);
        }
        else {
            list.find('.check-column input[type="checkbox"]').prop('checked',false);
            $("#cb-select-all-1, #cb-select-all-2").prop('checked',false);
        }
    });
    if(typeof wpColorPicker != "undefined")
        $(".aiosc-color-input").wpColorPicker();
    if(aiosc_tinymce_enabled()) {
        $("input[type='submit'], button[type='submit']").on("mousedown",function() {
            tinyMCE.triggerSave(); //must save before submitting in order to pass data to request
        });
    }
    //popups
    $(document).on('click','.aiosc-popup .aiosc-discard-button, .aiosc-popup .aiosc-popup-x',function(e) {
        e.preventDefault();
        aiosc_popup_close($(this).parents('.aiosc-popup'));
    });

});
function aiosc_popup_open(popup, onOpen) {
    jQuery('.aiosc-popup-bg').show();
    jQuery(popup).fadeIn({
        duration: 100,
        complete: function() {
            if(typeof onOpen == "function")
                onOpen.call();
        }
    });
}
function aiosc_popup_close(popup, onClose) {
    jQuery('.aiosc-popup-bg').fadeOut(100);
    jQuery(popup).fadeOut({
        duration: 100,
        complete: function() {
            if(typeof onClose == "function")
                onClose.call();
        }
    });
}
function aiosc_init_colorpicker() {
    var i = jQuery('.aiosc-color-input');
    var color = (i.val() != '')? i.val() : false;
    i.wpColorPicker({
        default: color
    })
}