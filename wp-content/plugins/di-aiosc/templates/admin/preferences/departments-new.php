<?php
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;

if(isset($_POST['department_id']) && is_numeric($_POST['department_id']) && $_POST['department_id'] > 0)
    $department = new aiosc_Department($_POST['department_id']);
else
    $department = false;
?>
<input type="hidden" name="section" value="<?php echo !aiosc_is_department($department)?'departments-new':'departments-edit'?>" />
<?php if(aiosc_is_department($department)) : ?>
<input type="hidden" name="department_id" value="<?php echo $department->ID ?>" />
<?php endif; ?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li data-screen="departments"><?php _e('List','aiosc')?></li>
        <li <?php if(!aiosc_is_department($department)) : ?>class="active"<?php endif; ?> data-screen="departments-new"><?php _e('Add New','aiosc')?></li>
        <?php if(aiosc_is_department($department)) : ?>
            <li class="active"><?php printf(__('Editing &quot;%s&quot;','aiosc'),$department->name) ?></li>
        <?php endif; ?>
    </ul>
</div>
<table class="form-table">
    <tbody>
    <tr>
        <th><label for="dep-name"><?php _e('Name','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Name must be unique.','aiosc')?> </small>
        </th>
        <td>
            <input type="text" id="dep-name" name="name" value="<?php echo @$department->name?>" >
        </td>
    </tr>
    <tr>
        <th><label for="dep-desc"><?php _e('Description','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Not required but can help in management.','aiosc')?> </small>
        </th>
        <td>
            <textarea id="dep-desc" rows=4 cols=50 name="description"><?php echo @$department->description?></textarea>
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="dep-assigns"><?php _e('Assign Operators','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('You can assign more than one operator.','aiosc')?> </small>
        </th>
        <td>
            <div class="aiosc-ops-list">
                <ul>
                    <?php
                    $ops = aiosc_UserManager::get_users_with_capability('staff', true);
                    if(count($ops) > 0) :
                        foreach($ops as $op) :
                            if(get_user_meta($op->ID,'aiosc_op_on_break',true) != true) :
                    ?>
                    <li><label><input type="checkbox" name="ops[]" value="<?php echo $op->ID ?>" <?php checked(in_array($op->ID, @$department->operators)) ?> />
                            <?php echo get_avatar($op->ID,24); ?>
                            <a href="<?php aiosc_user_url($op->ID)?>" target="_blank"><strong><?php echo $op->wpUser->display_name ?></strong>
                                <small><em>@<?php echo $op->wpUser->user_login;?></em></small></a>
                            <small> - <em><?php echo $aiosc_capabilities->get_role_name($op->aiosc_role)?></em></small>
                        </label></li>
                    <?php
                                endif;
                    endforeach;
                        else : ?>

                    <?php endif; ?>
                </ul>
            </div>
        </td>
    </tr>
    <?php echo do_action('aiosc_custom_department_fields', aiosc_is_department($department) ? $department : false) ?>
    <tr>
        <th><label for="dep-active"><?php _e('Active?','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Is this department available for use?','aiosc')?> </small>
        </th>
        <td>
            <?php $isactive = aiosc_is_department($department) && !$department->is_active?false:true; ?>
            <input type="checkbox" id="dep-active" name="active" value="1" <?php checked($isactive); ?> />
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th>&nbsp;</th>
        <td>
            <input type="submit" class="button button-primary" id="aiosc-form-submit" value="<?php echo aiosc_is_department($department)?__('Update','aiosc'):__('Save','aiosc')?>" />
            <button type="button" class="button" onClick="javascript:click_first_subtab()"><?php _e('Discard','aiosc')?></button>
        </td>
    </tr>
    </tbody>
</table>