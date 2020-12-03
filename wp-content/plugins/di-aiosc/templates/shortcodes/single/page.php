<?php aisoc_print_js_debug() ?>
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
<div class="aiosc-popup-bg"></div>
<?php if($aiosc_user->can('close_ticket',array('ticket_id'=>$ticket)))
    echo aiosc_load_template('shortcodes/single/popup/close-ticket.php', true); ?>

<?php if($aiosc_user->can('request_ticket_closure',array('ticket_id'=>$ticket)))
    echo aiosc_load_template('shortcodes/single/popup/request-closure.php', true); ?>

<?php if($aiosc_user->can('reopen_ticket',array('ticket_id'=>$ticket)))
    echo aiosc_load_template('shortcodes/single/popup/reopen-ticket.php'); ?>

<div class="aiosc-ticket-preview">
    <?php echo aiosc_load_template('shortcodes/single/sidebar.php',true) ?>
    <div class="aiosc-ticket-main">
        <div class="aiosc-window">
            <!-- Title -->
            <h2 class="aiosc-title">
                <?php echo !empty($ticket->subject)?$ticket->subject:__('Untitled ticket','aiosc'); ?>
                <span>
                    <?php printf(__('Created by <a href="%s">%s</a> on %s','aiosc'),aiosc_get_user_url($author->id,'#'),
                        $author->display_name,
                        aiosc_get_datetime(strtotime($ticket->date_created))) ?>
                </span>
            </h2>
            <div class="aiosc-separator"></div>
            <!-- Content -->
            <div class="aiosc-ticket-content">
                <?php echo $ticket->content; ?>
            </div>
        </div>
        <div class="aiosc-eot"><span><?php _e('End of content','aiosc')?></span></div>
        <div class="aiosc-form-response"></div>
        <!-- REPLIES -->
        <?php
        if($ticket->status != 'closed' && $aiosc_user->can('reply_ticket',array('ticket_id'=>$ticket))) :
            echo aiosc_load_template('shortcodes/single/reply/form.php', true);
        endif;
        ?>
        <?php echo aiosc_load_template('shortcodes/single/reply/list.php', true) ?>
        <!-- END of REPLIES -->
    </div>
</div>
<script>
    var AIOSC_FRONTEND_ACCESS = true;
</script>