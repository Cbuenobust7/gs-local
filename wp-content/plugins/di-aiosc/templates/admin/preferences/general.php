<?php
global $aiosc_settings, $aiosc_capabilities;
?>
<input type="hidden" name="section" value="general" />
<table class="form-table">
    <tbody>
    <?php
    /*
     <tr>
        <th><label for="enable_hints"><?php _e('Enable Hints','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Would you like to see hints next to input fields?','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="enable_hints" name="enable_hints" value="1" <?php checked(aiosc_get_settings('enable_hints'))?>>
                <?php _e('Yes, enable hints.','aiosc')?></label>
        </td>
    </tr>
     */ ?>
    <tr>
        <th><label for="enable_staff_ribbon"><?php _e('Enable STAFF Ribbon','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Would you like to have ribbon over your avatar?','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="enable_staff_ribbon" name="enable_staff_ribbon" value="1" <?php checked(aiosc_get_settings('enable_staff_ribbon'))?>>
                <?php _e('Yes, show staff ribbon over avatars of staff members.','aiosc')?></label>
        </td>
    </tr>
    <tr>
        <th><label for="enable_public_tickets"><?php _e('Enable Public Tickets','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Customers can create public tickets?','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="enable_public_tickets" name="enable_public_tickets" value="1" <?php checked(aiosc_get_settings('enable_public_tickets'))?>>
                <?php _e('Yes, allow users to make their tickets publicly visible.','aiosc')?></label>
        </td>
    </tr>
    <tr><td colspan="2" class="aiosc-title"><h3><?php _e('User Roles','aiosc')?></h3><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="default_role"><?php _e('Default Role','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Upon registration, users will be granted with this role.','aiosc')?> </small></th>
        <td>
            <select id="default_role" name="default_role">
                <option value=""><?php _e('- No default role -','aiosc')?></option>
                <?php
                foreach($aiosc_capabilities->get_roles() as $k=>$v) :
                    $selected = $k === aiosc_get_settings('default_role')?'selected="selected"':'';
                    ?>
                    <option value="<?php echo $k?>" <?php echo $selected ?>><?php echo $v['name']?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr><td colspan="2" class="aiosc-title"><h3><?php _e('Attachments','aiosc')?></h3><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="allow_upload"><?php _e('Allow upload','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Allow customers to upload files along with tickets & replies.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" value="1" <?php checked(aiosc_get_settings('allow_upload'))?> id="allow_upload" name="allow_upload" />
                <?php _e('Yes, allow users to upload attachments with tickets and replies.','aiosc') ?></label>
        </td>
    </tr>
    <tr>
        <th><label for="allow_download"><?php _e('Allow download','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Allow customers to download their files anytime.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" value="1" <?php checked(aiosc_get_settings('allow_download'))?> id="allow_download" name="allow_download" />
                <?php _e('Yes, allow users to download attachments from tickets and replies.','aiosc') ?></label>
        </td>
    </tr>
    <tr>
        <th><label for="max_upload_size_per_file"><?php _e('Maximum file size (Kb)','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Maximum size of single file, not the size of whole upload.','aiosc')?> </small>
        </th>
        <td>
            <input type="number" min=1 required value="<?php echo aiosc_get_settings('max_upload_size_per_file')?>" id="max_upload_size_per_file" name="max_upload_size_per_file" />
            <?php _e('Kb','aiosc') ?> &nbsp;
            <em><?php printf(__('Max. possible value is <code>%sb</code>, according to <code>%s</code>.','aiosc'),aiosc_ini_get('upload_max_filesize',0),'php.ini') ?></em>
        </td>
    </tr>
    <tr>
        <th><label for="max_files_per_ticket"><?php _e('Max. files per ticket','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('How many files can user attach to single ticket?','aiosc')?> </small>
        </th>
        <td>
            <input type="number" min=1 <?php echo aiosc_ini_get('max_file_uploads') != ''?'max="'.aiosc_ini_get('max_file_uploads').'"':''?> required value="<?php echo aiosc_get_settings('max_files_per_ticket')?>" id="max_files_per_ticket" name="max_files_per_ticket" />
            <em><?php printf(__('Max. possible value is <code>%s</code>, according to <code>%s</code>.','aiosc'),aiosc_ini_get('max_file_uploads'),'max_file_uploads') ?></em>
        </td>
    </tr>
    <tr>
        <th><label for="max_files_per_reply"><?php _e('Max. files per reply','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('How many files can user attach to single ticket reply?','aiosc')?> </small>
        </th>
        <td>
            <input type="number" min=1 <?php echo aiosc_ini_get('max_file_uploads') != ''?'max="'.aiosc_ini_get('max_file_uploads').'"':''?> required value="<?php echo aiosc_get_settings('max_files_per_reply')?>" id="max_files_per_reply" name="max_files_per_reply" />
            <em><?php printf(__('Max. possible value is <code>%s</code>, according to <code>%s</code>.','aiosc'),aiosc_ini_get('max_file_uploads'),'max_file_uploads') ?></em>
        </td>
    </tr>

    <tr><td colspan="2" class="aiosc-title"><h3><?php _e('Attachment Mime-types','aiosc')?></h3><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="upload_mimes"><?php _e('Allowed Mime-types','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('File extensions (comma separated) that are allowed for upload.','aiosc')?> </small>
        </th>
        <td>
            <input type="text" style="width: 100%" required value="<?php echo aiosc_get_settings('upload_mimes')?>" id="upload_mimes" name="upload_mimes" />
        </td>
    </tr>
    <tr>
        <th><label for="upload_mimes_forbid"><?php _e('Forbid Mime-types','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Instead of allowing above mime-types, you can forbid them.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" value="1" <?php checked(aiosc_get_settings('upload_mimes_forbid'))?> id="upload_mimes_forbid" name="upload_mimes_forbid" />
                <?php _e('Forbid above extensions and allow all others.','aiosc') ?></label>
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