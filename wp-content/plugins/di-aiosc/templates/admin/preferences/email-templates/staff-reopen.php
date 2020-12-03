<?php
global $aiosc_settings, $aiosc_capabilities;
$tpl = aiosc_EmailManager::get_template('staff_reopen');
?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li data-screen="email-templates-customer-creation"><?php _e('Customers','aiosc')?></li>
        <li class="active" data-screen="email-templates-staff-creation"><?php _e('Staff','aiosc')?></li>
        <li data-screen="email-templates-cron-reminder-queue"><?php _e('Cron Reminders','aiosc')?></li>
    </ul>
</div>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">

        <li><?php _e('On Ticket:', 'aiosc')?></li>
        <li data-screen="email-templates-staff-creation"><?php _e('Assignment','aiosc')?></li>
        <li data-screen="email-templates-staff-reply"><?php _e('New Reply','aiosc')?></li>
        <li data-screen="email-templates-staff-closure"><?php _e('Closure Request','aiosc')?></li>
        <li class="active" data-screen="email-templates-staff-reopen"><?php _e('Re-Opening','aiosc')?></li>
    </ul>
</div>
<input type="hidden" name="section" value="email-templates-staff-reopen" />
<table class="form-table">
    <tbody>
    <tr>
        <th><label for="email-subject"><?php _e('Subject','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('You may use content tags here too.','aiosc')?> </small>
        </th>
        <td>
           <input type="text" style="width: 100%" id="email-subject" name="email-subject" value="<?php echo $tpl['subject']?>" />
        </td>
    </tr>
    <tr>
        <th><label for="aiosc-demo-wp_content"><?php _e('E-Mail Content','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
        </th>
        <td>
            <?php
            wp_editor(
                $tpl['content'],
                'aiosc-content'
            );
            ?>
        </td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td>
            <input type="submit" class="button button-primary" id="aiosc-form-submit" value="<?php _e('Save','aiosc')?>" />
            <button type="button" class="button" id="aiosc-form-discard"><?php _e('Discard','aiosc')?></button>
        </td>
    </tr>
    </tbody>
</table>
<div id="aiosc-content-tags-wrap">
    <select id="aiosc-content-tags">
        <option value=""><?php _e('- Browse Tags -','aiosc')?></option>
        <?php foreach(aiosc_get_content_tags() as $c=>$data) : ?>
            <optgroup label="<?php echo @$data['label'] ?>">
                <?php foreach($data['fields'] as $k=>$v) : ?>
                    <option value="{%<?php echo $k?>%}"><?php echo $v?></option>
                <?php endforeach; ?>
            </optgroup>
        <?php endforeach; ?>
    </select>
    <button type="button" class="button"><?php _e('Insert','aiosc')?></button>
</div>
<script type="text/javascript">
    if(aiosc_tinymce_enabled()) {
        var str = aiosc_tinyMCEPreInit;
        str = str.replace(/aiosc-demo-wp_editor/gi, 'aiosc-content');
        tinymce.init( JSON.parse(str).mceInit['aiosc-content'] );
        jQuery("input[type='submit']").on("mousedown",function() {
            tinymce.triggerSave(); //must save before submitting in order to pass data to request
        });
        jQuery('#aiosc-content-tags-wrap').appendTo('#wp-aiosc-content-editor-tools');
    }
</script>