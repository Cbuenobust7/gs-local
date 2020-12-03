<?php
global $aiosc_settings, $aiosc_capabilities;

$deps = aiosc_DepartmentManager::get_departments(true);
$saved_deps = $aiosc_settings->get('cron_autoclose_ignore_departments');
if(!is_array($saved_deps)) $saved_deps = array();
?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li data-screen="cron"><?php _e('General','aiosc')?></li>
        <li class="active" data-screen="cron-autoclose"><?php _e('Auto-Closing','aiosc')?></li>
        <li data-screen="cron-reminder-queue"><?php _e('Queue Reminder','aiosc')?></li>
    </ul>
</div>
<input type="hidden" name="section" value="cron-autoclose" />
<table class="form-table">
    <tbody>
    <tr>
        <td colspan="2">
            <em>
                <?php _e('This task will automatically close inactive tickets. It can also send notifications to customers before closure to inform them their tickets will be closed if they don\'t respond in timely manner.<br />You can find more details in the <strong>documentation</strong>.','aiosc')?>
            </em>
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="cron_autoclose_enable"><?php _e('Enable Auto-Closing','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Automatically close inactive tickets?','aiosc')?> </small></th>
        <td>
            <label>
                <input type="checkbox" id="cron_autoclose_enable" name="cron_autoclose_enable" <?php checked($aiosc_settings->get('cron_autoclose_enable')) ?> />
                <?php _e('Yes, enable Auto-Closing.','aiosc')?>
            </label>
        </td>
    </tr>
    <tr><td colspan="2" class="aiosc-title"><h3><?php _e('Notifications', 'aiosc')?></h3></td></tr>
    <tr>
        <th><label for="cron_autoclose_notify_customer"><?php _e('Notify Customer','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Send notification to customer when ticket is closed?','aiosc')?> </small></th>
        <td>
            <label>
                <input type="checkbox" id="cron_autoclose_notify_customer" name="cron_autoclose_notify_customer" <?php checked($aiosc_settings->get('cron_autoclose_notify_customer')) ?> />
                <?php _e('Yes, send e-mail notification to customer.','aiosc')?>
            </label>
        </td>
    </tr>
    <tr>
        <th>
            <label for="aiosc-content"><?php _e('Inactivity Closure Note','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Default closure note for tickets closed by cron because of inactivity.','aiosc')?> </small></th>
        </th>
        <td>
            <?php
            wp_editor(
                $aiosc_settings->get('cron_autoclose_closure_note'),
                'aiosc-content',
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
        </td>
    </tr>
    <tr>
        <th>
            <label for="aiosc-content"><?php _e('Requested Closure Note','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Default closure note for tickets closed by cron because closure was requested by customer.','aiosc')?> </small></th>
        </th>
        <td>
            <?php
            wp_editor(
                $aiosc_settings->get('cron_autoclose_requested_closure_note'),
                'aiosc-content-2',
                array(
                    "media_buttons"=>false,
                    "quicktags"=>false,
                    'textarea_rows'=>4,
                    "textarea_name"=>"content-2",
                    "tinymce"=>array(
                        "forced_root_block"=>false,
                        "force_br_newlines"=>true,
                        "force_p_newlines"=>false
                    )
                )
            );
            ?>
        </td>
    </tr>
    <tr><td colspan="2" class="aiosc-title"><h3><?php _e('Rules', 'aiosc')?></h3></td></tr>
    <tr>
        <th><label for="cron_autoclose_interval"><?php _e('Inactivity Interval','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('What is considered as inactive ticket?','aiosc')?> </small></th>
        <td>
            <label for="cron_autoclose_interval"><?php _e('Ticket must be older than:', 'aiosc') ?></label>
            <select name="cron_autoclose_interval" id="cron_autoclose_interval">
                <?php for($i=1;$i<=30;$i++) : ?>
                    <option value="<?php echo $i?>" <?php if($i == $aiosc_settings->get('cron_autoclose_interval')) : ?>selected<?php endif;?>><?php echo $i?></option>
                <?php endfor; ?>
            </select>
            <label for="cron_autoclose_interval"><?php _e('day(s) &nbsp; &nbsp; <strong>OR</strong> ', 'aiosc')?></label>
            &nbsp; &nbsp;
            <label style="text-decoration: underline; display: inline-block;" title="<?php _e('If this option is checked, tickets with Closure Request will be closed regardless of their age.','aiosc')?>">
                <input type="checkbox" id="cron_autoclose_requested_closure" name="cron_autoclose_requested_closure" <?php checked($aiosc_settings->get('cron_autoclose_requested_closure')) ?> />
                <?php _e('it has pending closure request.','aiosc')?>
            </label>
        </td>
    </tr>
    <tr>
        <th><label for="cron_autoclose_ignore_departments"><?php _e('Ignore Departments','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Ignore tickets from selected departments. <br /> <br />Hold CTRL to select multiple departments.','aiosc')?> </small></th>
        <td>
            <div class="aiosc-listbox">
                <select class="aiosc-listbox" name="cron_autoclose_ignore_departments[]" id="cron_autoclose_ignore_departments" size="8" multiple>
                    <?php foreach($deps as $dep) : ?>
                        <option value="<?php echo $dep->ID?>" <?php if(in_array($dep->ID, $saved_deps)) : ?>selected<?php endif; ?>><?php echo $dep->name?></option>
                    <?php endforeach; ?>
                </select>
                <button class="button button-list-clear" data-list="cron_autoclose_ignore_departments"><?php _e('None', 'aiosc')?></button>
            </div>
        </td>
    </tr>

    <tr><td colspan="2" class="aiosc-title"><h3><?php _e('Reminder', 'aiosc')?></h3></td></tr>
    <tr>
        <th><label for="cron_reminder_inactivity_enable"><?php _e('Enable Inactivity Reminder','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Notify customers when their ticket is considered inactive?','aiosc')?> </small></th>
        <td>
            <label>
                <input type="checkbox" id="cron_reminder_inactivity_enable" name="cron_reminder_inactivity_enable" <?php checked($aiosc_settings->get('cron_reminder_inactivity_enable')) ?> />
                <?php _e('Yes, enable Inactivity Reminder.','aiosc')?>
            </label>
        </td>
    </tr>
    <tr>
        <th><label for="cron_reminder_inactivity_interval"><?php _e('Reminder Interval','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('When to send reminder?','aiosc')?> </small></th>
        <td>
            <select name="cron_reminder_inactivity_interval" id="cron_reminder_inactivity_interval">
                <?php for($i=1;$i<=30;$i++) : ?>
                    <option value="<?php echo $i?>" <?php if($i == $aiosc_settings->get('cron_reminder_inactivity_interval')) : ?>selected<?php endif;?>><?php echo $i?></option>
                <?php endfor; ?>
            </select>
            <label for="cron_reminder_inactivity_interval"><?php printf(__('day(s) <a href="%s" title="%s">before closure</a>.', 'aiosc'),
                    '#cron_autoclose_interval', sprintf(__('day(s) before (%s) field', 'aiosc'),__('Inactivity Interval','aiosc')))?> </label>
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
<script type="text/javascript">
    if(aiosc_tinymce_enabled()) {
        var str = aiosc_tinyMCEPreInit;
        str = str.replace(/aiosc-demo-wp_editor/gi, 'aiosc-content');
        tinymce.init( JSON.parse(str).mceInit['aiosc-content'] );
        str = aiosc_tinyMCEPreInit;
        str = str.replace(/aiosc-demo-wp_editor/gi, 'aiosc-content-2');
        tinymce.init( JSON.parse(str).mceInit['aiosc-content-2'] );

        jQuery("input[type='submit']").on("mousedown",function() {
            tinymce.triggerSave(); //must save before submitting in order to pass data to request
        });
    }
</script>