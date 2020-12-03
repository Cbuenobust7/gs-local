jQuery(document).ready(function() {
    jQuery(document).on('change', 'select[name="aiosc_new_role"]', function(e) {
        jQuery('select[name="aiosc_new_role"]').not(jQuery(this)).val(jQuery(this).val());
    });
});