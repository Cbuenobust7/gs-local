<?php
global $aiosc_settings, $aiosc_user, $aiosc_capabilities;
?>
<input type="hidden" name="section" value="general" />
<table class="form-table">
    <tbody>
    <tr>
        <th><label><?php _e('E-Mail Notifications','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Would you like to receive notifications?','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="aiosc_notifications" name="aiosc_notifications" value="1" <?php checked($aiosc_user->get_meta('aiosc_notifications', false))?>>
                <?php _e('Send me notifications related to tickets assigned to me.','aiosc')?></label>
            <br /><br />
            <label>
                <input type="checkbox" id="aiosc_department_notifications" name="aiosc_department_notifications" value="1" <?php checked($aiosc_user->get_meta('aiosc_department_notifications', false))?>>
                <?php _e('Send me all notifications related to departments I am assigned to.','aiosc')?></label>
        </td>
    </tr>
    <tr>
        <th><label><?php _e('Hide "Create New" page','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Would you like to hide this page from main menu for your account?','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="aiosc_staff_create_form_disable" name="aiosc_staff_create_form_disable" value="1" <?php checked($aiosc_user->get_meta('aiosc_staff_create_form_disable', false))?>>
                <?php _e('Yes please, hide it.','aiosc')?></label>
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