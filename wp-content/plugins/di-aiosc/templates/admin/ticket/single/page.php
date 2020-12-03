<?php
/**
 * @update 1.1 - added "Edit Author" feature
 */
?>
<?php aisoc_print_js_debug() ?>
<?php
global $aiosc_user, $aiosc_settings, $aiosc_capabilities;

global $ticket, $reply;
$ticket = new aiosc_Ticket(aiosc_pg('ticket_id'));
$edit_mode = aiosc_pg('edit_mode') == 1?true:false;
if(!aiosc_is_ticket($ticket) || (!$aiosc_user->can('view_ticket',array('ticket_id'=>$ticket))) || ($edit_mode && !$aiosc_user->can('edit_ticket',array('ticket_id'=>$ticket)))) {
    echo aiosc_load_template('admin/ticket/ticket-error.php');
}
else {
    $can_edit_replies = $aiosc_user->can('edit_ticket',array('ticket_id'=>$ticket));
    $author = new aiosc_User($ticket->author_id);
    $operator = new aiosc_User($ticket->op_id);
    $author_url = aiosc_get_user_url($author->ID);
    $operator_url = aiosc_get_user_url($operator->ID);
    $department = new aiosc_Department($ticket->department_id);
    $priority = new aiosc_Priority($ticket->priority_id);

    if($edit_mode) {

        //$available_authors = aiosc_UserManager::get_users_with_capability('create_ticket', true);

        $subject_textbox = "<input type='text' placeholder='".__('Subject','aiosc')."' name='subject' value='$ticket->subject' style='width: 100%' />";

        $priority_select = '<select name="priority">';
        foreach(aiosc_PriorityManager::get_priorities(true) as $obj) {
            $selected = $obj->ID == $priority->ID?'selected="selected"':'';
            $priority_select .= "<option $selected value='$obj->ID'>$obj->name</option>";
        }
        $selected = "";
        $priority_select .= "</select>";

        $department_select = '<select name="department" id="new_department">';
        foreach(aiosc_DepartmentManager::get_departments(true) as $obj) {
            $selected = $obj->ID == $department->ID?'selected="selected"':'';
            $department_select .= "<option $selected value='$obj->ID'>$obj->name</option>";
        }
        $selected = "";
        $department_select .= "</select>";


        $operator_select = '<select name="operator" id="new_operator" data-placeholder="'.__('Operator', 'aiosc').'">';
        foreach($department->get_ops(true) as $obj) {
            $selected = $obj->ID == $operator->ID?'selected="selected"':'';
            $operator_select .= "<option $selected value='$obj->ID'>".$obj->wpUser->display_name." (".$obj->wpUser->user_login.")</option>";
        }
        $selected = "";
        $operator_select .= "</select>";

        $author_select = '<select name="author" id="new_author" data-placeholder="'.__('Author', 'aiosc').'">';
        $author_select .= "<option value='$author->ID' selected>".$author->wpUser->display_name." (".$author->wpUser->user_login.")</option>";
        $author_select .= "</select>";

        $visibility_check = "<label><input type='checkbox' name='is_public' ".checked($ticket->is_public, true, false)."> ".__('Public','aiosc')."</label>";
    }
?>
    <div class="aiosc-popup-bg"></div>
    <?php if($can_edit_replies)
        echo aiosc_load_template('admin/ticket/single/popup/reply-edit.php'); ?>

    <?php if($aiosc_user->can('close_ticket',array('ticket_id'=>$ticket)))
        echo aiosc_load_template('admin/ticket/single/popup/close-ticket.php'); ?>

    <?php if($aiosc_user->can('request_ticket_closure',array('ticket_id'=>$ticket)))
        echo aiosc_load_template('admin/ticket/single/popup/request-closure.php'); ?>

    <?php if($aiosc_user->can('reopen_ticket',array('ticket_id'=>$ticket)))
        echo aiosc_load_template('admin/ticket/single/popup/reopen-ticket.php'); ?>

    <?php if($aiosc_user->can('edit_ticket',array('ticket_id'=>$ticket)))
        echo aiosc_load_template('admin/ticket/single/popup/reply-remove.php'); ?>


    <?php if($edit_mode) : ?>
        <div class="aiosc-edit-mode-notice"><p><?php _e('You are currently in <code>TICKET EDIT MODE</code> so you cannot post replies.','aiosc')?></p></div>
        <div id="aiosc-edit-mode-response" style="margin-left: 0; margin-right: 20px; margin-top: 20px;"></div>
        <form id="aiosc-ticket-edit-form" name="aiosc-ticket-edit-form" method="post" action="<?php echo admin_url('/admin-ajax.php')?>">
        <input type="hidden" name="action" value="aiosc_new_ticket" />
        <input type="hidden" name="ticket_id" value="<?php echo $ticket->ID?>" />
        <?php endif; ?>
<div class="wrap aiosc-grid-holder">
    <div class="aiosc-ticket-widgets">
        <a href="#" id="aiosc-sidebar-toggler"><?php _e('More Details','aiosc')?></a>
        <div class="aiosc-window">
            <h2 class="page-title"><?php _e('Details','aiosc') ?>
                <div class="aiosc-separator"></div></h2>
            <table class="aiosc-ticket-details-table">
                <tbody>
                <tr>
                    <td>
                        <span class="aiosc-ticket-id">#<?php echo $ticket->ID?></span>
                    </td>
                    <td>
                        <?php if($edit_mode && $ticket->status == 'closed') : ?>

                            <span class="aiosc-status aiosc-status-<?php echo $ticket->status?>">

                            <label><input type="checkbox" name="reopen" value="1" /> <?php echo $ticket->status_name?> - <?php _e('Re-Open?','aiosc')?> </label>
                        </span>

                        <?php else : ?>
                            <span class="aiosc-status aiosc-status-<?php echo $ticket->status?>">
                            <?php echo $ticket->status_name?>
                        </span>
                        <?php endif; ?>

                    </td>
                </tr>

                <tr><th><?php _e('Priority','aiosc')?>:</th>
                    <td>
                    <?php if(!$edit_mode) : ?><span class="aiosc-priority-badge" style="<?php echo $priority->get_color_style()?>">
                            <?php echo $priority->name?></span>
                    <?php else : ?>
                        <?php echo $priority_select ?>
                    <?php endif; ?>
                    </td>
                </tr>

                <tr><th><?php _e('Department','aiosc')?>:</th>
                    <td>
                        <?php if(!$edit_mode) : ?>
                            <?php echo $department->name?>
                        <?php else : ?>
                            <?php echo $department_select?>
                        <?php endif; ?></td>
                </tr>
                <tr><th><?php _e('Author','aiosc')?>:</th><td>
                        <?php if($edit_mode) : ?>
                            <?php echo $author_select; ?>
                        <?php else : ?>
                            <?php if(!empty($author_url)) : ?>
                                <a href="<?php echo $author_url?>" target="_blank" title="<?php _e('View profile','aiosc')?>"><?php echo $author->display_name; ?></a>
                            <?php else : ?>
                                <?php echo $author->display_name; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td></tr>
                <?php if($aiosc_user->can('staff')) : ?>
                <tr><th><?php _e('Operator','aiosc')?>:</th><td>
                        <?php if(!$edit_mode) : ?>
                            <?php if(!empty($operator_url)) : ?>
                                <a href="<?php echo $operator_url?>" target="_blank" title="<?php _e('View profile','aiosc')?>"><?php echo $operator->display_name; ?></a>
                            <?php else : ?>
                                <?php echo $operator->display_name; ?>
                            <?php endif; ?>

                        <?php else : ?>
                            <?php echo $operator_select; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>

                <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
                <tr><th><?php _e('Visibility','aiosc')?>:</th>
                    <td>
                        <?php if(!$edit_mode) : ?>
        <?php echo ($ticket->is_public)?__('Public','aiosc'):__('Private','aiosc')?>
        <?php else : ?>
        <?php echo $visibility_check ?>
        <?php endif; ?>
                    </td>
                </tr>
                <tr><th><?php _e('Date Created','aiosc')?>:</th><td>
                        <?php echo date_i18n(get_option('date_format'),strtotime($ticket->date_created)) ?>
                        <?php echo date_i18n(get_option('time_format'),strtotime($ticket->date_created))?></td></tr>
                <?php if(strtotime($ticket->date_open) > 0) : ?>
                    <tr><th><?php _e('Open since','aiosc')?>:</th><td>
                            <?php echo date_i18n(get_option('date_format'),strtotime($ticket->date_open)) ?>
                            <?php echo date_i18n(get_option('time_format'),strtotime($ticket->date_open))?></td></tr>
                <?php endif; ?>
                <?php if(strtotime($ticket->date_closed) > 0) : ?>
                    <tr><th><?php _e('Closed since','aiosc')?>:</th><td>
                            <?php echo date_i18n(get_option('date_format'),strtotime($ticket->date_closed)) ?>
                            <?php echo date_i18n(get_option('time_format'),strtotime($ticket->date_closed))?></td></tr>
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
            <div class="aiosc-window">
                <h2 class="page-title"><?php _e('Attachments','aiosc') ?>
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
                            <?php echo $attachment->get_short_name(12); ?> <strong>(<?php echo $attachment->get_file_size('kb')?> Kb)</strong></th>
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
        <?php if($aiosc_user->can('staff') ||
            $aiosc_user->can('request_ticket_closure',array('ticket_id'=>$ticket)) ||
            $aiosc_user->can('reopen_ticket', array('ticket_id'=>$ticket))) : ?>
            <div class="aiosc-window">
                <h2 class="page-title"><?php _e('Actions','aiosc') ?>
                    <div class="aiosc-separator"></div></h2>
                <?php if(!$edit_mode) : ?>
                    <?php if($aiosc_user->can('reopen_ticket',array('ticket_id'=>$ticket)) && $ticket->status == 'closed') : ?>
                        <button type="button" class="button button-primary" onclick="aiosc_reopen_ticket(<?php echo $ticket->ID?>, this)"><?php _e('Re-Open Ticket','aiosc')?></button>
                    <?php endif; ?>
                    <?php if($aiosc_user->can('request_ticket_closure',array('ticket_id'=>$ticket))) : ?>
                        <button type="button" class="button" onclick="aiosc_request_closure(<?php echo $ticket->ID?>)"><?php _e('Request Closure','aiosc')?></button>
                    <?php endif; ?>
                    <?php if($aiosc_user->can('edit_ticket',array('ticket_id'=>$ticket))) : ?>
                        <button type="button" class="button" onclick="window.location.href='<?php echo aiosc_get_page_ticket_preview($ticket,true)?>'"><?php _e('Edit Mode','aiosc')?></button>
                    <?php endif; ?>
                    <?php if($ticket->status != 'closed' && $aiosc_user->can('close_ticket',array('ticket_id'=>$ticket))) : ?>
                        <button type="button" class="button" onclick="aiosc_close_ticket(<?php echo $ticket->ID?>)">
                            <?php $ticket->closure_requested ? _e('Close (REQUESTED)','aiosc') : _e('Close','aiosc')?></button>
                    <?php endif; ?>
                <?php else : ?>
                    <button type="submit" class="button button-primary edit-button-submit"><?php _e('Save Edits','aiosc')?></button>
                    <button type="button" class="button" onclick="window.location.href='<?php echo aiosc_get_page_ticket_preview($ticket,false)?>'"><?php _e('Exit','aiosc')?></button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if($ticket->status == 'closed') : ?>
            <div class="aiosc-window">
                <h2 class="page-title"><?php _e('Closure Note','aiosc') ?>
                    <div class="aiosc-separator"></div></h2>
                    <?php echo $ticket->closure_note; ?>
            </div>
        <?php endif; ?>
        <?php do_action('aiosc_init_widgets', $ticket) ?>
    </div>
    <!-- MAIN -->
    <div class=" aiosc-ticket-main">
        <div class="aiosc-window">
            <h2 class="page-title">
                <?php if(!$edit_mode) : ?>
        <?php echo !empty($ticket->subject)?$ticket->subject:__('Untitled ticket','aiosc'); ?>
        <?php else : ?>
        <?php echo $subject_textbox ?>
        <?php endif; ?>
                <span><?php printf(__('Created by <a href="%s">%s</a> on %s at %s','aiosc'),aiosc_get_user_url($author->ID,'#'),
                        $author->display_name,
                        date_i18n(get_option('date_format'),strtotime($ticket->date_created)),
                        date_i18n(get_option('time_format'),strtotime($ticket->date_created))) ?></span></h2>
            <div class="aiosc-separator"></div>
                <?php if(!$edit_mode) : ?>
                    <div class="aiosc-ticket-content">
        <?php echo $ticket->content; ?>
            </div>

            <?php else : ?>
                <?php wp_editor($ticket->content,'aiosc-content',array(
                    "media_buttons"=>false,
                    "quicktags"=>false,
                    'textarea_rows'=>8,
                    "textarea_name"=>"content",
                    'mce' => array(
                        'mce_buttons' => 'code,italic,underline'
                    ),
                        "tinymce"=>array(
                            "forced_root_block"=>false,
                            "force_br_newlines"=>true,
                            "force_p_newlines"=>false
                        )
                ))?>
            <?php endif; ?>
        </div>
        <div class="aiosc-eot"><span><?php _e('End of content','aiosc')?></span></div>
        <div id="aiosc-reply-response" class="below-h2"></div>
        <!-- REPLIES -->
        <?php if($ticket->status != 'closed' && !$edit_mode && $aiosc_user->can('reply_ticket',array('ticket_id'=>$ticket))) : ?>
            <?php echo aiosc_load_template('admin/ticket/single/reply/form.php') ?>
        <?php endif; ?>
        <?php echo aiosc_load_template('admin/ticket/single/reply/list.php') ?>
        <!-- END of REPLIES -->
    </div>
    <!-- END of MAIN -->
</div>
    <?php if($edit_mode) : ?>
    </form>

    <script>
        jQuery(document).ready(function($) {
            $(document).on('change','#new_department',function(e) {
                var dep_id = $(this).val();
                $(".edit-button-submit").attr('disabled','disabled');
                $("#new_operator").attr('disabled','disabled');
                $.post(AIOSC_AJAX_URL, { action: 'aiosc_load_operator_list', department_id: dep_id }, function(data) {
                    $(".edit-button-submit").removeAttr('disabled');
                    $("#new_operator").removeAttr('disabled');
                    console.log(data);
                    var res = $.parseJSON(data);
                    $("#new_operator").html(res.data.html);
                })
            });
            var authors = $('#new_author');
            var operators = $('#new_operator');
            var select2_options = {
                language: {
                    noResults: function() {
                        return "<?php _e('No users found', 'aiosc')?>"
                    },
                    inputTooShort: function(args) {
                        var remainingChars = args.minimum - args.input.length;
                        return "<?php _e('Enter %d more characters.', 'aiosc')?>".replace('%d', remainingChars);
                    }
                },
                escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                minimumInputLength: 3,
                templateResult: function (repo) {
                    if(repo.loading) return '<?php _e('Searching...', 'aiosc')?>';
                    return '<div class="clearfix">' + repo.name + ' (<em>'+repo.login+'</em>)</div>';
                },
                templateSelection: function (repo) {
                    if(typeof repo.name == "undefined")
                        return repo.text;

                    return repo.name + " (" + repo.login + ")";
                }
            };
            operators.select2({
                placeholder: operators.attr('data-placholder')
            });
            authors.select2($.extend({
                placeholder: authors.attr('data-placeholder'),
                ajax: {
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            action: 'aiosc_get_user_list',
                            authors: true
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            }, select2_options));
        })
    </script>
<?php endif; ?>
    <script>
        jQuery(document).ready(function($) {
            var currA = $("#adminmenu .wp-submenu li.current a");
            currA.attr('href',currA.attr('href')+'&ticket_id='+<?php echo $ticket->ID?>);
        });
    </script>
<?php
}
?>