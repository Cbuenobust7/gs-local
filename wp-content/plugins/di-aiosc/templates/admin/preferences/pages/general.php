<?php
global $aiosc_settings, $aiosc_capabilities;

$pages = get_pages();
?>
<input type="hidden" name="section" value="pages" />
<table class="form-table">
    <tbody>
    <tr>
        <th><label for="pages_frontend_enable"><?php _e('Use Front-End Pages','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Are you going to use AIOSC in front-end?','aiosc')?> </small></th>
        <td>
            <label>
            <input type="checkbox" id="pages_frontend_enable" name="pages_frontend_enable" <?php checked($aiosc_settings->get('pages_frontend_enable')) ?> />
                <?php _e('Yes, enable front-end pages.','aiosc')?>
            </label>
        </td>
    </tr>
    <tr><td colspan="2"><div class="aiosc-separator"></div></td> </tr>
    <tr>
        <th><label for="page_ticket_form"><?php _e('New Ticket Form','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Which page will display this form?','aiosc')?> </small></th>
        <td>
            <select id="page_ticket_form" name="page_ticket_form">
                <option value=""><?php _e('- None -','aiosc')?></option>
                <?php
                foreach($pages as $page) :
                    $selected = $page->ID == $aiosc_settings->get('page_ticket_form')?'selected="selected"':'';
                    ?>
                    <option value="<?php echo $page->ID?>" <?php echo $selected ?>><?php echo $page->post_title?></option>
                <?php endforeach; ?>
            </select>
            <small><?php printf(__('Use <code>%s</code> shortcode on this page to display form.','aiosc'),'[aiosc_ticket_form]')?></small>
        </td>
    </tr>
    <tr>
        <th><label for="page_ticket_preview"><?php _e('Ticket Preview (Single)','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Which page will display single ticket preview?','aiosc')?> </small></th>
        <td>
            <select id="page_ticket_preview" name="page_ticket_preview">
                <option value=""><?php _e('- None -','aiosc')?></option>
                <?php
                foreach($pages as $page) :
                    $selected = $page->ID == $aiosc_settings->get('page_ticket_preview')?'selected="selected"':'';
                    ?>
                    <option value="<?php echo $page->ID?>" <?php echo $selected ?>><?php echo $page->post_title?></option>
                <?php endforeach; ?>
            </select>
            <small><?php printf(__('Use <code>%s</code> shortcode on this page to ticket preview.','aiosc'),'[aiosc_ticket_preview]')?></small>
        </td>
    </tr>
    <tr>
        <th><label for="page_ticket_list"><?php _e('Ticket List (My Tickets)','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Which page will display list of all user\'s tickets?','aiosc')?> </small></th>
        <td>
            <select id="page_ticket_list" name="page_ticket_list">
                <option value=""><?php _e('- None -','aiosc')?></option>
                <?php
                foreach($pages as $page) :
                    $selected = $page->ID == $aiosc_settings->get('page_ticket_list')?'selected="selected"':'';
                    ?>
                    <option value="<?php echo $page->ID?>" <?php echo $selected ?>><?php echo $page->post_title?></option>
                <?php endforeach; ?>
            </select>
            <small><?php printf(__('Use <code>%s</code> shortcode on this page to display ticket list.','aiosc'),'[aiosc_ticket_list]')?></small>
        </td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td>
            <input type="submit" class="button button-primary" id="aiosc-form-submit" value="<?php _e('Submit','aiosc')?>" />
            <button type="button" class="button" id="aiosc-form-discard"><?php _e('Discard','aiosc')?></button>
        </td>
    </tr>
    </tbody>
</table>