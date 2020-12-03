<?php
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;

if(isset($_POST['priority_id']) && is_numeric($_POST['priority_id']) && $_POST['priority_id'] > 0)
    $priority = new aiosc_Priority($_POST['priority_id']);
else
    $priority = false;
?>
<input type="hidden" name="section" value="<?php echo !aiosc_is_priority($priority)?'priorities-new':'priorities-edit'?>" />
<?php if(aiosc_is_priority($priority)) : ?>
<input type="hidden" name="priority_id" value="<?php echo $priority->ID ?>" />
<?php endif; ?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li data-screen="priorities"><?php _e('List','aiosc')?></li>
        <li <?php if(!aiosc_is_priority($priority)) : ?>class="active"<?php endif; ?> data-screen="priorities-new"><?php _e('Add New','aiosc')?></li>
        <?php if(aiosc_is_priority($priority)) : ?>
            <li class="active"><?php printf(__('Editing &quot;%s&quot;','aiosc'),$priority->name) ?></li>
        <?php endif; ?>
    </ul>
</div>
<table class="form-table">
    <tbody>
    <tr>
        <th><label for="pri-name"><?php _e('Name','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Name must be unique.','aiosc')?> </small>
        </th>
        <td>
            <input type="text" id="pri-name" name="name" value="<?php echo @$priority->name?>" >
        </td>
    </tr>
    <tr>
        <th><label for="pri-desc"><?php _e('Description','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Not required but can help in management.','aiosc')?> </small>
        </th>
        <td>
            <textarea id="pri-desc" rows=4 cols=50 name="description"><?php echo @$priority->description?></textarea>
        </td>
    </tr>
    <tr>
        <th><label for="pri-level"><?php _e('Level','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Higher level = higher importance.','aiosc')?> </small>
        </th>
        <td>
            <input type="number" size="2" id="pri-level" name="level" value="<?php echo (int)@$priority->level?>" >
        </td>
    </tr>
    <tr>
        <th><label for="pri-color"><?php _e('Color','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Use colors for easier managing.','aiosc')?> </small>
        </th>
        <td>
            <input type="text" class="aiosc-color-input" id="pri-color" name="color" value="<?php echo @$priority->color?>" >
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="dep-active"><?php _e('Active?','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Is this priority available for use?','aiosc')?> </small>
        </th>
        <td>
            <?php $isactive = aiosc_is_priority($priority) && !$priority->is_active?false:true; ?>
            <input type="checkbox" id="pri-active" name="active" value="1" <?php checked($isactive); ?> />
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th>&nbsp;</th>
        <td>
            <input type="submit" class="button button-primary" id="aiosc-form-submit" value="<?php echo aiosc_is_priority($priority)?__('Update','aiosc'):__('Save','aiosc')?>" />
            <button type="button" class="button" onClick="javascript:click_first_subtab()"><?php _e('Discard','aiosc')?></button>
        </td>
    </tr>
    </tbody>
</table>