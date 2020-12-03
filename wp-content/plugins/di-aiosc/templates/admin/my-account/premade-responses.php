<?php
/**
 * Template file for Priority list in Preferences page
 */
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;
?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li class="active" data-screen="premade-responses"><?php _e('List','aiosc')?></li>
        <li data-screen="premade-responses-new"><?php _e('Add New','aiosc')?></li>
    </ul>
</div>
<div class="tablenav alignleft actions bulkactions premade-response-actions">
    <select>
        <option value="" selected="selected"><?php _e('Bulk Actions','aiosc')?></option>
        <option value="public"><?php _e('Share','aiosc')?></option>
        <option value="private"><?php _e('Make Private','aiosc')?></option>
        <option value="delete"><?php _e('Delete','aiosc')?></option>
    </select>
    <input type="button" name="" id="doaction" class="button action" value="<?php _e('Apply','aiosc')?>">
</div>
<div class="aiosc-clear"></div>
<table class="wp-list-table widefat fixed">
    <thead>
    <tr>
        <th scope="col" id="cb" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select All','aiosc')?></label>
            <input id="cb-select-all-1" type="checkbox">
        </th>
        <th scope="col" class="manage-column column-title"><?php _e('Pre-Made Responses','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Is Shared?','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Date Created','aiosc')?></th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <th scope="col" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-2"><?php _e('Select All','aiosc')?></label>
            <input id="cb-select-all-2" type="checkbox">
        </th>
        <th scope="col" class="manage-column column-title"><?php _e('Pre-Made Responses','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Is Shared?','aiosc')?></th>
        <th scope="col" class="manage-column"><?php _e('Date Created','aiosc')?></th>
    </tr>
    </tfoot>
    <tbody id="the-list">
    <?php $pris = aiosc_PremadeResponseManager::get_responses(false);
    if($pris) :
        $y = 0;
        foreach($pris as $k=>$pri) :
            $edit_url = 'javascript:switch_screen(\'premade-responses-new\',{response_id: '.$pri->ID.'})';
            $delete_url = 'javascript:update_responses(\'delete\',['.$pri->ID.'])';

            ?>
            <tr class="<?php echo ($y % 2 == 0)?'alternate':''?>" id="row-<?php echo $pri->ID?>">
                <th scope="row" class="check-column">
                    <label class="screen-reader-text" for="cb-select-<?php echo $pri->ID ?>"><?php printf(__('Select %s','aiosc'),$pri->name)?></label>
                    <input id="cb-select-<?php echo $pri->ID ?>" type="checkbox" name="responses[]" value="<?php echo $pri->ID ?>">
                </th>
                <td class="column-title">
                    <strong>
                        <a class="row-title" href="<?php echo $edit_url; ?>" title="<?php _e('Edit','aiosc')?> <?php echo $pri->name ?>"><?php echo $pri->name; ?></a>
                    </strong>
                    <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                    <div class="row-actions">
                    <span class="edit">
                        <a href="<?php echo $edit_url; ?>" title="<?php _e('Edit this response','aiosc')?>"><?php _e('Edit','aiosc')?></a> |
                    </span>
                    <span class="trash">
                        <a class="submitdelete" title="<?php _e('Delete permanently this response','aiosc')?>" href="<?php echo $delete_url; ?>"><?php _e('Delete permanently','aiosc')?></a>
                    </span>
                    </div>
                </td>
                <td><?php echo $pri->is_shared?__('Shared','aiosc'):__('Private','aiosc') ?></td>
                <td><?php echo date('Y-m-d H:i:s',strtotime($pri->date_created))?></td>
            </tr>
        <?php
        $y++;
        endforeach;
    else : ?>
        <tr><td colspan="4"><?php _e('No pre-made responses found.','aiosc')?></td></tr>
    <?php endif; ?>
    </tbody>
</table>