<div class="aiosc-popup aiosc-reply-editor">
    <div class="aiosc-popup-x" title="<?php _e('Close popup','aiosc')?>"><i class="dashicons dashicons-no"></i></div>
    <div class="aiosc-popup-header">
        <?php _e('Edit reply','aiosc')?>
    </div>
    <form id="aiosc-reply-edit-form" name="aiosc-reply-edit-form" action="<?php echo admin_url('/admin-ajax.php')?>" method="post">
        <input type="hidden" name="ticket_id" value="0" />
        <input type="hidden" name="reply_id" value="0" />
        <input type="hidden" name="action" value="aiosc_new_reply" />
    <div class="aiosc-popup-content">
        <div id="reply-edit-form-response"></div>
        <?php wp_editor('','aiosc-reply-content',array(
            "media_buttons"=>false,
            "quicktags"=>true,
            'textarea_rows'=>4,
            "textarea_name"=>"content",
            "tinymce"=>array(
                "forced_root_block"=>false,
                "force_br_newlines"=>true,
                "force_p_newlines"=>false
            )
        ))?>
    </div>
        <div class="aiosc-popup-controls">
            <input type="submit" class="button button-primary" value="<?php _e('Update Reply','aiosc')?>" />
            <button type="button" class="button aiosc-discard-button"><?php _e('Discard','aiosc')?></button>
        </div>

    </form>
</div>