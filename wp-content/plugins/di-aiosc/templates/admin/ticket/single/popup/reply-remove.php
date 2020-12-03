<div class="aiosc-popup aiosc-reply-remove-popup">
    <div class="aiosc-popup-x" title="<?php _e('Close popup','aiosc')?>"><i class="dashicons dashicons-no"></i></div>
    <div class="aiosc-popup-header">
        <?php _e('Remove Reply','aiosc')?>
    </div>
    <form id="aiosc-reply-remove-form" name="aiosc-reply-remove-form" action="<?php echo admin_url('/admin-ajax.php')?>" method="post">
        <input type="hidden" name="reply_id" value="0" />
        <input type="hidden" name="action" value="aiosc_reply_remove" />
    <div class="aiosc-popup-content">
        <div id="reply-remove-response"></div>
        <p>
            <?php _e('Are you sure you want to remove this reply?','aiosc') ?>
        </p>
    </div>
        <div class="aiosc-popup-controls">
            <input type="submit" class="button button-primary" value="<?php _e('Confirm','aiosc')?>" />
            <button type="button" class="button aiosc-discard-button"><?php _e('Cancel','aiosc')?></button>
        </div>

    </form>
</div>