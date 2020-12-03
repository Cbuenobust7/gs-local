<div class="aiosc-popup aiosc-request-closure-popup">
    <div class="aiosc-popup-x" title="<?php _e('Close popup','aiosc')?>"><i class="dashicons dashicons-no"></i></div>
    <div class="aiosc-popup-header">
        <?php _e('Request ticket closure','aiosc')?>
    </div>
    <form id="aiosc-request-closure-form" name="aiosc-request-closure-form" action="<?php echo admin_url('/admin-ajax.php')?>" method="post">
        <input type="hidden" name="ticket_id" value="0" />
        <input type="hidden" name="action" value="aiosc_request_closure" />
    <div class="aiosc-popup-content">
        <div id="request-closure-response"></div>
        <p>
            <?php _e('Are you sure you want to request closure on this ticket?','aiosc') ?>
        </p>
    </div>
        <div class="aiosc-popup-controls">
            <input type="submit" class="button button-primary" value="<?php _e('Confirm','aiosc')?>" />
            <button type="button" class="button aiosc-discard-button"><?php _e('Cancel','aiosc')?></button>
        </div>

    </form>
</div>