<?php
global $aiosc_settings, $aiosc_capabilities;

?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li class="active" data-screen="cron"><?php _e('General','aiosc')?></li>
        <li data-screen="cron-autoclose"><?php _e('Auto-Closing','aiosc')?></li>
        <li data-screen="cron-reminder-queue"><?php _e('Queue Reminder','aiosc')?></li>
    </ul>
</div>
<input type="hidden" name="section" value="cron-general" />
<table class="form-table">
    <tbody>
    <tr>
        <td colspan="2">
            <em>
                <?php _e('Before enabling CRON, you should read detailed guide on how to setup AIOSC CRON in the documentation.','aiosc')?>
            </em>
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="cron_enable"><?php _e('Enable Cron','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Would you like to run scheduled AIOSC tasks?','aiosc')?> </small></th>
        <td>
            <label>
            <input type="checkbox" id="cron_enable" name="cron_enable" <?php checked($aiosc_settings->get('cron_enable')) ?> />
                <?php _e('Yes, enable AIOSC Cron.','aiosc')?>
            </label>
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