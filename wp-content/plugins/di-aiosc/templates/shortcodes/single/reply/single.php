<?php
global $aiosc_settings, $aiosc_user, $ticket, $reply, $can_edit_replies;
$r_author = $reply->author_id == $aiosc_user?$aiosc_user:new aiosc_User($reply->author_id);
$r_author_url = aiosc_get_user_url($r_author->ID);

if(!empty($r_author_url)) $r_author_url = "<a href='$r_author_url' target='_blank'>".$r_author->display_name."</a>";
else $r_author_url = $r_author->display_name;

$date = date_i18n(get_option('date_format'),strtotime($reply->date_created));
$time = date_i18n(get_option('time_format'),strtotime($reply->date_created));
$time_ago = aiosc_time_ago($reply->date_created);

?>
<div id="aiosc-reply-<?php echo $reply->ID?>" class="aiosc-reply <?php echo $reply->is_staff_reply?"aiosc-staff-reply":""?>">
    <div class="aiosc-reply-avatar">
        <?php echo get_avatar($r_author->ID, 46); ?>
        <?php if($aiosc_settings->get('enable_staff_ribbon') && $reply->is_staff_reply) : ?>
            <div class="aiosc-staff-ribbon" title="<?php _e('This reply is being posted by Staff member.','aiosc')?>"><?php _e('STAFF','aiosc')?></div>
        <?php endif; ?>
    </div>
    <div class="aiosc-reply-main">
        <div class="aiosc-reply-head">
            <?php printf(__('Posted by %s, %s - <em>%s at %s</em>','aiosc'),$r_author_url, $time_ago, $date, $time); ?>

        </div>
        <div class="aiosc-reply-content" id="aiosc-reply-content-<?php echo $reply->ID?>">
            <?php echo $reply->content; ?>
        </div>
        <?php if(!empty($reply->attachment_ids)) : ?>
            <div class="aiosc-reply-meta">
                <ul>
                    <li><strong><?php _e('Attachments','aiosc') ?>:</strong></li>
                    <?php foreach($reply->attachment_ids as $ratt_id) :
                        $att = new aiosc_Attachment($ratt_id);
                        ?>
                        <li><img src="<?php echo $att->get_icon_url()?>" />
                            <a href="<?php echo $att->get_download_url($reply)?>"><?php echo $att->get_short_name(20)?></a>
                            <strong>(<?php echo $att->get_file_size('kb')?>Kb)</strong> &nbsp; &nbsp;</li>

                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <div class="aiosc-separator"></div>
</div>