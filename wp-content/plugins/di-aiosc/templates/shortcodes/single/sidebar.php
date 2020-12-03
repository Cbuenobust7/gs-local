<?php
global  $aiosc_user, //contains aiosc_User instance of current user
        $aiosc_settings, //can be used for retrieving AIOSC settings (->get)
        $ticket, //contains aiosc_Ticket instance of queried ticket and all data is in here
        $priority, //contains aiosc_Priority instance of ticket's priority object
        $department, //contains aiosc_Department instance of ticket's department
        $author, //contains aiosc_User instance of ticket's author
        $operator, //contains aiosc_User instance of ticket's operator
        $replies; //contains array of aiosc_Reply instances, all ticket replies are here
?>
<div class="aiosc-ticket-sidebar">
    <div class="aiosc-window">
        <h2 class="aiosc-title"><?php _e('Details','aiosc') ?>
            <div class="aiosc-separator"></div></h2>
        <table class="aiosc-ticket-details-table">
            <tbody>
            <tr><td><span class="aiosc-ticket-id">#<?php echo $ticket->ID?></span></td>
                <td>
                    <span class="aiosc-status aiosc-status-<?php echo $ticket->status?>">
                        <?php echo $ticket->status_name?>
                    </span>
                </td>
            </tr>

            <tr><th><?php _e('Priority','aiosc')?>:</th>
                <td>
                    <span class="aiosc-priority-badge" style="<?php echo $priority->get_color_style()?>">
                        <?php echo $priority->name?>
                    </span>
                </td>
            </tr>

            <tr><th><?php _e('Department','aiosc')?>:</th>
                <td><?php echo $department->name?></td>
            </tr>
            <tr>
                <th><?php _e('Author','aiosc')?>:</th>
                <td>
                    <?php if(aiosc_get_user_url($author->ID) != '') : ?>
                        <a href="<?php echo aiosc_get_user_url($author->ID)?>" target="_blank" title="<?php _e('View profile','aiosc')?>"><?php echo $author->display_name; ?></a>
                    <?php else : ?>
                        <?php echo $author->display_name; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if($aiosc_user->can('staff')) : ?>
                <tr><th><?php _e('Operator','aiosc')?>:</th>
                    <td><?php if(aiosc_get_user_url($operator->ID) != '') : ?>
                            <a href="<?php echo aiosc_get_user_url($operator->ID)?>" target="_blank" title="<?php _e('View profile','aiosc')?>"><?php echo $operator->display_name; ?></a>
                        <?php else : ?>
                            <?php echo $operator->display_name; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="2"><div class="aiosc-separator"></div></td>
            </tr>
            <tr>
                <th><?php _e('Visibility','aiosc')?>:</th>
                <td><?php echo ($ticket->is_public)?__('Public','aiosc'):__('Private','aiosc')?></td>
            </tr>
            <tr>
                <th><?php _e('Date Created','aiosc')?>:</th>
                <td><?php echo aiosc_get_datetime(strtotime($ticket->date_created))?></td>
            </tr>
            <?php if(strtotime($ticket->date_open) > 0) : ?>
                <tr>
                    <th><?php _e('Open since','aiosc')?>:</th>
                    <td><?php echo aiosc_get_datetime(strtotime($ticket->date_open))?></td>
                </tr>
            <?php endif; ?>
            <?php if(strtotime($ticket->date_closed) > 0) : ?>
                <tr>
                    <th><?php _e('Closed since','aiosc')?>:</th>
                    <td><?php echo aiosc_get_datetime(strtotime($ticket->date_closed))?></td>
                </tr>
            <?php endif; ?>
            <?php if(strtotime($ticket->last_update) > 0) : ?>
                <tr><th><?php _e('Last Update','aiosc')?>:</th><td>
                        <?php echo date_i18n(get_option('date_format'),strtotime($ticket->last_update)) ?>
                        <?php echo date_i18n(get_option('time_format'),strtotime($ticket->last_update))?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if(is_array($ticket->attachment_ids) && $aiosc_user->can('reply_ticket',array('ticket_id'=>$ticket))) : ?>
        <!-- Attachments -->
        <div class="aiosc-window">
            <h2 class="aiosc-title"><?php _e('Attachments','aiosc') ?>
                <div class="aiosc-separator"></div></h2>
            <table class="aiosc-attachment-table">
                <tbody>
                <?php
                $total_size = 0;
                foreach($ticket->attachment_ids as $att_id) :
                    $attachment = new aiosc_Attachment($att_id);
                    $total_size += $attachment->get_file_size('b',false);
                    ?>
                    <tr>
                        <th title="<?php echo $attachment->file_name; ?>">
                            <img src="<?php echo $attachment->get_icon_url()?>" />
                            <?php echo $attachment->get_short_name(10); ?> <strong>(<?php echo $attachment->get_file_size('kb')?> Kb)</strong></th>
                        <td>
                            <?php if($aiosc_user->can('download_file',array('ticket_id'=>$ticket, 'file_id'=>$attachment))) : ?>
                                <a href="<?php echo $attachment->get_download_url($ticket)?>"><?php _e('Download','aiosc'); ?></a>
                            <?php else : ?>
                                &nbsp;
                            <?php endif; ?>
                        </td></tr>
                <?php endforeach; ?>
                <tr><td colspan=2>&nbsp;</tr>
                <tr><td colspan=2><div class="aiosc-separator"></div></tr>
                <tr><td colspan=2 style="text-align: center"><?php printf(__('There are %d files attached with total size of %s Mb','aiosc'),count($ticket->attachment_ids),number_format($total_size / 1024 / 1024,2))?></tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php
    /* TICKET META DISPLAY IS DEPRECATED! USE WIDGETS INSTEAD */
    /* if(is_array($ticket->ticket_meta) && $aiosc_user->can('answer_ticket',array('ticket_id'=>$ticket))) : ?>
        <!-- Ticket Meta -->
        <div class="aiosc-window">
            <h2 class="aiosc-title"><?php _e('Meta','aiosc') ?>
                <div class="aiosc-separator"></div></h2>
            <table class="aiosc-ticket-details-table">
                <tbody>
                <?php foreach($ticket->ticket_meta as $k=>$v) :
                    if(!isset($v['hidden'])) : ?>
                    <tr>
                        <th><?php echo isset($v['name'])?$v['name']:$k?></th>
                        <td><?php echo isset($v['value'])?$v['value']:$v; ?></td>
                    </tr>
                <?php endif; endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; */ ?>

    <?php if($aiosc_user->can('staff') || $aiosc_user->can('request_ticket_closure',array('ticket_id'=>$ticket)) || $aiosc_user->can('reopen_ticket',array('ticket_id'=>$ticket))) : ?>
        <!-- Actions -->
        <div class="aiosc-window">
            <h2 class="aiosc-title"><?php _e('Actions','aiosc') ?>
                <div class="aiosc-separator"></div>
            </h2>
            <?php if($aiosc_user->can('reopen_ticket',array('ticket_id'=>$ticket)) && $ticket->status == 'closed') : ?>
                <button type="button" class="button button-primary" onclick="aiosc_reopen_ticket(<?php echo $ticket->ID?>, this)"><?php _e('Re-Open Ticket','aiosc')?></button>
            <?php endif; ?>
                <?php if($aiosc_user->can('edit_ticket',array('ticket_id'=>$ticket))) : ?>
                    <button type="button" class="button" onclick="window.location.href='<?php echo aiosc_get_page_ticket_preview($ticket,true,false)?>'"><?php _e('Edit Mode','aiosc')?></button>
                <?php endif; ?>
                <?php if($aiosc_user->can('request_ticket_closure',array('ticket_id'=>$ticket))) : ?>
                    <button type="button" class="button" onclick="aiosc_request_closure(<?php echo $ticket->ID?>)"><?php _e('Request Closure','aiosc')?></button>
                <?php endif; ?>
                <?php if($ticket->status != 'closed' && $aiosc_user->can('close_ticket',array('ticket_id'=>$ticket))) : ?>
                    <button type="button" class="button" onclick="aiosc_close_ticket(<?php echo $ticket->ID?>)">
                        <?php $ticket->closure_requested ? _e('Close (REQUESTED)','aiosc') : _e('Close','aiosc')?></button>
                <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if($ticket->status == 'closed') : ?>
        <!-- Closure Note -->
        <div class="aiosc-window">
            <h2 class="aiosc-title"><?php _e('Closure Note','aiosc') ?>
                <div class="aiosc-separator"></div></h2>
            <?php echo $ticket->closure_note;  ?>
        </div>
    <?php endif; ?>
    <?php do_action('aiosc_init_widgets', $ticket) ?>
</div>