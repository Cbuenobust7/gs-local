<?php
    global $aiosc_user, $aiosc_settings, $ticket;
?>
<div class="aiosc-window aiosc-reply-form">
    <?php if(!$ticket->awaiting_reply) : ?>
        <span id="aiosc-expand-reply-form"><?php _e('Click here to show reply form.', 'aiosc')?></span>
    <?php endif; ?>
    <div class="aiosc-reply-form-content" <?php if(!$ticket->awaiting_reply) : ?>style="display: none;"<?php endif; ?>>
        <h2 class="page-title page-title-sm"><?php _e('Post new Reply','aiosc') ?></h2>
        <div class="aiosc-separator"></div>
        <form id="aiosc-form" action="<?php echo get_admin_url()?>admin-ajax.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="aiosc_new_reply" />
            <input type="hidden" name="ticket_id" value="<?php echo aiosc_pg('ticket_id')?>" />
            <?php if($aiosc_user->can('staff')) :
                $premades = aiosc_PremadeResponseManager::get_responses(true);
                if(!empty($premades)) :
                    $my_premades = array();
                    $shared_premades = array();
                    foreach($premades as $pr) {
                        if($pr->author_id == $aiosc_user->ID) $my_premades[] = $pr;
                        else $shared_premades[] = $pr;
                    }
                    ?>
                    <div id="aiosc-premade-responses">
                        <select >
                            <option value="0"><?php _e('- Select Pre-Made Response -','aiosc')?></option>
                            <?php
                            if(!empty($my_premades)) : ?>
                                <optgroup label="<?php _e('My Responses','aiosc')?>">
                                    <?php
                                    foreach($my_premades as $premade) : ?>
                                        <option value="<?php echo $premade->ID?>"><?php echo $premade->name?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                            <?php
                            if(!empty($shared_premades)) : ?>
                                <optgroup label="<?php _e('Shared by others','aiosc')?>">
                                    <?php
                                    foreach($shared_premades as $premade) : ?>
                                        <option value="<?php echo $premade->ID?>"><?php echo $premade->name?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                        <button type="button" class="button"><?php _e('Insert','aiosc')?></button>
                    </div>
                <?php endif;
            endif; ?>
            <?php wp_editor('','aiosc-content',array(
                "media_buttons"=>false,
                "quicktags"=>$aiosc_user->can('answer_ticket',array('ticket_id'=>$ticket->ID)),
                'textarea_rows'=>4,
                "textarea_name"=>"content",
                "tinymce"=>array(
                    "forced_root_block"=>false,
                    "force_br_newlines"=>true,
                    "force_p_newlines"=>false
                )
            ))?>

            <?php if($aiosc_settings->get('allow_upload') || $aiosc_user->can('answer_ticket',array('ticket_id'=>$ticket->ID))) : ?>
                <div class="cholder">
                    <div class="aiosc-uploader aiosc-uploader-compact">
                        <ul class="aiosc-uploader-files">
                            <li class="aiosc-uploader-browse">
                                <i class="dashicons mainicon dashicons-welcome-add-page"></i>
                                       <span class="aiosc-uploader-file-name">
                                            <a href="#"><?php _e('Attach file', 'aiosc')?></a>
                                       </span>
                            </li>
                        </ul>
                    </div>
                    <div class="input-info">
                        <?php printf(__('Maximum number of attachments: <strong>%d</strong> | Maximum size per file: <strong>%s</strong> Kb','aiosc'),
                            $aiosc_settings->get('max_files_per_reply'), number_format($aiosc_settings->get('max_upload_size_per_file'),0))?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="aiosc-separator"></div>
            <div class="cholder">
                <input type="submit" value="<?php _e('Post Reply','aiosc')?>" class="button button-primary" />
            </div>
        </form>
    </div>
    <div class="aiosc-loading-holder"><div class="aiosc-loading-bar"><span><?php _e('Posting reply...','aiosc')?></span></div></div>
</div>
<script>
    jQuery(document).ready(function($) {
        aioscupload = new aioscUploader({
            ul: 'ul.aiosc-uploader-files',
            inputName: 'attachments[]',
            maxFileNameLen: 20,
            maxFiles: <?php echo $aiosc_settings->get('max_files_per_reply') ?>
        });
        $('#aiosc-premade-responses').appendTo($('#wp-aiosc-content-wrap .wp-editor-tools'));
        $(document).on('click','#aiosc-premade-responses button',function(e) {
            e.preventDefault();
            var sel = $(this).parent().find('select');
            if(sel.val() > 0) {
                aiosc_insert_premade_response(sel, $(this));
            }
        })
    });
</script>