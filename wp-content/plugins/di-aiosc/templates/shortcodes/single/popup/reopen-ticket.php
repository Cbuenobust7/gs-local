<?php global $aiosc_user; ?>
<div class="aiosc-popup aiosc-reopen-ticket-popup">
    <div class="aiosc-popup-x" title="<?php _e('Close popup','aiosc')?>"><i class="dashicons dashicons-no"></i></div>
    <div class="aiosc-popup-header">
        <?php _e('Re-Open Ticket','aiosc')?>
    </div>
    <form id="aiosc-reopen-ticket-form" name="aiosc-reopen-ticket-form" action="<?php echo admin_url('/admin-ajax.php')?>" method="post">
        <input type="hidden" name="ticket_id" value="0" />
        <input type="hidden" name="action" value="aiosc_reopen_ticket" />
        <div class="aiosc-popup-content">
            <div id="aiosc-reopen-ticket-response" class="aiosc-form-response"></div>
            <p>
                <?php _e('Are you sure you want to re-open on this ticket?','aiosc') ?>
            </p>
        </div>
        <div class="aiosc-popup-controls">
            <?php if($aiosc_user->can('staff')) : ?>
                <label class="aiosc-fleft" title="<?php _e('Send an e-mail notification telling customer his ticket is open again.', 'aiosc')?>">
                    <input type="checkbox" name="notify_customer" checked value="1" /><?php _e('Send notification to customer.', 'aiosc')?></label>
            <?php endif; ?>
            <input type="submit" class="button button-primary" value="<?php _e('Confirm','aiosc')?>" />
            <button type="button" class="button aiosc-discard-button"><?php _e('Cancel','aiosc')?></button>
        </div>

    </form>
</div>