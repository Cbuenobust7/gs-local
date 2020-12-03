<?php aisoc_print_js_debug() ?>
<div class="wrap">
    <div class="aiosc-window">
        <h2 class="page-title"><?php _e('Tools','aiosc') ?>
            <span><?php _e('Here are some AIOSC tools that we found useful they may be of use for you too.','aiosc') ?></span></h2>
        <div class="aiosc-toolbar">
            <ul class="aiosc-tabs">
                <li data-screen="general"><?php _e('User Roles','aiosc')?></li>
            </ul>
        </div>
        <div class="aiosc-clear"></div>
        <div id="ajax-response"></div>

        <div class="aiosc-form">
            <form method="post" id="aiosc-form" action="<?php echo get_admin_url()?>admin-ajax.php">
                <input type="hidden" name="action" value="aiosc_tools_submit" />
                <div class="aiosc-tab-content-holder">
                    <div class="aiosc-loading-holder"><div class="aiosc-loading-bar"><span><?php _e('Loading Screen...','aiosc')?></span></div></div>
                    <div class="aiosc-tab-content">

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    jQuery(document).on("click","#aiosc-content-tags-wrap > button",function() {
        var sel = jQuery(this).parent().find("select");
        if(sel.val() != "") {
            if (aiosc_tinymce_enabled()) {
                tinymce.get('aiosc-content').execCommand('mceInsertContent', true, sel.val());
            }
            else jQuery('#aiosc-content').val(sel.val());
        }
    })
</script>