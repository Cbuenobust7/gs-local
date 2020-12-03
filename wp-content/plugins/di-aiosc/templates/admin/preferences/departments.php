<?php
/**
 * Template file for Departments list in Preferences page
 */
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;
?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li class="active" data-screen="departments"><?php _e('List','aiosc')?></li>
        <li data-screen="departments-new"><?php _e('Add New','aiosc')?></li>
    </ul>
</div>
<div class="tablenav alignleft actions bulkactions department-actions">
    <select>
        <option value="" selected="selected"><?php _e('Bulk Actions','aiosc')?></option>
        <option value="activate"><?php _e('Activate','aiosc')?></option>
        <option value="deactivate"><?php _e('Deactivate','aiosc')?></option>
        <option value="delete"><?php _e('Delete','aiosc')?></option>
    </select>
    <input type="button" name="" id="doaction" class="button action" value="<?php _e('Apply','aiosc')?>">
</div>
<div class="aiosc-clear"></div>
<table class="wp-list-table widefat fixed plugins">
    <thead>
    <tr>
        <th scope="col" id="cb" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select All','aiosc')?></label>
            <input id="cb-select-all-1" type="checkbox">
        </th>
        <th scope="col" class="manage-column column-title"><?php _e('Departments','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Operators','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Tickets','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Date Created','aiosc')?></th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <th scope="col" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-2"><?php _e('Select All','aiosc')?></label>
            <input id="cb-select-all-2" type="checkbox">
        </th>
        <th scope="col" class="manage-column column-title"><?php _e('Departments','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Operators','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Tickets','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Date Created','aiosc')?></th>
    </tr>
    </tfoot>
    <tbody id="the-list">
    <?php $deps = aiosc_DepartmentManager::get_departments();
    if($deps) :
        foreach($deps as $k=>$dep) :
            $edit_url = 'javascript:switch_screen(\'departments-new\',{department_id: '.$dep->ID.'})';
            $delete_url = 'javascript:update_departments(\'delete\',['.$dep->ID.'])';
            $ticket_count = $dep->ticket_count();
            $tickets_url = aiosc_get_page_ticket_list(false,array('department'=>$dep->ID));
            ?>
        <tr class="<?php echo $dep->is_active?'active':''?>" id="row-<?php echo $dep->ID?>">
            <th scope="row" class="check-column">
                <label class="screen-reader-text" for="cb-select-<?php echo $dep->ID ?>"><?php printf(__('Select %s','aiosc'),$dep->name)?></label>
                <input id="cb-select-<?php echo $dep->ID ?>" type="checkbox" name="checked[]" value="<?php echo $dep->ID ?>">
            </th>
            <td class="column-title">
                <strong>
                    <a class="row-title" href="<?php echo $edit_url; ?>" title="<?php _e('Edit','aiosc')?> <?php echo $dep->name ?>"><?php echo $dep->name; ?></a>
                </strong>
                <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                <div class="row-actions">
                    <span class="edit">
                        <a href="<?php echo $edit_url; ?>" title="<?php _e('Edit this department','aiosc')?>"><?php _e('Edit','aiosc')?></a> |
                    </span>
                    <span class="trash">
                        <a class="submitdelete" title="<?php _e('Delete permanently this department','aiosc')?>" href="<?php echo $delete_url; ?>"><?php _e('Delete permanently','aiosc')?></a>
                    </span>
                </div>
            </td>
            <td><?php echo $dep->op_count()?></td>
            <td><?php if($ticket_count > 0) : ?><a href="<?php echo $tickets_url?>" title='<?php printf(__('View all tickets from &quot;%s&quot; department.','aiosc'),$dep->name)?>'><?php echo $ticket_count; ?></a><?php else : ?>0<?php endif; ?></td>
            <td><?php echo date('Y-m-d H:i:s',strtotime($dep->date_created))?></td>
        </tr>
    <?php endforeach;
        else : ?>
    <tr><td colspan="5"><?php _e('No departments found.','aiosc')?></td></tr>
    <?php endif; ?>
    </tbody>
</table>