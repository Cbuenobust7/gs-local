jQuery(document).ready(function($) {

});
function aiosc_download_file(file_id, ticket_id) {
    if(typeof file_id != "undefined" && typeof ticket_id != "undefined") {
        var iframe = jQuery("<iframe></iframe>");
        iframe.attr('src',AIOSC_AJAX_URL+'?action=aiosc_download_file&file_id='+file_id+'&ticket_id='+ticket_id);
        iframe.css({
            width: 0,
            height: 0,
            position: 'absolute',
            'margin-left': '-99999px',
            'margin-top': '-99999px'
        });
        iframe.appendTo(jQuery('body'));
        setTimeout(function() {
            iframe.remove();
        },20000); //remove iframe after 20 seconds
    }
}