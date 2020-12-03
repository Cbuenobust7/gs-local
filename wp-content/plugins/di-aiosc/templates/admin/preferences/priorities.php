<?php
/**
 * Template file for Priority list in Preferences page
 */
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;
?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li class="active" data-screen="priorities"><?php _e('List','aiosc')?></li>
        <li data-screen="priorities-new"><?php _e('Add New','aiosc')?></li>
    </ul>
</div>
<div class="tablenav alignleft actions bulkactions priority-actions">
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
        <th scope="col" class="manage-column column-title"><?php _e('Priorities','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Level','aiosc')?></th>
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
        <th scope="col" class="manage-column column-title"><?php _e('Priorities','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Level','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Tickets','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Date Created','aiosc')?></th>
    </tr>
    </tfoot>
    <tbody id="the-list">
    <?php $pris = aiosc_PriorityManager::get_priorities();
    if($pris) :
        foreach($pris as $k=>$pri) :
            $edit_url = 'javascript:switch_screen(\'priorities-new\',{priority_id: '.$pri->ID.'})';
            $delete_url = 'javascript:update_priorities(\'delete\',['.$pri->ID.'])';
            $ticket_count = $pri->ticket_count();
            $tickets_url = aiosc_get_page_ticket_list(false,array('priority'=>$pri->ID));
            $color_style = (!empty($pri->color))?'background-color: '.$pri->color.';':'';
            if(!empty($color_style)) {
                $color_style .= aiosc_get_brightness($pri->color) > 180?' color: black;':' color: white;';
            }
            ?>
        <tr class="<?php echo $pri->is_active?'active':''?>" id="row-<?php echo $pri->ID?>">
            <th scope="row" class="check-column">
                <label class="screen-reader-text" for="cb-select-<?php echo $pri->ID ?>"><?php printf(__('Select %s','aiosc'),$pri->name)?></label>
                <input id="cb-select-<?php echo $pri->ID ?>" type="checkbox" name="checked[]" value="<?php echo $pri->ID ?>">
            </th>
            <td class="column-title">
                <strong>
                    <a class="row-title" href="<?php echo $edit_url; ?>" title="<?php _e('Edit','aiosc')?> <?php echo $pri->name ?>"><?php echo $pri->name; ?></a>
                </strong>
                <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                <div class="row-actions">
                    <span class="edit">
                        <a href="<?php echo $edit_url; ?>" title="<?php _e('Edit this priority','aiosc')?>"><?php _e('Edit','aiosc')?></a> |
                    </span>
                    <span class="trash">
                        <a class="submitdelete" title="<?php _e('Delete permanently this priority','aiosc')?>" href="<?php echo $delete_url; ?>"><?php _e('Delete permanently','aiosc')?></a>
                    </span>
                </div>
            </td>
            <td><span class="aiosc-priority-badge" style="<?php echo $color_style ?>"><?php echo $pri->level ?></span></td>
            <td><?php if($ticket_count > 0) : ?><a href="<?php echo $tickets_url?>" title='<?php printf(__('View all tickets with &quot;%s&quot; priority.','aiosc'),$pri->name)?>'><?php echo $ticket_count; ?></a><?php else : ?>0<?php endif; ?></td>
            <td><?php echo date('Y-m-d H:i:s',strtotime($pri->date_created))?></td>
        </tr>
    <?php endforeach;
        else : ?>
    <tr><td colspan="5"><?php _e('No priorities found.','aiosc')?></td></tr>
    <?php endif; ?>
    </tbody>
</table>