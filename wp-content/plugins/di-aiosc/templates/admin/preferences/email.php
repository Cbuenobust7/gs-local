<?php
global $aiosc_settings, $aiosc_capabilities;
?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <li class="active" data-screen="email"><?php _e('Auto Responders','aiosc')?></li>
        <li data-screen="email-piping"><?php _e('E-Mail Piping','aiosc')?></li>
    </ul>
</div>
<input type="hidden" name="section" value="email" />
<table class="form-table">
    <tbody>
    <tr><td colspan="2" class="aiosc-title"><h3 style="margin-top: 0;"><?php _e('Customers','aiosc')?></h3><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="email_ar_customer_ticket_creation"><?php _e('On Ticket Creation','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Send confirmational email to user when his ticket is successfully created.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="email_ar_customer_ticket_creation" name="email_ar_customer_ticket_creation" value="1" <?php checked($aiosc_settings->get('email_ar_customer_ticket_creation'))?>>
                <?php _e('Yes, send confirmation to user.','aiosc')?></label>
        </td>
    </tr>
    <tr>
        <th><label for="email_ar_customer_ticket_reply"><?php _e('On New Reply','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Send notification email to user when Operator posts new reply on his ticket.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="email_ar_customer_ticket_reply" name="email_ar_customer_ticket_reply" value="1" <?php checked($aiosc_settings->get('email_ar_customer_ticket_reply'))?>>
                <?php _e('Yes, send notification to user.','aiosc')?></label>
        </td>
    </tr>
    <tr>
        <th><label for="email_ar_customer_ticket_close"><?php _e('On Ticket Closure','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Send notification email to user when Operator closes his ticket.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="email_ar_customer_ticket_close" name="email_ar_customer_ticket_close" value="1" <?php checked($aiosc_settings->get('email_ar_customer_ticket_close'))?>>
                <?php _e('Yes, send notification to user.','aiosc')?></label>
        </td>
    </tr>
    <tr>
        <th><label for="email_ar_customer_ticket_reopen"><?php _e('On Re-Opening','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Send notification email to user when his ticket is re-opened.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="email_ar_customer_ticket_reopen" name="email_ar_customer_ticket_reopen" value="1" <?php checked($aiosc_settings->get('email_ar_customer_ticket_reopen'))?>>
                <?php _e('Yes, send notification to user.','aiosc')?></label>
        </td>
    </tr>
    <tr><td colspan="2" class="aiosc-title"><h3><?php _e('Staff Members','aiosc')?></h3><div class="aiosc-separator"></div></td></tr>
    <tr>
        <th><label for="email_ar_staff_ticket_creation"><?php _e('On Ticket Assignment','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Send notification email to Operator when new ticket is assigned to him.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="email_ar_staff_ticket_creation" name="email_ar_staff_ticket_creation" value="1" <?php checked($aiosc_settings->get('email_ar_staff_ticket_creation'))?>>
                <?php _e('Yes, send notification to staff member.','aiosc')?></label>
        </td>
    </tr>
    <tr>
        <th><label for="email_ar_staff_ticket_reply"><?php _e('On New Reply','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Send notification email to Operator when customer posts a new reply on ticket.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="email_ar_staff_ticket_reply" name="email_ar_staff_ticket_reply" value="1" <?php checked($aiosc_settings->get('email_ar_staff_ticket_reply'))?>>
                <?php _e('Yes, send notification to staff member.','aiosc')?></label>
        </td>
    </tr>
    <tr>
        <th><label for="email_ar_staff_ticket_close"><?php _e('On Ticket Closure Request','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Send notification email to Operator when customer request closure of his ticket.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="email_ar_staff_ticket_close" name="email_ar_staff_ticket_close" value="1" <?php checked($aiosc_settings->get('email_ar_staff_ticket_close'))?>>
                <?php _e('Yes, send notification to staff member.','aiosc')?></label>
        </td>
    </tr>
    <tr>
        <th><label for="email_ar_staff_ticket_reopen"><?php _e('On Re-Opening','aiosc')?></label>
            <a href="#" class="aiosc-info-tooltip dashicons dashicons-lightbulb"></a>
            <small><?php _e('Send notification email to Operator when customer re-opens his ticket.','aiosc')?> </small>
        </th>
        <td>
            <label><input type="checkbox" id="email_ar_staff_ticket_reopen" name="email_ar_staff_ticket_reopen" value="1" <?php checked($aiosc_settings->get('email_ar_staff_ticket_reopen'))?>>
                <?php _e('Yes, send notification to staff member.','aiosc')?></label>
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