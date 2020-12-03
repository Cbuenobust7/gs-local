<?php
class aiosc_Ticket {
    public $ID;
    public $data;
    function __construct($id, $data = false) {
        $this->ID = $id;
        if(is_numeric($id) && $id > 0) {
            if($data !== false) $this->data = $data;
            else $this->data = $this->get_data();
        }
        else $this->data = false;
        if($this->data != false) {
            $this->data->subject = aiosc_clean_content($this->data->subject,"", false);
            $this->data->content = aiosc_clean_content($this->data->content);
            $this->data->closure_note = aiosc_clean_content($this->data->closure_note);
            if(empty($this->data->closure_note))
                $this->data->closure_note = apply_filters('aiosc_closure_note',__('Operator did not leave any note about closure.','aiosc'));

            if($this->data->status == 'queue') $this->data->status_name = __('In Queue','aiosc');
            elseif($this->data->status == 'open') $this->data->status_name = __('Open','aiosc');
            elseif($this->data->status == 'closed') $this->data->status_name = __('Closed','aiosc');
            else $this->data->status_name = $this->data->status;

            $this->data->attachment_ids = (!empty($this->data->attachment_ids)) ? unserialize($this->data->attachment_ids) : false;
            $this->data->ticket_meta = (!empty($this->data->ticket_meta)) ? unserialize($this->data->ticket_meta) : false;
            $this->data->is_public = aiosc_enumToBool($this->data->is_public);
            $this->data->closure_requested = aiosc_enumToBool($this->data->closure_requested);

            if(strtotime(@$this->data->last_update) < strtotime($this->data->date_created))
                $this->data->last_update = $this->data->date_created;

            /**
             * @since 2.0
             */
            $this->data->awaiting_reply = ($this->data->status == 'closed') ? false : ($this->data->status == 'queue') ? true : aiosc_enumToBool($this->data->awaiting_reply);
        }
    }

    private function get_data() {
        global $wpdb;
        $q = "SELECT * FROM `".aiosc_get_table(aiosc_tables::tickets)."` WHERE ID=".$this->ID;
        $data = $wpdb->get_results($q);
        if(count($data) > 0) return $data[0];
        else return false;
    }
    public function save_meta() {
        global $wpdb;
        if($this->data->ticket_meta !== false)
            $meta = serialize($this->data->ticket_meta);
        else
            $meta = '';

        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::tickets)."` SET ticket_meta='$meta' WHERE ID=".$this->ID);
    }
    public function load_meta() {
        global $wpdb;
        $q = "SELECT ticket_meta FROM `".aiosc_get_table(aiosc_tables::tickets)."` WHERE ID=".$this->ID;
        $data = $wpdb->get_var($q);
        $this->data->ticket_meta = !empty($data) ? unserialize($data) : false;
    }

    /**
     * Meta is passed as 'key'=>'value'
     * @param $key - meta key
     * @param string $default - if array key does not exist, return this value
     * @return string
     */
    function get_meta($key, $default='') {
        if($this->data->ticket_meta === false) return $default;
        if(!isset($this->data->ticket_meta[$key])) return $default;
        return $this->data->ticket_meta[$key];
    }
    function unset_meta($keys, $save=true) {
        global $wpdb;
        if(!is_array($keys)) $keys = array($keys);
        foreach($keys as $key) {
            unset($this->data->ticket_meta[$key]);
        }
        if($save) $this->save_meta();
    }
    function set_meta($key_values,$save=true) {
        global $wpdb;
        foreach($key_values as $k=>$v) {
            $this->data->ticket_meta[$k] = $v;
        }
        if($save) $this->save_meta();
    }
    function set_department($department, $new_op = 0) {
        if(is_numeric($department) && $department > 0) $department = new aiosc_Department($department);
        if(!aiosc_is_department($department)) return false;
        $new_ops = $department->get_ops(false);
        if($new_op < 1) {
            if(in_array($this->data->op_id, $new_ops)) {
                $new_op = $this->data->op_id;
            }
            else {
                $new_op = aiosc_UserManager::get_free_operator($department, $new_ops, array($this->data->op_id));
            }
        }
        else {
            if(!in_array($new_op, $new_ops)) {
                $new_op = aiosc_UserManager::get_free_operator($department, $new_ops, array($this->data->op_id));
                if($new_op < 1) return false;
            }
        }
        if($new_op < 1) return false;
        global $wpdb;
        $new_op = esc_sql($new_op);
        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::tickets)."` SET department_id=$department->ID, op_id = $new_op WHERE ID=$this->ID");
        $this->data->department_id = $department->ID;
        $this->data->op_id = $new_op;
        return true;
    }
    function set_priority($priority, $priority_level=0) {
        if(is_numeric($priority) && $priority > 0) $priority = new aiosc_Priority($priority);
        if(!aiosc_is_priority($priority)) return false;
        $level = $priority_level != null?(int)$priority_level:$priority->level;
        global $wpdb;
        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::tickets)."` SET priority_id=$priority->ID, priority_level=$level
         WHERE ID=$this->ID");
        $this->data->priority_id = $priority->ID;
        $this->data->priority_level = $level;
        return true;
    }
    function set_visibility($visibility) {
        $visibility = aiosc_boolToEnum($visibility);
        if($visibility != 'Y' && $visibility != 'N') return false;
        global $wpdb;
        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::tickets)."` SET is_public='$visibility' WHERE ID=$this->ID");
        $this->data->is_public = aiosc_enumToBool($visibility);
        return true;
    }
    function set_last_update($date = '') {
        if(!is_numeric($date) || $date == '') $date = current_time('mysql');
        global $wpdb;
        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::tickets)."` SET last_update='$date' WHERE ID=$this->ID");
        $this->data->last_update = $date;
        return true;
    }
    /**
     * Changes Author ID of ticket, and takes ownership of all replies and attachments related to this ticket.
     * @since 1.0.9
	 * @update 2.0.1 - Replies for a whole ticket were assigned to new author, instead of replies only created by this author.
     * @param $author
     * @return bool
     */
    function set_author($author) {
        global $wpdb;
        if(is_numeric($author)) $author = new aiosc_User($author);
        if(!aiosc_is_user($author) || !$author->can('create_ticket')) return false;
        if($author->ID == $this->author_id) return true;
        $old_author = $this->author_id;
        $replies = aiosc_ReplyManager::get_replies(array('ticket_id'=>$this->ID));
        //set ownership of files attached to ticket directly
        if(is_array($this->attachment_ids)) {
            foreach($this->attachment_ids as $att) {
                $a = new aiosc_Attachment($att);
                $a->set_owner($author);
            }
        }
        //set ownership of replies and attached files.
        if(is_array($replies)) {
            foreach($replies as $reply) {
				if($reply->author_id == $old_author)
					$reply->set_author($author);
            }
        }
        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::tickets)."` SET author_id='$author->ID' WHERE ID=$this->ID");
        $this->data->author_id = $author->ID;
        do_action('aiosc_ticket_authorship_change', $this, $old_author, $author->ID);
        return true;
    }
    function reply_count() {
        if($this->ID > 0) {
            global $wpdb;
            $q = $wpdb->get_var("SELECT COUNT(*) FROM `".aiosc_get_table(aiosc_tables::replies)."` WHERE ticket_id=$this->ID");
            return $q;
        }
        return 0;
    }
    function get_url($edit_mode=false, $return_frontend = false) {
        global $aiosc_user;
        if($edit_mode && $aiosc_user->can('edit_ticket',array('ticket_id'=>$this->ID)))
            return aiosc_get_page_ticket_preview($this->ID,true, false);
        else return aiosc_get_page_ticket_preview($this->ID,false, $return_frontend);

    }
    function get_attachments_count() {
        if(isset($this->data->attachment_ids) && is_array($this->data->attachment_ids)) return count($this->data->attachment_ids);
        else return 0;
    }

    /**
     * Get total size of attachments (in Kilobytes)
     * @return float|int|string
     */
    function get_attachments_size() {
        $size = 0;
        if(is_array($this->data->attachment_ids)) {
            foreach($this->data->attachment_ids as $id) {
                $att = new aiosc_Attachment($id);
                $size += $att->get_file_size('b');
            }
            if($size > 0) return number_format($size / 1024,2);
            else return $size;
        }
        else return $size;
    }
    function remove($attachments=true) {
        if(empty($this->ID) || $this->ID < 1) return false;
        //remove attachments if needed
        if(is_array($this->attachment_ids) && $attachments) {
            foreach($this->attachment_ids as $att_id) {
                $att = new aiosc_Attachment($att_id);
                $att->remove();
            }
        }
        //remove replies
        aiosc_ReplyManager::remove_by_ticket($this->ID,$attachments);
        global $wpdb;
        $wpdb->query("DELETE FROM `".aiosc_get_table(aiosc_tables::tickets)."` WHERE ID=$this->ID");
        do_action('aiosc_ticket_removed',$this->ID);
        return true;
    }
    function open() {
        global $wpdb;
        $now = current_time('mysql');
        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::tickets)."` SET status='open', closure_note='', closure_requested='N', date_open='$now', last_update='$now' WHERE ID=$this->ID");
    }

    /**
     * @param string $type - days / date / timestamp
     * @param string $date_format
     * @return string
     */
    function get_scheduled_closure($type = 'days', $date_format = '') {
        global $wpdb;
        if(empty($date_format)) $date_format = get_option('date_format');
        $days = aiosc_get_settings('cron_autoclose_interval');
        $date = strtotime($this->data->last_update." +$days days");
        if($type == 'date')
            return aiosc_get_datetime($date, $date_format);
        elseif($type == 'timestamp')
            return strtotime($date);
        elseif($type == 'days') {
            $now = current_time('timestamp');
            if($now <= $date) return 0;
            return floor(($date - $now) / (60*60*24));
        }
        return '';

    }
    /** Magic */
    function __isset( $key ) {
        return isset( $this->data->$key );
    }
    function __get( $key ) {
        $value = '';
        if ( isset( $this->data->$key ) )
            $value = $this->data->$key;

        return $value;
    }
    function __set( $key, $value ) {
        $this->data->$key = $value;
    }
}
function aiosc_is_ticket($object) {
    if($object === false || empty($object) || $object === null) return false;
    if(is_a($object,'aiosc_Ticket') && is_numeric($object->ID) && $object->ID > 0 && $object->data != false) return true;
    return false;
}