'use strict';

var loader = jQuery('.aiosc-loading-holder');
var response = jQuery("#ajax-response");
var screen = jQuery('.aiosc-tab-content');

jQuery(document).ready(function($) {

    loader = $('.aiosc-loading-holder');
    response = $("#ajax-response");
    screen = $('.aiosc-tab-content');

    $("#aiosc-form").ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            $("#aiosc-form-submit").removeAttr('disabled');
            aiosc_log(data);
            $("#remove_old_sc_roles").prop('checked',false);
            var res = $.parseJSON(data);
            response.hide();
            response.removeClass('error').removeClass('updated');
            if(res.result == 0) {
                response.addClass("error");
            }
            else {
                response.addClass("updated");
            }
            response.html("<p>"+res.message+"</p>");
            response.fadeIn();
            $("html, body").animate({ scrollTop: 0 }, "slow");
        },
        beforeSubmit: function() {
            $("#aiosc-form-submit").attr('disabled','disabled');
        }
    });
    $(document).on('click','.aiosc-tabs > li',function(e) {
        var me = $(this);
        var screen_id = me.attr('data-screen');
        if(screen_id) {
            switch_screen(screen_id,'', me);
        }
    });

    $(document).on('click','.aiosc-subtabs > li',function(e) {
        var me = $(this);
        var screen_id = me.attr('data-screen');
        if(screen_id) {
            switch_screen(screen_id,'', me);
        }
    });

    $('.aiosc-tabs > li:eq(0)').trigger('click');
});
function switch_screen(screen_id, additional, li) {
    if(typeof additional != "object")
        additional = {};

    var param_data = jQuery.extend({
        action: 'aiosc_tools_screen',
        screen: screen_id
    }, additional);

    response.hide();
    response.removeClass('error').removeClass('updated');
    loader.fadeIn(10);

    jQuery.post(AIOSC_AJAX_URL, param_data, function(data) {
        try {
            aiosc_log(data);
            var res = jQuery.parseJSON(data);
            if(res.result == 1) {
                if(li) {
                    li.parent().find('.active').removeClass('active');
                    li.addClass('active');
                }
                jQuery('.aiosc-tab-content').html(res.data.html);
                loader.fadeOut(300);
            }
            else {
                response.addClass("error");
                response.html("<p>"+res.message+"</p>").show();
            }
            loader.fadeOut(100);
        }
        catch(ex) {
            console.log(ex);
        }
    });
}
function click_first_tab() {
    jQuery('.aiosc-tabs > li:eq(0)').trigger('click');
}
function click_first_subtab() {
    jQuery('.aiosc-subtabs > li:eq(0)').trigger('click');
}