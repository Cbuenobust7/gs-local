<?php
class aiosc_ReplyManager {
    static function is_delay_ok($user, $ticket_id) {
        global $aiosc_settings, $wpdb;
        if($aiosc_settings->get('reply_delay') < 1) return true;
        $ticket_id = esc_sql($ticket_id);
        $last = $wpdb->get_var('SELECT date_created FROM `'.aiosc_get_table(aiosc_tables::replies)."` WHERE author_id=$user->ID AND ticket_id=$ticket_id ORDER BY date_created DESC LIMIT 0,1");
        $last = strtotime($last);
        if($last < 1) return true;
        $delay = $aiosc_settings->get('reply_delay',0);
        $now = current_time('timestamp');
        if($now > ($last + $delay)) return true;
        else return false;
    }
    static function update_reply($id = 0, $ticket_id, $content, $attachments = false, $is_public=false, $via_email = false, $user = null) {
        global $aiosc_settings, $aiosc_user, $wpdb;

        if(!aiosc_is_user($user)) $user = $aiosc_user;
        if(is_numeric($id) && $id > 0) $reply = new aiosc_Reply($id);
        else $reply = false;

        if($id < 1) {
            //check creation delay
            if(!self::is_delay_ok($user, $ticket_id))
                return aiosc_response(0,__("<strong>Error:</strong> You must wait some time until you can post a new reply.",'aiosc'));
        }
        $ticket = new aiosc_Ticket($ticket_id);
        if(!aiosc_is_ticket($ticket) || ($reply == false && $ticket->status == 'closed'))
            return aiosc_response(0,__("<strong>Error:</strong> Ticket doesn't exist or is closed for replies.",'aiosc'));

        if(!$user->can('reply_ticket',array('ticket_id'=>$ticket_id)))
            return aiosc_response(0,AIOSC_PERMISSION_ERROR);

        $errors = array();

        $aiosc_settings->load_settings();

        if(strlen($content) < $aiosc_settings->get('min_reply_len'))
            $errors[] = sprintf(__('<strong>Error:</strong> Description is too short. Please write a more constructive description.','aiosc'),$aiosc_settings->get('min_reply_len'));

        if(!empty($errors)) return aiosc_response(0,implode('<br>',$errors));
        //process attachments
        $files = array();
        $fupload = aiosc_AttachmentManager::upload_attachments($attachments);

        if(aiosc_is_error($fupload)) {
            $fupload = @json_decode($fupload);
            $errors[] = @$fupload->message;
            return aiosc_response(0,implode('<br>',$errors));
        }
        else {

            $fupload = @json_decode($fupload);
            $files = @$fupload->data->files;
        }

        if($reply === false) $errors = apply_filters('aiosc_before_reply_creation',$errors);
        else $errors = apply_filters('aiosc_before_reply_update',$errors, $reply);

        if(!empty($errors)) return aiosc_response(0,implode('<br>',$errors));

        //everything passed? insert/update ticket into database
        $content = esc_sql($content);
        $is_public = aiosc_boolToEnum($is_public);
        $now = current_time('mysql');

        if(aiosc_is_reply($reply)) {
            $q = $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::replies)."` SET
            content='$content', ticket_id='$ticket->ID', is_public='$is_public' WHERE ID=$reply->ID");

            $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::tickets)."` SET last_update='$now', awaiting_reply = '".aiosc_boolToEnum(!$user->can('staff'))."' WHERE ID=".$ticket->ID);
            aiosc_log($wpdb->last_query);
            do_action('aiosc_after_reply_update',$ticket);
            return aiosc_response(1,sprintf(__('Reply <code>#%s</code> updated successfully.','aiosc'),$reply->ID));
        }
        else {
            if(!empty($files)) $files = esc_sql(serialize($files));
            else $files = '';
            $via_email = aiosc_boolToEnum($via_email == true?true:false);
            $staff_reply = aiosc_boolToEnum($user->ID != $ticket->author_id);
            $q = $wpdb->query("INSERT INTO `".aiosc_get_table(aiosc_tables::replies)."`
            (content, ticket_id, author_id, is_public, attachment_ids, via_email, is_staff_reply, date_created)
            VALUES('$content', $ticket->ID, $user->ID, '$is_public', '$files', '$via_email', '$staff_reply', '$now')");
            if($q) {
                if($ticket->status == 'queue' && $user->can('staff')) {
                    $ticket->open();
                }
                $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::tickets)."` SET last_update='$now', awaiting_reply = '".aiosc_boolToEnum(!$user->can('staff'))."' WHERE ID=".$ticket->ID);
                $reply = new aiosc_Reply($wpdb->insert_id);

                do_action('aiosc_after_reply_creation',$reply);

                //to customer
                if($user->ID != $ticket->author_id) {
                    if($aiosc_settings->get('email_ar_customer_ticket_reply')) {
                        aiosc_EmailManager::send_customer_reply($ticket, $reply);
                    }
                }
                else {
                    //to operator
                    if($aiosc_settings->get('email_ar_staff_ticket_reply')) {
                        aiosc_EmailManager::send_staff_reply($ticket, $reply);
                    }
                }

                return aiosc_response(1,__('Reply was posted successfully.','aiosc'));
            }
            else return aiosc_response(0,__('<strong>Error:</strong> Reply could not be created for unknown reason.','aiosc'));
        }
    }

    /**
     * Get array of table fields in Tickets table
     * @return array
     */
    private static function get_columns() {
        return array('ID','content','author_id','ticket_id','is_public','date_created','attachment_ids');
    }
    static function get_replies($args=array(),$additional_query="",$search_like="") {
        global $aiosc_user, $wpdb;
        $cols = self::get_columns();
        $q = "SELECT * FROM `".aiosc_get_table(aiosc_tables::replies)."`";
        //parse
        $where = "";
        $is_and = 0;
        if(!empty($args) && is_array($args)) {
            foreach($args as $arg=>$val) {
                if(in_array($arg,$cols)) {
                    if(empty($where)) $where .= " WHERE ";
                    if($is_and > 0) $where .= " AND ";
                    $where .= $arg." = '".esc_sql($val)."'";
                    $is_and++;
                }
            }
            if(!empty($search_like))
                $where .= " AND $search_like ";
        }
        else {
            if(!empty($search_like))
                $where .= " WHERE $search_like ";
        }
        if(!empty($additional_query))
            $where .= " ".$additional_query;

        $results = $wpdb->get_results($q.$where);
        $replies = array();
        foreach($results as $result) {
            $replies[] = new aiosc_Reply($result->ID,$result);
        }
        return $replies;
    }

    /**
     * Lazy loading of replies, called via AJAX from Ticket Preview page
     * @param $ticket_obj
     * @param int $from
     * @param int $limit
     * @param bool $frontend - if true, shortcode reply template will be used
     * @return array|mixed|string|void
     */
    static function load_ajax_replies($ticket_obj, $from = 0, $limit = 3, $frontend=false) {
        global $aiosc_user, $aiosc_settings;
        if(is_numeric($ticket_obj)) $ticket_obj = new aiosc_Ticket($ticket_obj);
        if(!aiosc_is_ticket($ticket_obj)) return aiosc_response(0);
        $from = (int)$from;
        $limit = (int)$limit;
        $replies = self::get_replies(array('ticket_id'=>$ticket_obj->ID)," ORDER BY ID DESC LIMIT $from,$limit");
        if(!empty($replies) && $replies !== false) {
            global $can_edit_replies;
            $can_edit_replies = $aiosc_user->can('edit_ticket',array('ticket_id'=>$ticket_obj));;
            ob_start();
            foreach($replies as $r) {
                global $reply, $ticket;
                $ticket = $ticket_obj;
                $reply = $r;
                if($frontend) {
					/** 
					 * @update 2.0.2 - Added TRUE to aiosc_get_template_path so template can be loaded from theme's directory if exists.
					 **/
                    include aiosc_get_template_path('shortcodes/single/reply/single.php', true);
                }
                else
                    include AIOSC_DIR."/templates/admin/ticket/single/reply/single.php";
            }
        }

        $html = ob_get_clean();
        if(!empty($html)) return aiosc_response(1,'',array('html'=>$html,'limit'=>$limit));
        else return aiosc_response(0);
    }
    static function get_count_by($args=array()) {
        global $wpdb;
        $q = "SELECT COUNT(*) FROM `".aiosc_get_table(aiosc_tables::replies)."`";
        if(!empty($args)) {
            foreach($args as $k=>$v) {
                if(in_array($k,self::get_columns())) $where[] = "$k = '".esc_sql($v)."'";
            }
        }
        if(!empty($where)) {
            $q .= " WHERE ".implode(" AND ",$where);
        }
        return $wpdb->get_var($q);
    }

    /**
     * Removes all replies from specific ticket.
     * @param $ticket_id
     * @param bool $attachments - remove attachments as well?
     */
    static function remove_by_ticket($ticket_id, $attachments=true) {
        global $wpdb;
        $reps = self::get_replies(array('ticket_id'=>$ticket_id));
        foreach($reps as $rep) {
            if(!empty($rep->attachment_ids) && $attachments) {
                foreach($rep->attachment_ids as $att_id) {
                    $att = new aiosc_Attachment($att_id);
                    $att->remove();
                }
            }
        }
        $ticket_id = esc_sql($ticket_id);
        $wpdb->query("DELETE FROM `".aiosc_get_table(aiosc_tables::replies)."` WHERE ticket_id=$ticket_id");
    }
    static function remove($reply) {
        global $aiosc_user, $aiosc_settings, $wpdb;
        if(is_numeric($reply)) $reply = new aiosc_Reply($reply);
        if(!$aiosc_user->can('delete_reply',array('ticket_id'=>$reply->ticket_id, 'reply_id'=>$reply)) || aiosc_is_demo()) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
        $id = $reply->ID;
        $reply->remove(true);
        return aiosc_response(1,sprintf(__('Reply <code>%s</code> was removed successfully.','aiosc'),"#$id"));
    }
}
