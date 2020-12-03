<?php
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;

$action = @$_POST['action2']; //DELETE | ACTIVATE | DEACTIVATE
$departments = @$_POST['departments']; //ID of departments

?>
<input type="hidden" name="section" value="departments-update" />
<input type="hidden" name="confirmation" value="<?php echo $action ?>" />
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li data-screen="departments"><?php _e('List','aiosc')?></li>
        <li data-screen="departments-new"><?php _e('Add New','aiosc')?></li>
        <li class="active">
            <?php
            if($action == 'delete') {
                if(count($departments) == 1)
                    printf(__('Confirm deletion of %d department','aiosc'),count($departments));
                else
                    printf(__('Confirm deletion of %d departments','aiosc'),count($departments));
            }
            elseif($action == 'activate') {
                if(count($departments) == 1)
                    printf(__('Confirm activation of %d department','aiosc'),count($departments));
                else
                    printf(__('Confirm activation of %d departments','aiosc'),count($departments));
            }
            elseif($action == 'deactivate') {
                if(count($departments) == 1)
                    printf(__('Confirm deactivation of %d department','aiosc'),count($departments));
                else
                    printf(__('Confirm deactivation of %d departments','aiosc'),count($departments));
            }
            ?>
        </li>
    </ul>
</div>
<table class="form-table">
    <tbody>
    <tr>
        <th colspan="2"><?php
            if($action == 'delete')
                _e('Departments to be deleted:','aiosc');
            elseif($action == 'activate')
                _e('Departments to be activated:','aiosc');
            else
                _e('Departments to be deactivated:','aiosc');
            ?></th>
    </tr>
    <tr class="deps-for-deletion">
        <td colspan="2">
            <div class="aiosc-ops-list aiosc-deps-list">
                <ul>
                    <?php
                    $dep_ids = array();
                    foreach($departments as $dep_id) :
                        $dep = new aiosc_Department($dep_id);
                        if(aiosc_is_department($dep)) :
                            ?>
                            <li><label><input type="checkbox" name="departments[]" value="<?php echo $dep->ID ?>" checked />
                                    <strong><?php echo $dep->name; ?></strong>
                                    <small><em>(<?php printf(__('Tickets: %d','aiosc'),$dep->ticket_count())?>)</em>,
                                        <em>(<?php printf(__('Operators: %d','aiosc'),$dep->op_count())?>)</em></small>
                                </label></li>
                        <?php
                        $dep_ids[] = $dep_id;
                        endif;
                    endforeach;
                    ?>
                </ul>
            </div>
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
    <?php
    if($action == 'delete') :
    ?>
    <tr>
        <th colspan="2"><?php _e('What would you like to do with tickets assigned to this department:','aiosc'); ?></th>
    </tr>
    <tr class="deps-for-deletion">
        <td colspan="2">
            <div class="aiosc-ops-list aiosc-deps-list">

                <?php
                $free_deps = aiosc_DepartmentManager::get_departments(false,true);
                ?>
                <select name="new_department">
                    <option value="0"><?php _e('- Delete all tickets -','aiosc')?></option>
                    <?php foreach($free_deps as $new_dep) :
                        if(!in_array($new_dep->ID, $dep_ids)) : ?>
                    <option value="<?php echo $new_dep->ID?>"><?php printf(__('Move to: %s','aiosc'),$new_dep->name); ?></option>
                    <?php endif; endforeach; ?>
                </select>
            </div>
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
    <?php endif; ?>
    <tr>
        <th>&nbsp;</th>
        <td>
            <input type="submit" class="button button-primary" id="aiosc-form-submit" value="<?php if($action == 'delete') _e('Confirm Deletion','aiosc'); elseif($action == 'deactivate') _e('Confirm Deactivation','aiosc'); else _e('Confirm Activation','aiosc'); ?>" />
            <button type="button" class="button" onClick="javascript:click_first_subtab()"><?php _e('Discard','aiosc')?></button>
        </td>
    </tr>
    </tbody>
</table>