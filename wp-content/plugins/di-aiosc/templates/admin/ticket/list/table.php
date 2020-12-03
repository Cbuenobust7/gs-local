<?php
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;


$items_per_page = aiosc_tickets_per_page();
$current_page = aiosc_pg('paged',false,1) > 0 ? aiosc_pg('paged',false,1) : 1;


//sorting
$ticket_sorting = '';
if(in_array(aiosc_pg('sort'),aiosc_TicketManager::get_columns())) $ticket_sorting = 'ORDER BY '.esc_sql(aiosc_pg('sort')).' ';
if(!empty($ticket_sorting)) $ticket_sorting .= strtolower(aiosc_pg('order')) == 'desc'?'DESC':'ASC';

global $wpdb;
$count_query = "SELECT COUNT(*) FROM `".aiosc_get_table(aiosc_tables::tickets)."` ";
if(aiosc_pg('is_public') != 'Y' && !$aiosc_user->can('staff')) $count_query .= " WHERE author_id=$aiosc_user->ID ";
$ticket_count = $wpdb->get_var( $count_query . aiosc_get_query(aiosc_pg('is_public') == 'Y' || $aiosc_user->can('staff')));
//aiosc_TicketManager::get_count_by($ticket_query, $additional_query);

//get LIMIT (from where to start)
$ticket_limit = ($current_page - 1) * $items_per_page;
if($ticket_limit < 0) $ticket_limit = 0;

$ticket_sorting .= " LIMIT $ticket_limit, $items_per_page";
$query = 'SELECT * FROM `'.aiosc_get_table(aiosc_tables::tickets).'`';
if(aiosc_pg('is_public') != 'Y' && !$aiosc_user->can('staff')) $query .= " WHERE author_id=$aiosc_user->ID ";
$tickets = aiosc_TicketManager::get_tickets($query . aiosc_get_query(aiosc_pg('is_public') == 'Y' || $aiosc_user->can('staff')) . $ticket_sorting);

?>
<div class="tablenav">
    <?php echo aiosc_get_pagination($ticket_count,$items_per_page, $current_page) ?>
</div>
<table class="wp-list-table widefat">
    <thead>
    <tr>
        <th scope="col" id="cb" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select All','aiosc')?></label>
            <input id="cb-select-all-1" type="checkbox">
        </th>
        <?php if(!aiosc_cookie_get('id-hide',false)) : ?>
            <th scope="col" class="manage-column column-ID sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>" style="width: 50px;">
                <a href="#" data-order="ID">
                    <span><?php _e('ID','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <th scope="col" class="manage-column column-subject sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
            <a href="#" data-order="subject">
                <span><?php _e('Subject','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <?php if(!aiosc_cookie_get('status-hide',false)) : ?>
            <th scope="col" class="manage-column column-status sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="status">
                    <span><?php _e('Status','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('replies-hide',false)) : ?>
            <th scope="col" class="manage-column num" style="width: 40px; text-align: center;">
                <div class="comment-grey-bubble" title="<?php _e('Replies','aiosc')?>"></div>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('priority-hide',false)) : ?>
            <th scope="col" class="manage-column column-priority sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="priority_level">
                    <span><?php _e('Priority','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('department-hide',false)) : ?>
            <th scope="col" class="manage-column column-department sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="department_id">
                    <span><?php _e('Department','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('author-hide',false) && $aiosc_user->can('staff')) : ?>
            <th scope="col" class="manage-column column-author sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="author_id">
                    <span><?php _e('Author','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('operator-hide',false) && $aiosc_user->can('staff')) : ?>
            <th scope="col" class="manage-column column-operator sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="op_id">
                    <span><?php _e('Operator','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('last_update-hide',false)) : ?>
            <th scope="col" class="manage-column column-last_update sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="last_update">
                    <span><?php _e('Last Update','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('date_created-hide',false)) : ?>
            <th scope="col" class="manage-column column-date_created sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="date_created">
                    <span><?php _e('Date Created','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <th scope="col" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-2"><?php _e('Select All','aiosc')?></label>
            <input id="cb-select-all-2" type="checkbox">
        </th>
        <?php if(!aiosc_cookie_get('id-hide',false)) : ?>
            <th scope="col" class="manage-column column-ID sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="ID">
                    <span><?php _e('ID','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <th scope="col" class="manage-column column-subject sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
            <a href="#" data-order="subject">
                <span><?php _e('Subject','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <?php if(!aiosc_cookie_get('status-hide',false)) : ?>
            <th scope="col" class="manage-column column-status sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="status">
                    <span><?php _e('Status','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('replies-hide',false)) : ?>
            <th scope="col" class="manage-column num" style="text-align: center;">
                <div class="comment-grey-bubble" title="<?php _e('Replies','aiosc')?>"></div>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('priority-hide',false)) : ?>
            <th scope="col" class="manage-column column-priority sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="priority_level">
                    <span><?php _e('Priority','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('department-hide',false)) : ?>
            <th scope="col" class="manage-column column-department sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="department_id">
                    <span><?php _e('Department','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('author-hide',false) && $aiosc_user->can('staff')) : ?>
            <th scope="col" class="manage-column column-author sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="author_id">
                    <span><?php _e('Author','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('operator-hide',false) && $aiosc_user->can('staff')) : ?>
            <th scope="col" class="manage-column column-operator sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="op_id">
                    <span><?php _e('Operator','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('last_update-hide',false)) : ?>
            <th scope="col" class="manage-column column-last_update sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="last_update">
                    <span><?php _e('Last Update','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
        <?php if(!aiosc_cookie_get('date_created-hide',false)) : ?>
            <th scope="col" class="manage-column column-date_created sortable <?php echo aiosc_pg('order') == 'desc'?'desc':'asc'?>">
                <a href="#" data-order="date_created">
                    <span><?php _e('Date Created','aiosc')?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php endif; ?>
    </tr>
    </tfoot>

    <tbody id="the-list">
    <?php if(count($tickets) > 0) :
        $y = 0;?>
        <?php
        foreach($tickets as $ticket) :
            $pri = new aiosc_Priority($ticket->priority_id);
            $dep = new aiosc_Department($ticket->department_id);
            $author = new aiosc_User($ticket->author_id);
            $author_url = aiosc_get_user_url($author->ID);
            $operator = new aiosc_User($ticket->op_id);
            $operator_url = aiosc_get_user_url($operator->ID);
            $delete_url = 'javascript:ticket_quick_delete('.$ticket->ID.')';
            $has_attachments = (is_array($ticket->attachment_ids) && count($ticket->attachment_ids) > 0);
            ?>
            <tr id="ticket-<?php echo $ticket->ID?>" <?php echo ($y % 2 == 0)?'class="alternate"':''?>>
                <th scope="row" class="check-column">
                    <label class="screen-reader-text" for="ticket_<?php echo $ticket->ID?>"><?php printf(__('Select %s','aiosc'),$ticket->subject)?></label>
                    <input type="checkbox" name="tickets[]" id="ticket_<?php echo $ticket->ID?>" class="subscriber" value="<?php echo $ticket->ID?>">
                </th>
                <?php if(!aiosc_cookie_get('id-hide',false)) : ?>
                    <td class="column-ID"><strong><?php echo $ticket->ID?></strong></td>
                <?php endif; ?>
                <td class="column-subject">
                    <strong>
                        <a class="row-title" href="<?php echo $ticket->get_url(false); ?>" title="<?php _e('View','aiosc')?> <?php echo $ticket->subject ?>"><?php echo $ticket->subject; ?></a>
                    </strong>
                    <div class="aiosc-ticket-row-controls">
                        <?php if($ticket->awaiting_reply && !aiosc_cookie_get('tag-awaiting_reply-hide',false)) : ?>
                            <span class="aiosc-label aiosc-label-reply"><?php _e('Awaiting staff reply', 'aiosc')?></span>
                        <?php endif; ?>
                        <?php if($ticket->closure_requested && !aiosc_cookie_get('tag-requested_closure-hide',false)) : ?>
                            <span class="aiosc-label aiosc-label-closure"><?php _e('Requested closure', 'aiosc')?></span>
                        <?php endif; ?>
                        <?php if($has_attachments && !aiosc_cookie_get('tag-attachments-hide',false)) : ?>
                            <span class="aiosc-attachment-icon" title="<?php _e('Has attachment(s)', 'aiosc')?>"><?php printf(__('Attachments: %s', 'aiosc'), count($ticket->attachment_ids))?></span>
                        <?php endif; ?>
                        <div class="row-actions">
                            <?php if($aiosc_user->can('edit_ticket',array('ticket_id'=>$ticket))) : ?>
                                <span class="edit">
                            <a href="<?php echo $ticket->get_url(true); ?>" title="<?php _e('Edit this ticket','aiosc')?>"><?php _e('Edit','aiosc')?></a> |
                        </span>
                            <?php endif; ?>
                            <?php if($aiosc_user->can('delete_ticket',array('ticket_id'=>$ticket))) : ?>
                                <span class="trash">
                        <a class="submitdelete" title="<?php _e('Delete permanently this ticket','aiosc')?>" href="<?php echo $delete_url; ?>"><?php _e('Delete','aiosc')?></a>
                    </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <?php if(!aiosc_cookie_get('status-hide',false)) : ?>
                    <td class="column-status">
                        <span class="aiosc-status aiosc-status-<?php echo strtolower($ticket->status)?>"><?php echo $ticket->status_name?></span>
                    </td>
                <?php endif; ?>
                <?php if(!aiosc_cookie_get('replies-hide',false)) : ?>
                    <td class="column-replies" style="text-align: center;">
                        <?php echo $ticket->reply_count()?>
                    </td>
                <?php endif; ?>
                <?php if(!aiosc_cookie_get('priority-hide',false)) : ?>
                    <td class="column-priority">
                        <span class="aiosc-priority-badge" style="<?php echo $pri->get_color_style()?>"><?php echo $pri->name?></span>
                    </td>
                <?php endif; ?>
                <?php if(!aiosc_cookie_get('department-hide',false)) : ?>
                    <td class="column-department">
                        <?php echo $dep->name?>
                    </td>
                <?php endif; ?>

                <?php if(!aiosc_cookie_get('author-hide',false) && $aiosc_user->can('staff')) : ?>
                    <td class="column-author">
                        <?php if($author_url != '') : ?>
                            <a href="<?php echo $author_url?>" target="_blank"><?php echo $author->display_name?></a>
                        <?php else : ?>
                            <?php echo $author->display_name?>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>

                <?php if(!aiosc_cookie_get('operator-hide',false) && $aiosc_user->can('staff')) : ?>
                    <td class="column-operator">
                        <?php if($operator_url != '') : ?>
                            <a href="<?php echo $operator_url?>" target="_blank"><?php echo $operator->display_name?></a>
                        <?php else : ?>
                            <?php echo $operator->display_name?>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>

                <?php if(!aiosc_cookie_get('last_update-hide',false)) : ?>
                    <td class="column-operator">
                        <?php echo aiosc_get_datetime($ticket->last_update, 'M d, Y h:i:s a') ?>
                    </td>
                <?php endif; ?>
                <?php if(!aiosc_cookie_get('date_created-hide',false)) : ?>
                    <td class="column-date">
                        <?php echo aiosc_get_datetime($ticket->date_created, 'M d, Y h:i:s a') ?>
                    </td>
                <?php endif; ?>
            </tr>
            <?php
            $y++;
        endforeach; ?>
    <?php else : ?>
        <tr><th scope="row" class="check-column">&nbsp;</th><td colspan=6><?php _e('No tickets found.','aiosc')?></td></tr>
    <?php endif; ?>
    </tbody>
</table>