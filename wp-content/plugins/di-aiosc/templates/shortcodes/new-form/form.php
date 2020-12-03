<?php aisoc_print_js_debug() ?>
<?php
/**
 * Template Name: AIOSC Ticket creation form
 */

global $aiosc_settings, $aiosc_user, $priorities, $departments;
?>
<div class="aiosc-window">
    <div class="aiosc-form-response"></div>
    <div class="aiosc-form">
        <form method="post" id="aiosc-form" action="<?php echo get_admin_url()?>admin-ajax.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="aiosc_new_ticket" />
            <input type="hidden" name="frontend" value="1" />
            <!-- Subject -->
            <div class="aiosc-cholder">
                <label for="aiosc-subject"><?php _e('Subject','aiosc')?></label>
                <input type="text" maxlength="255" style="width: 100%" id="aiosc-subject" name="subject" />
            </div>

            <!-- Content -->
            <div class="aiosc-cholder">
                <label for="aiosc-content"><?php _e('Description','aiosc')?></label>
                <?php wp_editor('','aiosc-content',array(
                        "media_buttons"=>false,
                        "quicktags"=>false,
                        'textarea_rows'=>8,
                        "textarea_name"=>"content",
                        "tinymce"=>array(
                            "forced_root_block"=>false,
                            "force_br_newlines"=>true,
                            "force_p_newlines"=>false
                        )
                    ))?>
            </div>

            <!-- Department -->
            <?php
            if($departments != false) :
                if(count($departments) > 1) : ?>
                    <div class="aiosc-cholder">
                        <label for="aiosc-department"><?php _e('Department','aiosc')?></label>
                        <select id="aiosc-department" name="department">
                                <option value=""><?php _e('Please select...','aiosc')?></option>
                                <?php foreach($departments as $dep) : ?>
                                    <option value="<?php echo $dep->ID?>"><?php echo $dep->name?></option>
                                <?php endforeach; ?>
                            </select>
                    </div>
                <?php else : ?>
                    <input type="hidden" name="department" value="<?php echo $departments[0]->ID?>" />
                <?php endif; ?>
            <?php else : ?>
                <input type="hidden" name="department" value="0" />
            <?php endif; ?>

            <!-- Priority -->
            <?php
            if($priorities != false) :
                if(count($priorities) > 1) : ?>
                    <div class="aiosc-cholder">
                        <label for="aiosc-priority"><?php _e('Priority','aiosc')?></label>
                        <select id="aiosc-priority" name="priority">
                                <option value=""><?php _e('Please select...','aiosc')?></option>
                                <?php foreach($priorities as $pri) : ?>
                                    <option value="<?php echo $pri->ID?>"><?php echo $pri->name?></option>
                                <?php endforeach; ?>
                            </select>
                    </div>
                <?php else : ?>
                    <input type="hidden" name="priority" value="<?php echo $priorities[0]->ID?>" />
                <?php endif; ?>
            <?php else : ?>
                <input type="hidden" name="priority" value="0" />
            <?php endif; ?>

            <!-- Addon Fields - EXTREMELY IMPORTANT -->
            <?php echo do_action('aiosc_custom_form_fields_front') ?>

            <!-- Visibility -->
            <?php if(aiosc_get_settings('enable_public_tickets')) : ?>
                <div class="aiosc-cholder">
                        <label for="aiosc-public"><?php _e('Public ticket?','aiosc')?></label>
                        <label><input type="checkbox" id="aiosc-public" name="is_public" /> <?php _e('Yes, allow others to see this ticket.','aiosc')?></label>
                </div>
            <?php endif; ?>

            <!-- Attachments -->
            <?php if(aiosc_get_settings('allow_upload') || $aiosc_user->can('answer_tickets')) : ?>
            <div class="aiosc-cholder">
                    <label for="aiosc-attach"><?php _e('Attach files','aiosc')?><br />
                        <small><?php printf(__('You can attach up to %s files per ticket.','aiosc'),aiosc_get_settings('max_files_per_ticket'))?></small>
                    </label>
                        <div class="aiosc-uploader">
                            <ul class="aiosc-uploader-files">
                                <li class="aiosc-uploader-browse">
                                    <i class="dashicons mainicon dashicons-welcome-add-page"></i>
                                       <span class="aiosc-uploader-file-name">
                                            <a href="#"><?php echo _x('+ New File', 'File Upload', 'aiosc')?></a>
                                       </span>
                                </li>
                            </ul>
                        </div>
                        <small class="input-info">
                            <?php printf(__('Maximum number of attachments: <strong>%d</strong> | Maximum size per file: <strong>%s</strong> Kb','aiosc'),
                                aiosc_get_settings('max_files_per_ticket'), number_format(aiosc_get_settings('max_upload_size_per_file'),0))?>
                        </small>
                </div>
            <?php endif; ?>

            <!-- Submit -->
            <div class="aiosc-cholder">
                <input type="submit" class="button button-primary" id="aiosc-form-submit" value="<?php _e('Submit','aiosc')?>" />
            </div>
        </form>
    </div>
</div>
