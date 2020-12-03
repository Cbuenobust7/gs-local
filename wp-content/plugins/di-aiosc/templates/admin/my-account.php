<?php
wp_enqueue_media();
?>
<?php aisoc_print_js_debug() ?>
<div class="aiosc-wp_editor">
    <?php wp_editor(
        '',
        'aiosc-demo-wp_editor',
        array(
            "media_buttons"=>false,
            "quicktags"=>false,
            'textarea_rows'=>4,
            "textarea_name"=>"content",
            "tinymce"=>array(
                "forced_root_block"=>false,
                "force_br_newlines"=>true,
                "force_p_newlines"=>false
            )
        )
    );
    ?>
</div>
<div class="wrap">
    <div class="aiosc-window">
        <h2 class="page-title"><?php _e('My Account','aiosc') ?>
            <span><?php _e('Edit your AIOSC account.','aiosc') ?></span></h2>
        <div class="aiosc-toolbar">
            <ul class="aiosc-tabs">
                <li data-screen="general"><?php _e('General','aiosc')?></li>
                <li data-screen="premade-responses"><?php _e('Pre-Made Responses','aiosc')?></li>
            </ul>
        </div>
        <div class="aiosc-clear"></div>
        <div id="ajax-response"></div>

        <div class="aiosc-form">
            <form method="post" id="aiosc-form" action="<?php echo get_admin_url()?>admin-ajax.php">
                <input type="hidden" name="action" value="aiosc_account_save" />
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