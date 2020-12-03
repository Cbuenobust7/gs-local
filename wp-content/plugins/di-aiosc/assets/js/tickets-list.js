var loader = jQuery('.aiosc-loading-holder');
var response = jQuery("#ajax-response");
var screen = jQuery('.aiosc-tab-content');

jQuery(document).ready(function($) {
    loader = $('.aiosc-loading-holder');
    response = $("#ajax-response");
    screen = $('.aiosc-tab-content');
    $(document).on('click', '#btn-toggle-filters', function(e) {
        e.preventDefault();
        var f = $('.aiosc-filters .aiosc-filters-container');
        if(f.length) {
            f.stop(true,true).slideToggle(100);
        }
    });
    $("#adv-settings").ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            window.location.reload();
        }
    });
    $(document).on('click', '#reset-filters', function() {
        $("#filter-priorities").val($("#filter-priorities option:eq(0)").val());
        $("#filter-departments").val($("#filter-departments option:eq(0)").val());
        $("#filter-is_public").val($("#filter-is_public option:eq(0)").val());
        $("#filter-authors").select2('val', $("#filter-authors option:eq(0)").val());
        $("#filter-operators").select2('val', $("#filter-operators option:eq(0)").val());
        $("#filter-awaiting_staff_reply").prop('checked', false);
        $("#filter-requested_closure").prop('checked', false);
    });
    $("#aiosc-form").ajaxForm({
        success: function(data, textStatus, jqXHR, $form) {
            aiosc_log(data);
            var res = jQuery.parseJSON(data);
            if(res.result == 1) {
                jQuery('.aiosc-tab-content').html(res.data.html);
                loader.fadeOut(300);
                $('.aiosc-tabs').find('.active').removeClass('active');
                $('.aiosc-tabs').find('li[data-screen="'+$('#list-screen').val()+'"]').addClass('active');
            }
            else {
                response.addClass("error");
                response.html("<p>"+res.message+"</p>").show();
            }
            loader.fadeOut(100);
        },
        beforeSubmit: function() {
            response.hide();
            response.removeClass('error').removeClass('updated');
            loader.fadeIn(10);
        }
    });
    //PAGINATION
    $(document).on('click','.aiosc-pagination a',function(e) {
        e.preventDefault();
        var p = $(this).attr('data-page');
        $(this).parent().find('input[name="paged"]').val(p);
        $(this).parents('form').submit();
    });

    $(document).on('click','.aiosc-tabs > li',function(e) {
        var me = $(this);
        var screen_id = me.attr('data-screen');
        if(screen_id) {
            switch_status(screen_id);
        }
    });

    $(document).on('click','.aiosc-tabs > li',function(e) {
        var me = $(this);
        var screen_id = me.attr('data-screen');
        if(screen_id) {
            switch_status(screen_id);
        }
    });
    $(document).on('click','th.sortable > a',function(e) {
        e.preventDefault();
        var order = $("#list-order");
        console.log(order.val());
        var sort = $("#list-sort");
        if($(this).parent().hasClass('desc')) {
            order.val('asc');
        }
        else order.val('desc');
        sort.val($(this).attr('data-order'));
        reload_screen();
    });
    click_first_tab();
});
function reload_screen() {
    jQuery("#aiosc-form").submit();
}
function ticket_quick_delete(id) {
    var chk = jQuery('input#ticket_'+id);
    jQuery('input[name="tickets[]"]').prop('checked',false);
    chk.prop('checked',true);
    jQuery('select[name="bulkaction"]').val('delete');
    jQuery('input#doaction').trigger('click');
}
function switch_status(screen_id) {
    jQuery("#list-screen").val(screen_id);
    if(jQuery('.aiosc-tab-content').find('input[type="hidden"][name="paged"]').length < 1)
        jQuery('.aiosc-tab-content').find('input[type="text"][name="paged"]').val(1);

    jQuery("#aiosc-form").submit();
}
function click_first_tab() {
    jQuery('.aiosc-tabs > li[data-screen="'+jQuery("#list-screen").val()+'"]').trigger('click');
}