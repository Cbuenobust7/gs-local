<?php
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;

$action = @$_POST['action2']; //DELETE | ACTIVATE | DEACTIVATE
$priorities = @$_POST['priorities']; //ID of departments

?>
<input type="hidden" name="section" value="priorities-update" />
<input type="hidden" name="confirmation" value="<?php echo $action ?>" />
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li data-screen="priorities"><?php _e('List','aiosc')?></li>
        <li data-screen="priorities-new"><?php _e('Add New','aiosc')?></li>
        <li class="active">
            <?php
            if($action == 'delete') {
                if(count($priorities) == 1)
                    printf(__('Confirm deletion of %d priority','aiosc'),count($priorities));
                else
                    printf(__('Confirm deletion of %d priorities','aiosc'),count($priorities));
            }
            elseif($action == 'activate') {
                if(count($priorities) == 1)
                    printf(__('Confirm activation of %d priority','aiosc'),count($priorities));
                else
                    printf(__('Confirm activation of %d priorities','aiosc'),count($priorities));
            }
            elseif($action == 'deactivate') {
                if(count($priorities) == 1)
                    printf(__('Confirm deactivation of %d priority','aiosc'),count($priorities));
                else
                    printf(__('Confirm deactivation of %d priorities','aiosc'),count($priorities));
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
                _e('Priorities to be deleted:','aiosc');
            elseif($action == 'activate')
                _e('Priorities to be activated:','aiosc');
            else
                _e('Priorities to be deactivated:','aiosc');
            ?></th>
    </tr>
    <tr class="deps-for-deletion">
        <td colspan="2">
            <div class="aiosc-ops-list aiosc-deps-list">
                <ul>
                    <?php

                        foreach($priorities as $pri_id) :
                            $pri = new aiosc_Priority($pri_id);
                            if(aiosc_is_priority($pri)) :
                            ?>
                            <li><label><input type="checkbox" name="priorities[]" value="<?php echo $pri->ID ?>" checked />
                                    <strong><?php echo $pri->name; ?></strong>
                                        <small><em>(<?php printf(__('Tickets: %d','aiosc'),$pri->ticket_count())?>)</em>,
                                            <em>(<?php printf(__('Level: %d','aiosc'),$pri->level)?>)</em></small>
                                </label></li>
                    <?php
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
            <th colspan="2"><?php _e('What would you like to do with tickets that use this priority:','aiosc'); ?></th>
        </tr>
        <tr class="deps-for-deletion">
            <td colspan="2">
                <div class="aiosc-ops-list aiosc-deps-list">

                    <?php
                    $free_pris = aiosc_PriorityManager::get_priorities(false,true);
                    ?>
                    <select name="new_priority">
                        <option value="0"><?php _e('- Delete all tickets -','aiosc')?></option>
                        <?php foreach($free_pris as $new_pri) :
                            if(!in_array($new_pri->ID, $priorities)) : ?>
                                <option value="<?php echo $new_pri->ID?>"><?php printf(__('Move to: %s','aiosc'),$new_pri->name); ?></option>
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