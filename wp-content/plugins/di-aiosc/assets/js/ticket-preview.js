
var aioscupload = null;
jQuery(document).ready(function($) {
    var loader = $('.aiosc-loading-holder');
    /** Responsive Toggler */
    $(document).on('click', '#aiosc-sidebar-toggler', function(e) {
        e.preventDefault();
        aiosc_toggle_sidebar($(this));
    });
    sidebar_init();
    $(window).resize(function() {
        sidebar_init();
    });
    $(document).on('click', '#aiosc-expand-reply-form', function(e) {
        e.preventDefault();
        $(this).parent().find('.aiosc-reply-form-content').fadeIn();
        $(this).remove();
    });
    //REPLY FORM
    $("#aiosc-form").ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            $("#aiosc-form-submit").removeAttr('disabled');
            aioscupload.upload_end();
            aiosc_log(data);
            var res = $.parseJSON(data);
            var response = $("#aiosc-reply-response");
            response.removeClass("updated").removeClass("error");
            if(res.result != 1) {
                response.addClass("error");
            }
            else {
                aioscupload.reset();
                try {
                    if(aiosc_tinymce_enabled())
                        tinymce.get('aiosc-content').setContent('');
                    else
                        $("textarea#aiosc-content").val('');
                }
                catch (e) {
                    $("textarea#aiosc-content").val('');
                }
                response.addClass("updated");
                $('.aiosc-no-replies-found').remove();
                $('.aiosc-window.aiosc-replies .page-title:eq(0)').after(res.data.html);
                $('.aiosc-window.aiosc-replies .aiosc-title:eq(0)').after(res.data.html);
            }
            loader.fadeOut(200);
            response.html("<p>"+res.message+"</p>");
            response.fadeIn();
            //$("html, body").animate({ scrollTop: 0 }, "slow");
        },
        beforeSubmit: function() {
            loader.fadeIn(100);
            aioscupload.upload_start();
            $("#aiosc-form-submit").attr('disabled','disabled');
        }
    });
    //REST
    $(document).on('click','.aiosc-ticket-more a',function(e) {
        e.preventDefault();
        if($(this).hasClass('more-shown')) {
            $(this).removeClass('more-shown');
            $(this).html($(this).attr('data-title-more'));
            $('.aiosc-ticket-content').animate({
                height: '150px'
            },500);
        }
        else {
            $(this).addClass('more-shown');
            $(this).html($(this).attr('data-title-less'));
            $('.aiosc-ticket-content').css({
                height: 'auto',
                display: 'none'
            }).slideDown();
        }
    });
    //edit ticket
    //ticket-edit-form
    var ticket_edit_form = $("#aiosc-ticket-edit-form");
    ticket_edit_form.ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            ticket_edit_form.find('button[type="submit"]').removeAttr('disabled');
            aiosc_log(data);
            var res = $.parseJSON(data);
            var response = $("#aiosc-edit-mode-response");
            response.removeClass("updated").removeClass("error");
            if(res.result != 1) {
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
            $("#aiosc-edit-mode-response").hide();
            ticket_edit_form.find('button[type="submit"]').attr('disabled','disabled');
        }
    });
    //edit reply
    var reply_edit_form = $("#aiosc-reply-edit-form");
    reply_edit_form.ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            reply_edit_form.find('input[type="submit"]').removeAttr('disabled');
            var res = $.parseJSON(data);
            var response = $("#reply-edit-form-response");
            response.removeClass("updated").removeClass("error");
            if(res.result != 1) {
                response.addClass("error");
            }
            else {
                response.addClass("updated");
                var data = (aiosc_tinymce_enabled()) ? tinymce.get('aiosc-reply-content').getContent({ format: 'raw' }) : jQuery("#aiosc-reply-content").val();
                jQuery("#aiosc-reply-content-"+reply_edit_form.find('input[name="reply_id"]').val()).html(data);
            }
            response.html("<p>"+res.message+"</p>");
            response.fadeIn();
            //$("html, body").animate({ scrollTop: 0 }, "slow");
        },
        beforeSubmit: function() {
            reply_edit_form.find('input[type="submit"]').attr('disabled','disabled');
        }
    });
    //remove reply
    var remove_reply_form = $("#aiosc-reply-remove-form");
    remove_reply_form.ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            remove_reply_form.find('input[type="submit"]').removeAttr('disabled');
            var res = $.parseJSON(data);
            var id = remove_reply_form.find('input[name="reply_id"]').val();

            if(res.result != 1) {
                alert(res.message);
                window.location.reload();
            }
            else {
                $("#aiosc-reply-"+id).fadeOut({
                    duration: 500,
                    complete: function() {
                        aiosc_popup_close($('.aiosc-popup'));
                        $("#aiosc-reply-"+id).remove();
                    }
                });
            }
        },
        beforeSubmit: function() {
            remove_reply_form.find('input[type="submit"]').attr('disabled','disabled');
        }
    });
    //request closure
    var request_closure_form = $("#aiosc-request-closure-form");
    request_closure_form.ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            request_closure_form.find('input[type="submit"]').removeAttr('disabled');
            var res = $.parseJSON(data);
            var response = $("#request-closure-response");
            response.removeClass("updated").removeClass("error");
            if(res.result != 1) {
                response.addClass('error');
                response.html("<p>"+res.message+"</p>");
                response.fadeIn();
            }
            else {
                $('.aiosc-request-closure-popup .aiosc-popup-content').html("<p>"+res.message+"</p>");
                request_closure_form.find('input[type="submit"]').remove();
                setTimeout(function() {
                    window.location.reload();
                },3000);
            }
        },
        beforeSubmit: function() {
            request_closure_form.find('input[type="submit"]').attr('disabled','disabled');
        }
    });
    //reopen ticket
    var reopen_ticket_form = $("#aiosc-reopen-ticket-form");
    reopen_ticket_form.ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            reopen_ticket_form.find('input[type="submit"]').removeAttr('disabled');
            var res = $.parseJSON(data);
            var response = $("#aiosc-reopen-ticket-response");
            response.removeClass("updated").removeClass("error");
            if(res.result != 1) {
                response.addClass('error');
                response.html("<p>"+res.message+"</p>");
                response.fadeIn();
            }
            else {
                $('.aiosc-reopen-ticket-popup .aiosc-popup-content').html("<p>"+res.message+"</p>");
                reopen_ticket_form.find('input[type="submit"]').remove();
                setTimeout(function() {
                    window.location.reload();
                },3000);
            }
        },
        beforeSubmit: function() {
            reopen_ticket_form.find('input[type="submit"]').attr('disabled','disabled');
        }
    });
    //close ticket
    var close_form = $("#aiosc-close-ticket-form");
    close_form.ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            close_form.find('input[type="submit"]').removeAttr('disabled');
            var res = $.parseJSON(data);
            var response = $("#close-ticket-response");
            response.removeClass("updated").removeClass("error");
            if(res.result != 1) {
                response.addClass('error');
                response.html("<p>"+res.message+"</p>");
                response.fadeIn();
            }
            else {
                $('.aiosc-close-ticket-popup .aiosc-popup-content').html("<p>"+res.message+"</p>");
                close_form.find('input[type="submit"]').remove();
                setTimeout(function() {
                    window.location.reload();
                },3000);
            }
        },
        beforeSubmit: function() {
            close_form.find('input[type="submit"]').attr('disabled','disabled');
        }
    });

});
function sidebar_init() {
    var toggler = jQuery('#aiosc-sidebar-toggler');
    var sidebar = jQuery('.aiosc-ticket-widgets > .aiosc-window');
    if(jQuery(window).width() > 940) {
        sidebar.show();
    }
    else {
        if(toggler.hasClass('shown'))  sidebar.show();
        else sidebar.hide();
    }
}
/**
 * Toggle sidebar on smaller screens
 * @since 1.0.9
 */
function aiosc_toggle_sidebar(toggler) {
    var sidebar = jQuery('.aiosc-ticket-widgets > .aiosc-window');
    if(toggler.hasClass('shown')) {
        toggler.removeClass('shown');
        sidebar.slideUp();
    }
    else {
        toggler.addClass('shown');
        sidebar.slideDown();
    }
}
var aiosc_replies_load_more = true;
function aiosc_load_replies(ticket) {
    if(!aiosc_replies_load_more) return;
    var load_more = jQuery('#aiosc-replies-load-more');
    var loading_reply = jQuery(".aiosc-reply-loading.aiosc-loading-bar");
    var no_more = jQuery("#aiosc-replies-no-more");
    var holder = jQuery('.aiosc-window.aiosc-replies');
    var loaded = holder.find('.aiosc-reply').length;
    var $ = jQuery;
    loading_reply.css('display','inline-block');
    load_more.find('a').hide();
    $.post(AIOSC_AJAX_URL, {
        action: 'aiosc_load_replies',
        ticket_id: ticket,
        from: loaded,
        frontend: (typeof AIOSC_FRONTEND_ACCESS != "undefined")?1:0
    },function(data) {
        aiosc_log(data);
        loading_reply.hide();
        var res = $.parseJSON(data);
        if(res.result == 1) {
            holder.append(res.data.html);
            load_more.appendTo(holder);
            load_more.find('a').show();
            no_more.insertAfter(load_more);
            aiosc_replies_loaded += res.data.limit;
        }
        else {
            load_more.remove();
            no_more.show();
            aiosc_replies_load_more = false;
        }
    })
}
function aiosc_insert_premade_response(sel, btn) {
    btn.attr('disabled','disabled');
    var ID = sel.val();
    sel.attr('disabled','disabled');
    jQuery.post(AIOSC_AJAX_URL, { action: 'aiosc_load_premade_response', response_id: ID },
    function(data) {
        btn.removeAttr('disabled');
        sel.removeAttr('disabled');
        aiosc_log(data);
        var res = jQuery.parseJSON(data);
        if(res.result == 1) {
            if(aiosc_tinymce_enabled() && tinymce.get('aiosc-content'))
                tinymce.get('aiosc-content').execCommand('mceInsertContent',true, res.data.html);
            else jQuery("#aiosc-content").val(res.data.html);
        }
        else alert(res.message);
    })
}
function aiosc_edit_reply_popup(reply, ticket) {
    var pop = jQuery('.aiosc-reply-editor');
    pop.find('input[name="reply_id"]').val(reply);
    pop.find('input[name="ticket_id"]').val(ticket);
    var content = jQuery("#aiosc-reply-content-"+reply).html();
    jQuery("#reply-edit-form-response").hide().html('');
    try {
	    if(aiosc_tinymce_enabled())
	        tinymce.get('aiosc-reply-content').setContent(content);
	    else
	        jQuery("#aiosc-reply-content").val(content);
    }
    catch (e) {
    	jQuery("#aiosc-reply-content").val(content);
    }
    jQuery("html, body").animate({ scrollTop: parseInt(pop.css('top')) }, "slow");
    aiosc_popup_open(pop);
}
function aiosc_remove_reply_popup(reply) {
    var pop = jQuery('.aiosc-reply-remove-popup');
    pop.find('input[name="reply_id"]').val(reply);
    jQuery("#reply-reply-remove-response").hide().html('');
    jQuery("html, body").animate({ scrollTop: parseInt(pop.css('top')) }, "slow");
    aiosc_popup_open(pop);
}
function aiosc_request_closure(ticket) {
    var pop = jQuery('.aiosc-request-closure-popup');
    pop.find('input[name="ticket_id"]').val(ticket);
    jQuery("#request-closure-response").hide().html('');
    jQuery("html, body").animate({ scrollTop: 0 }, "slow");
    aiosc_popup_open(pop);
}
function aiosc_reopen_ticket(ticket) {
    var pop = jQuery('.aiosc-reopen-ticket-popup');
    pop.find('input[name="ticket_id"]').val(ticket);
    jQuery("#aiosc-reopen-ticket-response").hide().html('');
    jQuery("html, body").animate({ scrollTop: 0 }, "slow");
    aiosc_popup_open(pop);
}
function aiosc_close_ticket(ticket) {
    var pop = jQuery('.aiosc-close-ticket-popup');
    pop.find('input[name="ticket_id"]').val(ticket);
    jQuery("#close-ticket-response").hide().html('');
    jQuery("html, body").animate({ scrollTop: 0 }, "slow");
    aiosc_popup_open(pop);
}