<?php
/**
 * Available Variables
 * $aiosc_user aiosc_User - current aiosc user
 * $aiosc_settings - can be used for retrieving AIOSC settings (->get or aiosc_settings_get())
 * $tickets array - contains array of aiosc_Ticket instances of queried tickets and all data is in here
 * $ticket_count int - ticket count of current query
 * $items_per_page int - how many items per page to show
 * $current_page int - current page index (for pagination)
 * $ticket_sorting string - current sorting order, (asc / desc)
 *
 * $_REQUEST['sort'] (column name), $_REQUEST['order'] (asc/desc)
 *
 * $publicOnly bool - is publicOnly enabled (true) or (false)
 */

?>
<div class="tablenav">
    <?php echo aiosc_get_pagination($ticket_count,$items_per_page, $current_page) ?>
</div>
<table class="aiosc-tickets-table">
    <thead>
    <tr>
        <th scope="col" class="manage-column column-ID sortable <?php echo $order?>" style="width: 40px;">
            <a href="#" data-order="ID">
                <span><?php _e('ID','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-subject sortable <?php echo $order?>">
            <a href="#" data-order="subject">
                <span><?php _e('Subject','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-status sortable <?php echo $order?>">
            <a href="#" data-order="status">
                <span><?php _e('Status','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-replies num">
            <div class="dashicons dashicons-format-chat" title="<?php _e('Replies','aiosc')?>"></div>
        </th>

        <th scope="col" class="manage-column column-priority sortable <?php echo $order?>">
            <a href="#" data-order="priority_level">
                <span><?php _e('Priority','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-department sortable <?php echo $order?>">
            <a href="#" data-order="department_id">
                <span><?php _e('Department','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-last_update sortable <?php echo $order?> aiosc-tr-right">
            <a href="#" data-order="last_update">
                <span><?php _e('Last Update','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-date_created sortable <?php echo $order?> aiosc-tr-right">
            <a href="#" data-order="date_created">
                <span><?php _e('Date Created','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <th scope="col" class="manage-column column-ID sortable <?php echo $order?>">
            <a href="#" data-order="ID">
                <span><?php _e('ID','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-subject sortable <?php echo $order?>">
            <a href="#" data-order="subject">
                <span><?php _e('Subject','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-status sortable <?php echo $order?>">
            <a href="#" data-order="status">
                <span><?php _e('Status','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-replies num">
            <div class="dashicons dashicons-format-chat" title="<?php _e('Replies','aiosc')?>"></div>
        </th>

        <th scope="col" class="manage-column column-priority sortable <?php echo $order?>">
            <a href="#" data-order="priority_level">
                <span><?php _e('Priority','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-department sortable <?php echo $order?>">
            <a href="#" data-order="department_id">
                <span><?php _e('Department','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-last_update sortable <?php echo $order?> aiosc-tr-right">
            <a href="#" data-order="last_update">
                <span><?php _e('Last Update','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
        <th scope="col" class="manage-column column-date_created sortable <?php echo $order?> aiosc-tr-right">
            <a href="#" data-order="date_created">
                <span><?php _e('Date Created','aiosc')?></span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
    </tr>
    </tfoot>

    <tbody id="the-list">
    <?php if(count($tickets) > 0) :
        $y = 0;?>
        <?php
        foreach($tickets as $ticket) :
            /** @var aiosc_Ticket $ticket */
            $pri = new aiosc_Priority($ticket->priority_id);
            $dep = new aiosc_Department($ticket->department_id);
            $has_attachments = (is_array($ticket->attachment_ids) && count($ticket->attachment_ids) > 0);
            ?>
            <tr id="ticket-<?php echo $ticket->ID?>" <?php echo ($y % 2 == 0)?'class="alternate"':''?>>
                <?php if(!aiosc_cookie_get('id-hide',false)) : ?>
                    <td class="column-ID">#<?php echo $ticket->ID?></td>
                <?php endif; ?>
                <td class="column-subject">
                    <strong>
                        <a class="row-title" href="<?php echo $ticket->get_url(false, true); ?>" title="<?php _e('View','aiosc')?> <?php echo $ticket->subject ?>"><?php echo $ticket->subject; ?></a>
                    </strong>
                    <div class="aiosc-ticket-row-controls">
                        <?php if($ticket->awaiting_reply) : ?>
                            <span class="aiosc-label aiosc-label-reply"><?php _e('Awaiting staff reply', 'aiosc')?></span>
                        <?php endif; ?>
                        <?php if($ticket->closure_requested) : ?>
                            <span class="aiosc-label aiosc-label-closure"><?php _e('Requested closure', 'aiosc')?></span>
                        <?php endif; ?>
                        <?php if($has_attachments) : ?>
                            <span class="aiosc-attachment-icon" title="<?php _e('Has attachment(s)', 'aiosc')?>"><?php printf(__('Attachments: %s', 'aiosc'), count($ticket->attachment_ids))?></span>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="column-status">
                    <span class="aiosc-status aiosc-status-<?php echo strtolower($ticket->status)?>"><?php echo $ticket->status_name?></span>
                </td>
                <td class="column-replies">
                    <?php echo $ticket->reply_count()?>
                </td>
                <td class="column-priority">
                    <span class="aiosc-priority-badge" style="<?php echo $pri->get_color_style()?>"><?php echo $pri->name?></span>
                </td>
                <td class="column-department">
                    <?php echo $dep->name?>
                </td>
                <td class="column-last_update">
                    <?php echo aiosc_get_datetime($ticket->last_update) ?>
                </td>
                <td class="column-date_created">
                    <?php echo aiosc_get_datetime($ticket->date_created) ?>
                </td>
            </tr>
            <?php
            $y++;
        endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan=8 class="aiosc-no-items"><?php _e('No tickets found.','aiosc')?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>