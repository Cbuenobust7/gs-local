<?php
class aiosc_EmailManager {
    /**
     * Send e-mails using @wp_mail function
     *
     * To enable debug-only mode, define @AIOSC_EMAIL_DEBUG_ONLY variable in wp-config.php or di-aiosc.php
     * @updated 2.0
     *
     * @param $to
     * @param string $reply_to
     * @param $subject
     * @param $message
     * @param string $headers
     * @param null $callback
     * @return bool
     */
    static function send($to, $reply_to='', $subject, $message, $headers='', $callback=null) {
        if(empty($to)) return false;
        if(!is_array($to)) $to = array($to);
        for($i=0;$i<count($to);$i++) {
            if(!aiosc_is_email($to[$i])) unset($to[$i]);
        }
        if(empty($to)) return false;

        $from = aiosc_get_from_email();
        if(empty($reply_to) || ($reply_to != $from && !aiosc_is_email($reply_to))) {
            $reply_to = $from;
        }
        if(empty($subject)) return false;
        if(empty($message)) return false;
        if(empty($headers)) {
            $headers = 'From: '.$from . "\r\n".
                'Reply-To: '.$reply_to . "\r\n".
                'Content-Type: text/html; charset=UTF-8' . "\r\n".
                'X-Mailer: PHP/' . phpversion();
        }
        if(!defined('AIOSC_EMAIL_DEBUG_ONLY') || AIOSC_EMAIL_DEBUG_ONLY == false) {
            if(wp_mail($to, $subject, $message, $headers)) {
                aiosc_log('[aiosc_EmailManager::send] [To: '.print_r($to,true).'] Email was sent successfully.');
                if(!empty($callback))
                    do_action($callback, $to);
                return true;
            }
        }
        else {
            aiosc_log('[aiosc_EmailManager::send] [DEBUG ENABLED] [To: '.implode(', ',$to).'] [Message Len: '.strlen($message).'].');
            return true;
        }
        aiosc_log('[aiosc_EmailManager::send] [To: '.print_r($to,true).'] [Message Len: '.strlen($message).'] Email was not sent.');
        return false;
    }
    private static function get_replyto_email(aiosc_User $user, $ticket) {
        global $aiosc_settings;
        if(!$aiosc_settings->get('email_piping_enable')) return null;
        if(!$user->can('reply_ticket',array('ticket_id'=>$ticket))) {
            return null;
        }
        $email = $user->ID."-".$ticket->ID."-".$ticket->hash_code."@".$aiosc_settings->get('email_piping_domain');
        if(!aiosc_is_email($email)) {
            $email = null;
        }
        return $email;
    }
    /** @SENDING */

    /**
     * Fired when customer creates new ticket, so we send him a confirmation e-mail that his ticket is
     * created successfully.
     *
     * @hook aiosc_email_customer_creation_sent
     *
     * @param $ticket
     * @return bool
     */
    static function send_customer_creation($ticket) {
        $tpl = self::get_template('customer_creation');
        $subject = self::apply_tags($tpl['subject'], $ticket);
        $message = self::apply_tags($tpl['content'], $ticket);
        $user = new aiosc_User($ticket->author_id);
        $to = $user->wpUser->user_email;
        $reply_to = self::get_replyto_email($user,$ticket);

        return self::send($to, $reply_to, $subject, $message, null, 'aiosc_email_customer_creation_sent');
    }

    /**
     * Fired when operator posts new reply so we send this e-mail to customer to inform him
     * that he received new reply on his ticket from operator
     *
     * @hook aiosc_email_customer_reply_sent
     *
     * @param $ticket
     * @param $reply
     * @return bool
     */
    static function send_customer_reply($ticket, $reply) {
        $tpl = self::get_template('customer_reply');
        $subject = self::apply_tags($tpl['subject'], $ticket, $reply);
        $message = self::apply_tags($tpl['content'], $ticket, $reply);
        $user = new aiosc_User($ticket->author_id);
        $to = $user->wpUser->user_email;
        $reply_to = self::get_replyto_email($user,$ticket);
        return self::send($to, $reply_to, $subject, $message, null, 'aiosc_email_customer_reply_sent');
    }

    /**
     * Fired when operator closes ticket, so customer gets informed about that
     *
     * @hook aiosc_email_customer_closure_sent
     *
     * @param $ticket
     * @return bool
     */
    static function send_customer_closure($ticket) {
        $tpl = self::get_template('customer_closure');
        $subject = self::apply_tags($tpl['subject'], $ticket);
        $message = self::apply_tags($tpl['content'], $ticket);
        $user = new aiosc_User($ticket->author_id);
        $to = $user->wpUser->user_email;

        return self::send($to, '', $subject, $message, null, 'aiosc_email_customer_closure_sent');
    }

    /**
     * Fired when operator re-opens customer ticket
     *
     * @hook aiosc_email_customer_reopen_sent
     *
     * @param $ticket
     * @return bool
     */

    static function send_customer_reopen($ticket) {
        $tpl = self::get_template('customer_reopen');
        $subject = self::apply_tags($tpl['subject'], $ticket);
        $message = self::apply_tags($tpl['content'], $ticket);
        $user = new aiosc_User($ticket->author_id);
        $to = $user->wpUser->user_email;

        return self::send($to, '', $subject, $message, null, 'aiosc_email_customer_reopen_sent');

    }
    /**
     * Fired by CRON whenever ticket is about to be closed due to inactivity
     *
     * @hook aiosc_email_cron_reminder_inactivity_sent
     *
     * @param $ticket
     * @return bool
     */
    static function send_customer_cron_reminder_inactivity($ticket) {
        $tpl = self::get_template('cron_reminder_inactivity');
        $subject = self::apply_tags($tpl['subject'], $ticket);
        $message = self::apply_tags($tpl['content'], $ticket);
        $user = new aiosc_User($ticket->author_id);
        $to = $user->wpUser->user_email;

        return self::send($to, '', $subject, $message, null, 'aiosc_email_cron_reminder_inactivity_sent');

    }
    /**
     * Fired by CRON whenever ticket is in queue longer than expected
     *
     * @hook aiosc_email_cron_reminder_queue_sent
     *
     * @param $ticket
     * @return bool
     */
    static function send_customer_cron_reminder_queue($ticket) {
        $tpl = self::get_template('cron_reminder_queue');
        $subject = self::apply_tags($tpl['subject'], $ticket);
        $message = self::apply_tags($tpl['content'], $ticket);
        $user = new aiosc_User($ticket->author_id);
        $to = $user->wpUser->user_email;

        return self::send($to, '', $subject, $message, null, 'aiosc_email_cron_reminder_queue_sent');

    }

    /**
     * Fired when customer creates new ticket so operator gets informed that he just
     * got assigned to a new ticket
     *
     * @uses $aiosc_user->get_meta('aiosc_notifications') to check if user should receive notification or not
     * @uses $aiosc_user->get_meta('aiosc_department_notifications') to check if other staff members should receive email
     * @hook aiosc_email_staff_creation_sent
     *
     * @param $ticket
     * @return bool
     */
    static function send_staff_creation(aiosc_Ticket $ticket) {
        $tpl = self::get_template('staff_creation');

        /**
         * Regularly send email to assigned operator
         */
        $subject = self::apply_tags($tpl['subject'], $ticket);
        $message = self::apply_tags($tpl['content'], $ticket);
        $operator = new aiosc_User($ticket->op_id);
        $reply_to = self::get_replyto_email($operator,$ticket);
        if(!$operator->get_meta('aiosc_notifications')) return false;
        $real_result = self::send($operator->wpUser->user_email, $reply_to, $subject, $message, null, 'aiosc_email_staff_creation_sent');

        /**
         * Send email-templates to all staff members from ticket's department, but only
         * if they have @aiosc_department_notifications enabled.
         * This excludes assigned operator, he will get his mail sent at the end of the function
         * and return result.
         * @since 1.0.3
         */
        $dep = new aiosc_Department($ticket->department_id);
        $usrs = $dep->get_ops(true);
        if(!empty($usrs) && is_array($usrs)) {
            foreach($usrs as $usr) {
                if($usr->ID != $ticket->op_id && $usr->get_meta('aiosc_department_notifications')) {
                    $subject = self::apply_tags($tpl['subject'], $ticket, null, $usr);
                    $message = self::apply_tags($tpl['content'], $ticket, null, $usr);
                    $reply_to = self::get_replyto_email($usr,$ticket);
                    self::send($usr->wpUser->user_email, $reply_to, $subject, $message, null, 'aiosc_email_staff_creation_sent');
                }
            }
        }
        return $real_result;
    }

    /**
     * Fired when customer posts a new reply on his ticket, so we send email to
     * ticket operator and inform him about new reply from customer
     *
     * @uses $aiosc_user->get_meta('aiosc_notifications') to check if user should receive notification or not
     * @uses $aiosc_user->get_meta('aiosc_department_notifications') to check if other staff members should receive email
     * @hook aiosc_email_staff_reply_sent
     *
     * @param $ticket
     * @param $reply
     * @return bool
     */
    static function send_staff_reply($ticket, $reply) {
        $tpl = self::get_template('staff_reply');

        /**
         * Regularly send email to assigned operator
         */
        $subject = self::apply_tags($tpl['subject'], $ticket, $reply);
        $message = self::apply_tags($tpl['content'], $ticket, $reply);
        $operator = new aiosc_User($ticket->op_id);
        $reply_to = self::get_replyto_email($operator,$ticket);
        if(!$operator->get_meta('aiosc_notifications')) return false;
        $real_result = self::send($operator->wpUser->user_email, $reply_to, $subject, $message, null, 'aiosc_email_staff_reply_sent');

        /**
         * Send email-templates to all staff members from ticket's department, but only
         * if they have @aiosc_department_notifications enabled.
         * This excludes assigned operator, he will get his mail sent at the end of the function
         * and return result.
         * @since 1.0.3
         */
        $dep = new aiosc_Department($ticket->department_id);
        $usrs = $dep->get_ops(true);
        if(!empty($usrs) && is_array($usrs)) {
            foreach($usrs as $usr) {
                if($usr->ID != $ticket->op_id && $usr->get_meta('aiosc_department_notifications')) {
                    $subject = self::apply_tags($tpl['subject'], $ticket, $reply, $usr);
                    $message = self::apply_tags($tpl['content'], $ticket, $reply, $usr);
                    $reply_to = self::get_replyto_email($usr,$ticket);
                    self::send($usr->wpUser->user_email, $reply_to, $subject, $message, null, 'aiosc_email_staff_reply_sent');
                }
            }
        }
        return $real_result;
    }

    /**
     * Fired when customer requests Ticket Closure so operator gets notification about this
     * and he may close ticket upon request
     *
     * @uses $aiosc_user->get_meta('aiosc_notifications') to check if user should receive notification or not
     * @uses $aiosc_user->get_meta('aiosc_department_notifications') to check if other staff members should receive email
     * @hook aiosc_email_staff_closure_sent
     *
     * @param $ticket
     * @return bool
     */
    static function send_staff_closure($ticket) {
        $tpl = self::get_template('staff_closure');

        /**
         * Regularly send email to assigned operator
         */
        $subject = self::apply_tags($tpl['subject'], $ticket);
        $message = self::apply_tags($tpl['content'], $ticket);
        $operator = new aiosc_User($ticket->op_id);
        $reply_to = self::get_replyto_email($operator,$ticket);
        if(!$operator->get_meta('aiosc_notifications')) return false;
        $real_result = self::send($operator->wpUser->user_email, $reply_to, $subject, $message, null, 'aiosc_email_staff_closure_sent');

        /**
         * Send email-templates to all staff members from ticket's department, but only
         * if they have @aiosc_department_notifications enabled.
         * This excludes assigned operator, he will get his mail sent at the end of the function
         * and return result.
         * @since 1.0.3
         */
        $dep = new aiosc_Department($ticket->department_id);
        $usrs = $dep->get_ops(true);
        if(!empty($usrs) && is_array($usrs)) {
            foreach($usrs as $usr) {
                if($usr->ID != $ticket->op_id && $usr->get_meta('aiosc_department_notifications')) {
                    $subject = self::apply_tags($tpl['subject'], $ticket, null, $usr);
                    $message = self::apply_tags($tpl['content'], $ticket, null, $usr);
                    $reply_to = self::get_replyto_email($usr,$ticket);
                    self::send($usr->wpUser->user_email, $reply_to, $subject, $message, null, 'aiosc_email_staff_closure_sent');
                }
            }
        }
        return $real_result;
    }
    /**
     * Fired when customer re-opens ticket so operator gets notification about this.
     *
     * @since 2.0
     *
     * @uses $aiosc_user->get_meta('aiosc_notifications') to check if user should receive notification or not
     * @uses $aiosc_user->get_meta('aiosc_department_notifications') to check if other staff members should receive email
     * @hook aiosc_email_staff_reopen_sent
     *
     * @param $ticket
     * @return bool
     */
    static function send_staff_reopen($ticket) {
        $tpl = self::get_template('staff_reopen');

        $subject = self::apply_tags($tpl['subject'], $ticket);
        $message = self::apply_tags($tpl['content'], $ticket);
        $operator = new aiosc_User($ticket->op_id);
        $reply_to = self::get_replyto_email($operator,$ticket);
        if(!$operator->get_meta('aiosc_notifications')) return false;
        $real_result = self::send($operator->wpUser->user_email, $reply_to, $subject, $message, null, 'aiosc_email_staff_reopen_sent');

        /**
         * Send email-templates to all staff members from ticket's department, but only
         * if they have @aiosc_department_notifications enabled.
         * This excludes assigned operator, he will get his mail sent at the end of the function
         * and return result.
         * @since 1.0.3
         */
        $dep = new aiosc_Department($ticket->department_id);
        $usrs = $dep->get_ops(true);
        if(!empty($usrs) && is_array($usrs)) {
            foreach($usrs as $usr) {
                if($usr->ID != $ticket->op_id && $usr->get_meta('aiosc_department_notifications')) {
                    $subject = self::apply_tags($tpl['subject'], $ticket, null, $usr);
                    $message = self::apply_tags($tpl['content'], $ticket, null, $usr);
                    $reply_to = self::get_replyto_email($usr,$ticket);
                    self::send($usr->wpUser->user_email, $reply_to, $subject, $message, null, 'aiosc_email_staff_reopen_sent');
                }
            }
        }
        return $real_result;
    }
    /**
     * Replace dynamic tags from content with real values
     *
     * @filter aiosc_apply_content_tags
     *
     * @param $content
     * @param $ticket
     * @param null $reply
     * @param bool|aiosc_User $operator - overrides user received from $ticket->op_id
     * @return mixed|void
     */
    private static function apply_tags($content, $ticket, $reply = null, $operator = false) {
        global $aiosc_capabilities;
        if(empty($content)) return $content;
        if(is_numeric($ticket) && $ticket > 0) $ticket = new aiosc_Ticket($ticket);
        if(is_numeric($reply) && $reply > 0) $reply = new aiosc_Reply($reply);
        $tags = array();
        $r_tags = array();
        if(aiosc_is_ticket($ticket)) {
            $pri = new aiosc_Priority($ticket->priority_id);
            $dep = new aiosc_Department($ticket->department_id);
            $customer = new aiosc_User($ticket->author_id);
            $op = aiosc_is_user($operator) ? $operator : new aiosc_User($ticket->op_id);
            $tags = array(
                //ticket
                'ticket.url'=>$ticket->get_url(false),
                'ticket.front_url'=>$ticket->get_url(false,true),
                'ticket.dynamic_url'=>$ticket->get_url(false, aiosc_isset_pg('frontend')),
                'ticket.id'=>$ticket->ID,
                'ticket.priority.name'=>$pri->name,
                'ticket.priority.id'=>$pri->ID,
                'ticket.priority.level'=>$pri->level,
                'ticket.priority.color'=>$pri->color,
                'ticket.department.name'=>$dep->name,
                'ticket.department.id'=>$dep->ID,
                'ticket.subject'=>$ticket->subject,
                'ticket.content'=>$ticket->content,
                'ticket.status'=>$ticket->status_name,
                'ticket.date.created'=>aiosc_get_datetime($ticket->date_created),
                'ticket.date.open'=>aiosc_get_datetime($ticket->date_open),
                'ticket.date.closed'=>aiosc_get_datetime($ticket->date_closed),
                'ticket.closure_note'=>$ticket->closure_note,
                'ticket.scheduled_closure.days'=>$ticket->get_scheduled_closure('days'),
                'ticket.scheduled_closure.date'=>$ticket->get_scheduled_closure('date'),
                'ticket.scheduled_closure.datetime'=>$ticket->get_scheduled_closure('date', get_option('date_format') . " " . get_option('time_format')),
                'ticket.attachments.count'=>$ticket->get_attachments_count(),
                'ticket.attachments.size'=>$ticket->get_attachments_size(),
                //customer
                'customer.id'=>$customer->ID,
                'customer.first_name'=>$customer->wpUser->first_name,
                'customer.last_name'=>$customer->wpUser->last_name,
                'customer.display_name'=>$customer->wpUser->display_name,
                'customer.email'=>$customer->wpUser->user_email,
                'customer.role'=>$aiosc_capabilities->get_role_name($customer->aiosc_role),
                'customer.url'=>aiosc_get_page_user_profile($customer),
                //operator
                'operator.id'=>$op->ID,
                'operator.first_name'=>$op->wpUser->first_name,
                'operator.last_name'=>$op->wpUser->last_name,
                'operator.display_name'=>$op->wpUser->display_name,
                'operator.email'=>$op->wpUser->user_email,
                'operator.role'=>$aiosc_capabilities->get_role_name($op->aiosc_role),
                'operator.url'=>aiosc_get_page_user_profile($op),
                //Misc
                'site.name'=>get_bloginfo('name'),
                'site.url'=>get_bloginfo('url'),
                'login.url'=>wp_login_url(),
                'aiosc.my_tickets'=>aiosc_get_page_ticket_list(false),
                'aiosc.new_ticket'=>aiosc_get_page_ticket_form(false),
                'aiosc.my_tickets_front'=>aiosc_get_page_ticket_list(true),
                'aiosc.new_ticket_front'=>aiosc_get_page_ticket_form(true),
                'date.now'=>aiosc_get_datetime(current_time('timestamp')),
                'date.year'=>date('Y'),
                'date.month'=>date_i18n('M'),
                'time.now'=>date(get_option('time_format'),current_time('timestamp'))
            );
        }
        if(aiosc_is_reply($reply)) {
            $r_tags = array(
                'reply.id'=>$reply->ID,
                'reply.content'=>$reply->content,
                'reply.date.created'=>aiosc_get_datetime($reply->date_created),
                'reply.attachments.count'=>$reply->get_attachments_count(),
                'reply.attachments.size'=>$reply->get_attachments_size()
            );
        }
        if(!empty($r_tags)) $tags = array_merge($tags, $r_tags);
        $tags = apply_filters('aiosc_apply_content_tags',$tags, $ticket, $reply, $operator);
        if(!empty($tags)) {
            $nTags = array();
            foreach($tags as $k=>$v) {
                $nTags["{%$k%}"] = $v;
            }
            $content = str_replace(array_keys($nTags), array_values($nTags), $content);
        }
        return $content;
    }

    /**
     * @update 2.1.7
     */
    static function get_template($template_name) {
        global $aiosc_settings;
        $subject = $aiosc_settings->get('email_templates_'.$template_name.'_subject');
        $content = $aiosc_settings->get('email_templates_'.$template_name.'_content');

        $content = aiosc_preclean_content($content);
	$content = stripslashes(wpautop($content));

        $subject = aiosc_preclean_content($subject);
        $subject = strip_tags($subject);

        return array('subject'=>$subject,'content'=>$content);
    }
}
