<?php
global $aiosc_settings, $aiosc_capabilities;

$departments = aiosc_DepartmentManager::get_departments(true);
$priorities = aiosc_PriorityManager::get_priorities(true);

$domain = aiosc_get_domain();
?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li data-screen="email"><?php _e('Auto Responders','aiosc')?></li>
        <li class="active" data-screen="email-piping"><?php _e('E-Mail Piping','aiosc')?></li>
    </ul>
</div>
<input type="hidden" name="section" value="email-piping" />
<table class="form-table">
    <tbody>
    <tr>
        <th><label for="email_piping_enable"><?php _e('Enable E-Mail Piping','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Allow users to create / reply to tickets via e-mail.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="email_piping_enable" name="email_piping_enable" value="1" <?php checked($aiosc_settings->get('email_piping_enable'))?>>
                <?php _e('Yes, enable e-mail piping.','aiosc')?></label>
        </td>
    </tr>
    <tr>
        <th><label for="email_piping_enable_html"><?php _e('Enable HTML Content-Type','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Allow HTML content in email or only plain text.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="email_piping_enable_html" name="email_piping_enable_html" value="1" <?php checked($aiosc_settings->get('email_piping_enable_html'))?>>
                <?php _e('Yes, allow user to send HTML e-mails.','aiosc')?></label>
        </td>
    </tr>
    <tr><td colspan="2" class="aiosc-title"><h3><?php _e('Ticket Creation','aiosc')?></h3><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="email_piping_domain"><?php _e('Forwarded E-Mail Address','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Users will be sending their tickets to this e-mail address.','aiosc')?> </small>
        </th>
        <td>
            <input type="text" id="email_piping_support_addr" name="email_piping_support_addr" placeholder="ex. support@<?php echo $domain?>" size="40" value="<?php echo $aiosc_settings->get('email_piping_support_addr')?>" />
        </td>
    </tr>
    <tr>
        <th><label for="email_piping_creation_department"><?php _e('Default Department','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Active Department used for storing tickets created via e-mail.','aiosc')?> </small>
        </th>
        <td>
            <?php
            if($departments != false) : ?>
                <select id="email_piping_creation_department" name="email_piping_creation_department">
                    <option value=""><?php _e('Please select...','aiosc')?></option>
                    <?php foreach($departments as $dep) : ?>
                        <option value="<?php echo $dep->ID?>" <?php if($aiosc_settings->get('email_piping_creation_department') == $dep->ID) : ?>selected<?php endif;?>><?php echo $dep->name?></option>
                    <?php endforeach; ?>
                </select>
            <?php else : ?>
                <?php _e('You must have at least one <strong>active</strong> department for this feature to work.', 'aiosc')?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th><label for="email_piping_creation_priority"><?php _e('Default Priority','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Active Priority used for tickets created via e-mail.','aiosc')?> </small>
        </th>
        <td>
            <?php
            if($priorities != false) : ?>
                <select id="email_piping_creation_priority" name="email_piping_creation_priority">
                    <option value=""><?php _e('Please select...','aiosc')?></option>
                    <?php foreach($priorities as $pri) : ?>
                        <option value="<?php echo $pri->ID?>" <?php if($aiosc_settings->get('email_piping_creation_priority') == $pri->ID) : ?>selected<?php endif;?>><?php echo $pri->name?></option>
                    <?php endforeach; ?>
                </select>
            <?php else : ?>
                <?php _e('You must have at least one <strong>active</strong> priority for this feature to work.', 'aiosc')?>
            <?php endif; ?>
        </td>
    </tr>
    <tr><td colspan="2" class="aiosc-title"><h3><?php _e('Ticket Replies','aiosc')?></h3><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="email_piping_domain"><?php _e('E-mail Sub-Domain','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Where to receive replies from users?','aiosc')?> </small>
        </th>
        <td>
            *@<input type="text" id="email_piping_domain" name="email_piping_domain" size="38" placeholder="aiosc.<?php echo $domain?>" value="<?php echo $aiosc_settings->get('email_piping_domain')?>" />
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