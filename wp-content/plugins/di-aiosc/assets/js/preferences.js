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
                if(section.val() == 'departments-new') {
                    section.val('departments-new-update');
                    section.parent().append('<input type="hidden" name="department_id" value="'+res.data.department_id+'" />');
                    section.parent().append('<input type="hidden" name="message" value="'+res.message+'" />');
                    $("#aiosc-form").find('input[type="submit"]').trigger("click");
                }
                else if(section.val() == 'departments-new-update' || section.val() == 'departments-edit') {
                    $('.aiosc-tab-content').html(res.data.html);
                    aiosc_init_colorpicker();
                }
                else if(section.val() == 'priorities-new') {
                    section.val('priorities-new-update');
                    section.parent().append('<input type="hidden" name="priority_id" value="'+res.data.priority_id+'" />');
                    section.parent().append('<input type="hidden" name="message" value="'+res.message+'" />');
                    $("#aiosc-form").find('input[type="submit"]').trigger("click");
                }
                else if(section.val() == 'priorities-new-update' || section.val() == 'priorities-edit') {
                    $('.aiosc-tab-content').html(res.data.html);
                    aiosc_init_colorpicker();
                }
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

    $('.aiosc-tabs > li:eq(0)').trigger('click');

    $(document).on('click','.department-actions #doaction',function(e) {
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
            update_departments(a.val(),ids);
        }
    })
    $(document).on('click','.priority-actions #doaction',function(e) {
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
            update_priorities(a.val(),ids);
        }
    })
});
function switch_screen(screen_id, additional, li) {
    if(typeof additional != "object")
        additional = {};

    var param_data = jQuery.extend({
        action: 'aiosc_pref_screen',
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
            aiosc_init_colorpicker();

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
        //remove instances of aiosc-content tinymce editor
        if(tinymce.get('aiosc-content')) {
            tinymce.get('aiosc-content').remove();
        }
        if(tinymce.get('aiosc-content-2')) {
            tinymce.get('aiosc-content-2').remove();
        }
        if(tinymce.get('aiosc-content-3')) {
            tinymce.get('aiosc-content-3').remove();
        }
        if(tinymce.get('aiosc-content-4')) {
            tinymce.get('aiosc-content-4').remove();
        }
        if(tinymce.get('aiosc-content-5')) {
            tinymce.get('aiosc-content-5').remove();
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
function update_departments(action,ids) {
    if(ids.length) {
        if(action == 'delete' || action == 'activate' || action == 'deactivate') {
            response.hide();
            response.removeClass('error').removeClass('updated');
            loader.fadeIn(10);
            jQuery.post(AIOSC_AJAX_URL, { action: 'aiosc_departments_update', action2: action, departments: ids}, function(data) {
                aiosc_log(data);
                var res = jQuery.parseJSON(data);
                if(res.result > 0) {
                    jQuery('.aiosc-tab-content').html(res.data.html);
                }
                else {
                    response.addClass("error");
                    response.html('<p>'+res.message+'</p>');
                    response.fadeIn();
                }
                loader.fadeOut(100);
            })
        }
    }
}
/**
 * Mass-updates priorities from list depending on action
 * @param action - can be DELETE | ACTIVATE | DEACTIVATE
 * @param ids - an array of priority ids
 */
function update_priorities(action,ids) {
    if(ids.length) {
        if(action == 'delete' || action == 'activate' || action == 'deactivate') {
            response.hide();
            response.removeClass('error').removeClass('updated');
            loader.fadeIn(10);
            jQuery.post(AIOSC_AJAX_URL, { action: 'aiosc_priorities_update', action2: action, priorities: ids}, function(data) {
                aiosc_log(data);
                var res = jQuery.parseJSON(data);
                if(res.result > 0) {
                    jQuery('.aiosc-tab-content').html(res.data.html);
                }
                else {
                    response.addClass("error");
                    response.html('<p>'+res.message+'</p>');
                    response.fadeIn();
                }
                loader.fadeOut(100);
            })
        }
    }
}