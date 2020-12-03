<?php

/**
 * Class aiosc_Cron
 * @since 2.0
 * Contains CRON functions for AIO Support Center
 */
class aiosc_Cron {
    /**
     * Run all cron tasks
     */
    static function run() {
        self::autoclose_tickets();
        self::run_reminder_queue();
        self::run_reminder_inactivity();
    }
    static function aiosc_cron_log($message='') {
        if(empty($message)) $message = '[AIOSC CRON] Unknown error.';
        else $message = '[AIOSC CRON] '.$message;
        aiosc_log($message);
    }
    /**
     * Automatically close inactive tickets
     * @return bool
     */
    static function autoclose_tickets() {
        global $wpdb, $aiosc_settings, $aiosc_user;
        if(!aiosc_get_settings('cron_autoclose_enable')) return false;
        $ignore_deps = aiosc_get_settings('cron_autoclose_ignore_departments');
        if(!is_array($ignore_deps)) $ignore_deps = array();

        $days_ago = (int)aiosc_get_settings('cron_autoclose_interval');
        if($days_ago < 1) {
            self::aiosc_cron_log(sprintf('[Auto-Closing] [Error] autoclose_interval is less than 1: (%d)', $days_ago));
            return false;
        }

        $last_date = date('Y-m-d H:i:s', strtotime(current_time('mysql')." - $days_ago days"));
        $query = "SELECT * FROM `".aiosc_get_table(aiosc_tables::tickets)."` WHERE status = 'open' ";

        if(aiosc_get_settings('cron_autoclose_requested_closure')) $query .= " AND (closure_requested = 'Y' OR (awaiting_reply = 'N' AND last_update <= '$last_date'))";
        else $query .= " AND (awaiting_reply = 'N' AND last_update <= '$last_date')";

        if(!empty($ignore_deps)) $query .= " AND department_id NOT IN (".implode(",", $ignore_deps).")";

        $tickets = $wpdb->get_results($query);

        $errors = '';
        $count = 0;
        if(is_array($tickets) && !empty($tickets)) {
            foreach($tickets as $ticket_data) {
                $id = $ticket_data->ID;
                $ticket = new aiosc_Ticket($id, $ticket_data);
                $note = $ticket->closure_requested ? aiosc_get_settings('cron_autoclose_requested_closure_note') : aiosc_get_settings('cron_autoclose_closure_note');
                $result = aiosc_TicketManager::close_ticket($ticket, $note, aiosc_get_settings('cron_autoclose_notify_customer'));
                if(aiosc_is_error($result)) {
                    $result = json_decode($result, true);
                    self::aiosc_cron_log(@$result['message']);
                }
                else {
                    $count++;
                    //Delete from CRON table if ticket is closed
                    $wpdb->query("DELETE FROM `".aiosc_get_table(aiosc_tables::cron)."` WHERE ticket_id=$id AND action='reminder_inactivity'");
                }
            }
        }
        if($count > 0) {
            self::aiosc_cron_log(sprintf('[Auto-Closing] [Success] Total of %d tickets were closed.', $count));
        }
        else {
            self::aiosc_cron_log('[Auto-Closing] [Idle] Task ran successfully but no action was taken.');
        }
        return true;
    }
    static function run_reminder_queue() {
        global $wpdb, $aiosc_settings, $aiosc_user;
        $action = 'reminder_queue';
        if(!aiosc_get_settings('cron_reminder_queue_enable')) return false;
        $ignore_deps = aiosc_get_settings('cron_reminder_queue_ignore_departments');
        if(!is_array($ignore_deps)) $ignore_deps = array();

        if((int)aiosc_get_settings('cron_reminder_queue_interval') < 1) {
            self::aiosc_cron_log(sprintf('[Queue Reminder] [Error] cron_reminder_queue_interval is less than 1: (%d)', (int)aiosc_get_settings('cron_reminder_queue_interval')));
            return false;
        }

        /** Dates */
        $now = current_time('mysql');
        $reminder_date = date('Y-m-d H:i:s', strtotime($now." - ".(int)aiosc_get_settings('cron_reminder_queue_interval')." days"));

        /** Query */
        $query = "SELECT * FROM `".aiosc_get_table(aiosc_tables::tickets)."` WHERE last_update <= '$reminder_date' ";

        if(aiosc_get_settings('cron_reminder_queue_include_open'))
            $query .= " AND (status = 'queue' OR (status = 'open' AND awaiting_reply = 'Y'))";
        else $query .= " AND status = 'queue'";

        if(!empty($ignore_deps)) $query .= " AND department_id NOT IN (".implode(",", $ignore_deps).")";

        //and get only those tickets for which cron did not send notification yet
        $query .= " AND ID NOT IN((SELECT ticket_id FROM `".aiosc_get_table(aiosc_tables::cron)."` WHERE action='$action' GROUP BY ticket_id)) ";

        $tickets = $wpdb->get_results($query);

        $count = 0;
        if(is_array($tickets) && !empty($tickets)) {
            foreach($tickets as $ticket_data) {
                $remind = false;
                $id = $ticket_data->ID;

                $ticket = new aiosc_Ticket($id, $ticket_data);
                $result = aiosc_EmailManager::send_customer_cron_reminder_queue($ticket);
                if($result == false) {
                    self::aiosc_cron_log(sprintf('[Queue Reminder] [Error] Could not send e-mail reminder for Ticket ID %d', $ticket->ID));
                }
                else {
                    $wpdb->query("INSERT INTO `".aiosc_get_table(aiosc_tables::cron)."` (ticket_id, action, date_called) VALUES($id, '$action', '$now')");
                    $count++;
                }
            }
        }
        if($count > 0) {
            self::aiosc_cron_log(sprintf('[Queue Reminder] [Success] Total of %d notifications were sent.', $count));
        }
        else {
            self::aiosc_cron_log('[Queue Reminder] [Idle] Task ran successfully but no action was taken.');
        }
        return true;
    }
    static function run_reminder_inactivity() {
        global $wpdb, $aiosc_settings, $aiosc_user;
        $action = 'reminder_inactivity';
        if(!aiosc_get_settings('cron_autoclose_enable')) return false;
        if(!aiosc_get_settings('cron_reminder_inactivity_enable')) return false;
        $ignore_deps = aiosc_get_settings('cron_autoclose_ignore_departments');
        if(!is_array($ignore_deps)) $ignore_deps = array();

        if((int)aiosc_get_settings('cron_reminder_inactivity_interval') < 1) {
            self::aiosc_cron_log(sprintf('[Queue Reminder] [Error] cron_reminder_inactivity_interval is less than 1: (%d)', (int)aiosc_get_settings('cron_reminder_inactivity_interval')));
            return false;
        }

        /** Dates */
        $now = current_time('mysql');
        $closure_date = date('Y-m-d H:i:s', strtotime(current_time('mysql')." -".(int)aiosc_get_settings('cron_autoclose_interval')." days"));
        $reminder_date = date('Y-m-d H:i:s', strtotime($closure_date." - ".(int)aiosc_get_settings('cron_reminder_inactivity_interval')." days"));

        /** Query */
        $query = "SELECT * FROM `".aiosc_get_table(aiosc_tables::tickets)."` WHERE status = 'open' AND awaiting_reply = 'N' AND last_update <= '$reminder_date' ";

        if(!empty($ignore_deps)) $query .= " AND department_id NOT IN (".implode(",", $ignore_deps).")";

        //and get only those tickets for which cron did not send notification yet
        $query .= " AND ID NOT IN((SELECT ticket_id FROM `".aiosc_get_table(aiosc_tables::cron)."` WHERE action='$action' GROUP BY ticket_id)) ";

        $tickets = $wpdb->get_results($query);

        $count = 0;
        if(is_array($tickets) && !empty($tickets)) {
            foreach($tickets as $ticket_data) {
                $remind = false;
                $id = $ticket_data->ID;

                $ticket = new aiosc_Ticket($id, $ticket_data);
                $result = aiosc_EmailManager::send_customer_cron_reminder_inactivity($ticket);
                if($result == false) {
                    self::aiosc_cron_log(sprintf('[Inactivity Reminder] [Error] Could not send e-mail reminder for Ticket ID %d', $ticket->ID));
                }
                else {
                    $wpdb->query("INSERT INTO `".aiosc_get_table(aiosc_tables::cron)."` (ticket_id, action, date_called) VALUES($id, '$action', '$now')");
                    $count++;
                }

            }
        }
        if($count > 0) {
            self::aiosc_cron_log(sprintf('[Inactivity Reminder] [Success] Total of %d notifications were sent.', $count));
        }
        else {
            self::aiosc_cron_log('[Inactivity Reminder] [Idle] Task ran successfully but no action was taken.');
        }
        return true;
    }
}