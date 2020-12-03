<?php
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;

$nbox_pos = array(
    'top-left'=>__('Top - Left','aiosc'),
    'top-right'=>__('Top - Right','aiosc'),
    'bottom-left'=>__('Bottom - Left','aiosc'),
    'bottom-right'=>__('Bottom - Right','aiosc')
);
$def = $aiosc_settings->defaults;
?>
<input type="hidden" name="section" value="tickets" />
<table class="form-table">
    <tbody>
    <tr><td colspan="2" class="aiosc-title"><h3><?php _e('Tickets','aiosc')?></h3><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="min_subject_len"><?php _e('Min. Subject Length','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('At least, how many characters subject must contain?','aiosc')?> </small>
        </th>
        <td>
            <input type="number" min=0 id="min_subject_len" name="min_subject_len" value="<?php echo $aiosc_settings->get('min_subject_len')?>" > <?php _e('characters','aiosc')?>
        </td>
    </tr>
    <tr>
        <th><label for="min_content_len"><?php _e('Min. Content Length','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('At least, how many characters content must contain?','aiosc')?> </small>
        </th>
        <td>
            <input type="number" min="<?php echo $def['min_content_len']?>" id="min_content_len" name="min_content_len" value="<?php echo $aiosc_settings->get('min_content_len')?>" > <?php _e('characters','aiosc')?>
        </td>
    </tr>
    <tr>
        <th><label for="creation_delay"><?php _e('Creation Delay','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('How much time must pass before customer can create another ticket?','aiosc')?> </small>
        </th>
        <td>
            <input type="number" min=0 id="creation_delay" name="creation_delay" value="<?php echo $aiosc_settings->get('creation_delay')?>" > <?php _e('seconds','aiosc')?>
        </td>
    </tr>
    <tr>
        <th><label for="allow_reopen_tickets"><?php _e('Allow Re-Opening','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Would you like to let your customers re-open closed tickets?','aiosc')?> </small>
        </th>
        <td>
            <label for="allow_reopen_tickets"><input type="checkbox" id="allow_reopen_tickets" name="allow_reopen_tickets" <?php checked($aiosc_settings->get('allow_reopen_tickets')) ?> />
                <?php _e('Yes, let customers re-open closed tickets.', 'aiosc'); ?></label>
        </td>
    </tr>

    <tr><td colspan="2" class="aiosc-title"><h3><?php _e('Replies','aiosc')?></h3><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="min_reply_len"><?php _e('Min. Reply Length','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('At least, how many characters reply content must contain?','aiosc')?> </small>
        </th>
        <td>
            <input type="number" min=10 id="min_reply_len" name="min_reply_len" value="<?php echo $aiosc_settings->get('min_reply_len')?>" > <?php _e('characters','aiosc')?>
        </td>
    </tr>
    <tr>
        <th><label for="reply_delay"><?php _e('Reply Delay','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('How much time must pass before customer can post another reply on the same ticket?','aiosc')?> </small>
        </th>
        <td>
            <input type="number" min=0 id="reply_delay" name="reply_delay" value="<?php echo $aiosc_settings->get('reply_delay')?>" > <?php _e('seconds','aiosc')?>
        </td>
    </tr>

    <tr>
        <th>&nbsp;</th>
        <td>
            <input type="submit" class="button button-primary" id="aiosc-form-submit" value="<?php _e('Submit','aiosc')?>" />
            <button type="button" class="button" id="aiosc-form-discard"><?php _e('Discard','aiosc')?></button>
        </td>
    </tr>
    </tbody>
</table>