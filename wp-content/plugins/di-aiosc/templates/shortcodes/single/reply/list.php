<?php
global $aiosc_user, $aiosc_settings, $ticket, $author, $operator, $replies;

$reply_limit = apply_filters('aiosc_reply_limit',3);
if(is_array($replies)) {
    $last_reply_by = new aiosc_User($replies[0]->author_id);
    $last_reply_by_url = aiosc_get_user_url($last_reply_by->ID);
    if(!empty($last_reply_by_url)) $last_reply_by = '<a href="'.$last_reply_by_url.'" target="_blank">'.$last_reply_by->display_name."</a>";
    else $last_reply_by = $last_reply_by->display_name;
}
$total_replies = aiosc_ReplyManager::get_count_by(array('ticket_id'=>$ticket->ID));
?>
<div class="aiosc-window aiosc-replies">
    <h2 class="aiosc-title aiosc-title-sm"><?php _e('Replies','aiosc') ?>
        <span>
            <?php if($total_replies > 0)
                printf(_n('There is %s reply posted on this ticket and last reply was posted by %s.',
                        'There are %s replies posted on this ticket and last reply was posted by %s.',
                        $total_replies,'aiosc'),
                    $total_replies, $last_reply_by);
            else _e('There are no replies yet.','aiosc'); ?>
        </span>
        <div class="aiosc-separator"></div>
    </h2>
    <?php
    if(!empty($replies) && $replies !== false) :
        global $reply;
        foreach($replies as $r) :
            $reply = $r;
            echo aiosc_load_template('shortcodes/single/reply/single.php', true);
        endforeach;
    ?>
        <div id="aiosc-replies-load-more">
            <div class="aiosc-reply-loading aiosc-loading-bar"></div>
            <a href="javascript:aiosc_load_replies(<?php echo $ticket->ID?>)"><?php _e('Load More','aiosc')?></a>
        </div>
        <div id="aiosc-replies-no-more"><?php _e('No more replies','aiosc')?></div>
    <?php
    else : ?>
        <div class="aiosc-no-replies-found"><?php _e('No replies posted yet.','aiosc')?></div>
    <?php endif; ?>
</div>
<script>
    var aiosc_replies_loaded = <?php echo $reply_limit; ?>
</script>