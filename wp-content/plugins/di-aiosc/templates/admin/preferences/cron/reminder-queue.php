<?php
global $aiosc_settings, $aiosc_capabilities;

$deps = aiosc_DepartmentManager::get_departments(true);
$saved_deps = $aiosc_settings->get('cron_reminder_queue_ignore_departments');
if(!is_array($saved_deps)) $saved_deps = array();
?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li data-screen="cron"><?php _e('General','aiosc')?></li>
        <li data-screen="cron-autoclose"><?php _e('Auto-Closing','aiosc')?></li>
        <li class="active" data-screen="cron-reminder-queue"><?php _e('Queue Reminder','aiosc')?></li>
    </ul>
</div>
<input type="hidden" name="section" value="cron-reminder-queue" />
<table class="form-table">
    <tbody>
    <tr>
        <td colspan="2">
            <em>
                <?php _e('This task will send e-mail notifications to all customers whose tickets are in queue longer than expected.<br />You can find more details in the <strong>documentation</strong>.','aiosc')?>
            </em>
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="cron_reminder_queue_enable"><?php _e('Enable Queue Reminder','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Notify customers when their ticket is in queue longer than expected?','aiosc')?> </small></th>
        <td>
            <label>
                <input type="checkbox" id="cron_reminder_queue_enable" name="cron_reminder_queue_enable" <?php checked($aiosc_settings->get('cron_reminder_queue_enable')) ?> />
                <?php _e('Yes, enable Queue Reminder.','aiosc')?>
            </label>
        </td>
    </tr>
    <tr>
        <th><label for="cron_reminder_queue_interval"><?php _e('Delay Interval','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('What is considered as delayed ticket?','aiosc')?> </small></th>
        <td>
            <label for="cron_reminder_queue_interval"><?php _e('Ticket is in <strong>queue</strong> for at least:', 'aiosc') ?></label>
            <select name="cron_reminder_queue_interval" id="cron_reminder_queue_interval">
                <?php for($i=1;$i<=30;$i++) : ?>
                    <option value="<?php echo $i?>" <?php if($i == $aiosc_settings->get('cron_reminder_queue_interval')) : ?>selected<?php endif;?>><?php echo $i?></option>
                <?php endfor; ?>
            </select>
            <label for="cron_reminder_queue_interval"><?php _e('day(s)', 'aiosc')?></label>
            &nbsp; &nbsp; &nbsp; &nbsp;
            <label style="text-decoration: underline; display: inline-block;" title="<?php _e('If this option is checked, all tickets awaiting staff reply will be included, both OPEN and IN QUEUE.','aiosc')?>">
                <input type="checkbox" id="cron_reminder_queue_include_open" name="cron_reminder_queue_include_open" <?php checked($aiosc_settings->get('cron_reminder_queue_include_open')) ?> />
                <?php _e('Include <strong>open</strong> tickets awaiting staff reply as well.','aiosc')?>
            </label>
        </td>
    </tr>
    <tr>
        <th><label for="cron_reminder_queue_ignore_departments"><?php _e('Ignore Departments','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Ignore tickets from selected departments. <br /> <br />Hold CTRL to select multiple departments.','aiosc')?> </small></th>
        <td>
            <div class="aiosc-listbox">
                <select class="aiosc-listbox" name="cron_reminder_queue_ignore_departments[]" id="cron_reminder_queue_ignore_departments" size="8" multiple>
                    <?php foreach($deps as $dep) : ?>
                        <option value="<?php echo $dep->ID?>" <?php if(in_array($dep->ID, $saved_deps)) : ?>selected<?php endif; ?>><?php echo $dep->name?></option>
                    <?php endforeach; ?>
                </select>
                <button class="button button-list-clear" data-list="cron_reminder_queue_ignore_departments"><?php _e('None', 'aiosc')?></button>
            </div>
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