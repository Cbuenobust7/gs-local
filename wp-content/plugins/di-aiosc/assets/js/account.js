var loader = jQuery('.aiosc-loading-holder');
var response = jQuery("#ajax-response");
var screen = jQuery('.aiosc-tab-content');
var main_editor = false;
var aiosc_tinyMCEPreInit = '';
jQuery(document).ready(function($) {
    if(aiosc_tinymce_enabled()) {
        if(aiosc_tinyMCEPreInit == '') {
            aiosc_tinyMCEPreInit = JSON.stringify(tinyMCEPreInit);
        }
    }
    loader = $('.aiosc-loading-holder');
    response = $("#ajax-response");
    screen = $('.aiosc-tab-content');

    $("#aiosc-form").ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            $("#aiosc-form-submit").removeAttr('disabled');
            aiosc_log(data);
            var res = $.parseJSON(data);
            response.hide();
            response.removeClass('error').removeClass('updated');
            if(res.result == 0) {
                response.addClass("error");
                response.html("<p>"+res.message+"</p>");
                response.fadeIn();
            }
            else {
                response.addClass("updated");
                var section = $('input[name="section"]');
                response.html("<p>"+res.message+"</p>");
                response.fadeIn();
            }
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
    $(document).on('click','.premade-response-actions #doaction',function(e) {
        e.preventDefault();
        var a = $(this).parent().find('select');
        if(a.val() != '') {
            var ids = [];
            $('#the-list').find('tr').each(function() {
                var chk = $(this).find('.check-column input[type="checkbox"]');
                if(chk.is(":checked")) {
                    ids.push(chk.val());
                }
            });
            update_responses(a.val(),ids);
        }
    });

    click_first_tab();
});
function switch_screen(screen_id, additional, li) {

    if(typeof additional != "object")
        additional = {};

    var param_data = jQuery.extend({
        action: 'aiosc_account_screen',
        screen: screen_id
    },additional);
    aiosc_hack_tinymce();
    response.hide();
    response.removeClass('error').removeClass('updated');
    loader.fadeIn(10);
    jQuery.post(AIOSC_AJAX_URL, param_data, function(data) {
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
    });
}
function aiosc_hack_tinymce() {
    if(aiosc_tinymce_enabled()) {
        //remove instance of aiosc-content tinymce editor
        if(tinymce.get('aiosc-content')) {
            tinymce.get('aiosc-content').remove();
        }
    }
}
function click_first_tab() {
    jQuery('.aiosc-tabs > li:eq(0)').trigger('click');
}
function click_first_subtab() {
    jQuery('.aiosc-subtabs > li:eq(0)').trigger('click');
}
/**
 * Mass-updates departments from list depending on action
 * @param action - can be DELETE | ACTIVATE | DEACTIVATE
 * @param ids - an array of department ids
 */
function update_responses(action,ids) {
    if(ids.length) {
        if(action == 'delete' || action == 'public' || action == 'private') {
            response.hide();
            response.removeClass('error').removeClass('updated');
            loader.fadeIn(10);
            jQuery.post(AIOSC_AJAX_URL, { action: 'aiosc_account_save', section: 'premade-responses-update', action2: action, responses: ids}, function(data) {
                aiosc_log(data);
                var res = jQuery.parseJSON(data);
                if(res.result > 0) {
                    click_first_subtab();
                    response.addClass("updated");
                }
                else {
                    response.addClass("error");
                }
                response.html('<p>'+res.message+'</p>');
                response.fadeIn();
                loader.fadeOut(100);
            })
        }
    }
}