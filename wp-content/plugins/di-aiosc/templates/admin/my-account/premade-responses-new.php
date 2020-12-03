<?php
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;

if(isset($_POST['response_id']) && is_numeric($_POST['response_id']) && $_POST['response_id'] > 0)
    $response = new aiosc_PremadeResponse($_POST['response_id']);
else
    $response = false;
?>
<input type="hidden" name="section" value="<?php echo !aiosc_is_premade_response($response)?'premade-responses-new':'premade-responses-edit'?>" />
<?php if(aiosc_is_premade_response($response)) : ?>
<input type="hidden" name="response_id" value="<?php echo $response->ID ?>" />
<?php endif; ?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li data-screen="premade-responses"><?php _e('List','aiosc')?></li>
        <li <?php if(!aiosc_is_premade_response($response)) : ?>class="active"<?php endif; ?> data-screen="premade-responses-new"><?php _e('Add New','aiosc')?></li>
        <?php if(aiosc_is_premade_response($response)) : ?>
            <li class="active"><?php printf(__('Editing &quot;%s&quot;','aiosc'),$response->name) ?></li>
        <?php endif; ?>
    </ul>
</div>
<table class="form-table">
    <tbody>
    <tr>
        <th><label for="pri-name"><?php _e('Name','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Name must be unique.','aiosc')?> </small>
        </th>
        <td>
            <input type="text" id="pri-name" name="name" style="width: 100%" value="<?php echo @$response->name?>" >
        </td>
    </tr>
    <tr>
        <th><label for="pri-desc"><?php _e('Content','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('The content of your pre-made response.','aiosc')?> </small>
        </th>
        <td>
            <?php
            wp_editor(
                $response->content,
                'aiosc-content',
                array(
                    'textarea_rows'=>4,
                    "tinymce"=>array(
                        "forced_root_block"=>false,
                        "force_br_newlines"=>true,
                        "force_p_newlines"=>false
                    )
                )
            );
            ?>
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="dep-active"><?php _e('Shared?','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Would you like others to see and use this response?','aiosc')?> </small>
        </th>
        <td>
            <?php $is_shared = aiosc_is_premade_response($response) && $response->is_shared?true:false; ?>
            <input type="checkbox" id="pri-is_shared" name="is_shared" value="1" <?php checked($is_shared); ?> />
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th>&nbsp;</th>
        <td>
            <input type="submit" class="button button-primary" id="aiosc-form-submit" value="<?php echo aiosc_is_premade_response($response)?__('Update','aiosc'):__('Save','aiosc')?>" />
            <button type="button" class="button" onClick="javascript:click_first_subtab()"><?php _e('Discard','aiosc')?></button>
        </td>
    </tr>
    </tbody>
</table>
<script type="text/javascript">
    if(aiosc_tinymce_enabled()) {
        var str = aiosc_tinyMCEPreInit;
        str = str.replace(/aiosc-demo-wp_editor/gi, 'aiosc-content');
        tinymce.init( JSON.parse(str).mceInit['aiosc-content'] );
        jQuery("input[type='submit']").on("mousedown",function() {
            tinymce.triggerSave(); //must save before submitting in order to pass data to request
        });
        jQuery('.wp-media-buttons').remove();
    }
</script>