<?php

class aiosc_User {
    public $ID;
    public $wpUser;
    public $aiosc_role;
    function __construct($id = 0) {
        $this->ID = $id;
        $this->wpUser = new WP_User($id);
        $this->aiosc_role = $this->get_aiosc_role();
    }
    private function get_aiosc_role() {
        global $aiosc_capabilities;
        if(is_array($this->wpUser->roles)) {
            foreach($this->wpUser->roles as $i=>$role) {
                if(array_key_exists($role,$aiosc_capabilities->get_roles()))
                    return $role;
            }
            return '';
        }
        return '';
    }
    public function set_role($aiosc_role) {
        global $aiosc_capabilities;
        if(empty($aiosc_role) || !$aiosc_capabilities->role_exists($aiosc_role)) return false;
        if($this->aiosc_role == $aiosc_role) return true;
        if(is_array($this->wpUser->roles)) {
            foreach($this->wpUser->roles as $i=>$role) {
                if(array_key_exists($role,$aiosc_capabilities->get_roles()))
                    $this->wpUser->remove_role($role);
            }
        }
        $this->wpUser->add_role($aiosc_role);
    }
    public function has_aiosc_role($role_id='') {
        global $aiosc_capabilities;
        if(empty($this->aiosc_role)) return false;
        if(empty($role_id)) return true;
        else {
            return $this->aiosc_role == $role_id;
        }
    }

    /**
     * Get all departments associated with this user.
     * If nothing is found, returns FALSE
     * @param bool $objects - return departments as objects or only array of IDs
     * @param array $exclude - ID of departments to exclude from search
     * @return array|bool
     */
    public function get_departments($objects=true, $exclude=array()) {
        if(!$this->can('staff')) return false;
        $all_deps = aiosc_DepartmentManager::get_departments(false,true);
        $my_deps = array();
        foreach($all_deps as $ad) {
            if(!in_array($ad->ID,$exclude) && in_array($this->ID, array_values($ad->operators))) $my_deps[] = $objects?$ad:$ad->ID;
        }
        if(!empty($my_deps)) return $my_deps;
        else return false;
    }
    /**
     * Check if user can perform specific action based on his AOISC role
     * It can be extended by using 'aiosc_capability_check' filter so extensions can check if user can do specific action with custom role.
     *
     * @filter aiosc_capability_check
     *
     * @param $capability
     * @param mixed $data - Optional. It can contain action-specific data such as ticket ID, for example if we are checking if user
     * can answer specific ticket, we could use $capability = "answer_ticket", $data = array('ticket_id'=>TICKET_ID).
     * @return bool
     */
    function can($capability, $data=null) {
        global $aiosc_capabilities, $aiosc_settings;
        if($this->aiosc_role == '') return false; //no AIOSC role? Cannot perform action.
        $caps = @$aiosc_capabilities->get_roles();
        $caps = $caps[$this->aiosc_role]['caps'];
        if(!isset($caps[$capability])) return false;

        if(isset($data['reply_id'])) {
            $reply = aiosc_is_reply($data['reply_id'])?$data['reply_id']:new aiosc_Reply($data['reply_id']);
            if(aiosc_is_reply($reply))
                $ticket = new aiosc_Ticket($reply->ticket_id);
        }
        else $reply = false;

        if(isset($data['ticket_id'])) $ticket = aiosc_is_ticket($data['ticket_id'])?$data['ticket_id']:new aiosc_Ticket($data['ticket_id']);
        else $ticket = false;


        if(isset($data['file_id'])) $attachment = aiosc_is_attachment($data['file_id'])?$data['file_id']:new aiosc_Attachment($data['file_id']);
        else $attachment = false;

        if ($capability == 'create_ticket') {
            return @$caps[$capability] === true;
        }
        elseif($capability == 'answer_ticket') {
            if(!aiosc_is_ticket($ticket)) return false;
            if(@$caps['answer_tickets'] === true) return true; //answer_tickets (not answer_ticket) is available to aiosc_admin role only
            if($ticket->op_id == $this->ID) return true;
            $mydeps = $this->get_departments(false);
            if(is_array($mydeps) && in_array($ticket->department_id, $mydeps)) return true;
            return false;
        }
        elseif($capability == 'edit_ticket') {
            if(!aiosc_is_ticket($ticket)) return false;
            if(@$caps['edit_tickets'] === true) return true; //edit_tickets (not edit_ticket) is available to aiosc_admin role only
            if(@$caps['edit_ticket'] !== true) return false;
            if($ticket->op_id != $this->ID) return false;
            return true;
        }
        elseif($capability == 'reply_ticket') {
            if(!aiosc_is_ticket($ticket)) return false;
            if(@$caps['reply_tickets'] == true) return true; //reply_tickets (not reply_ticket) is available to aiosc_admin role only
            if(@$caps['reply_ticket'] != true) return false;
            if($ticket->status == 'closed') return false;
            if($ticket->op_id == $this->ID) return true;
            if($ticket->author_id == $this->ID) return true;
            $mydeps = $this->get_departments(false);
            if(is_array($mydeps) && in_array($ticket->department_id, $mydeps)) return true;
            return false;
        }
        elseif($capability == 'view_ticket') {
            if(!aiosc_is_ticket($ticket)) return false;
            if(@$caps['view_tickets'] === true ) return true; //view_tickets (not view_ticket) is available to aiosc_admin role only
            if(@$caps['view_ticket'] !== true) return false;
            if($ticket->is_public) return true;
            if($ticket->op_id != $this->ID && $ticket->author_id != $this->ID) return false;
            return true;
        }
        elseif($capability == 'reopen_ticket') {
            if(!$this->can('staff') && !$aiosc_settings->get('allow_reopen_tickets')) return false;
            if(!aiosc_is_ticket($ticket)) return false;
            if($ticket->status != 'closed') return false;
            if(@$caps['reopen_tickets'] === true) return true;
            if(@$caps['reopen_ticket'] !== true) return false;
            if($this->can('staff')) return true;
            if($this->ID == $ticket->author_id) return true;
            return false;
        }
        elseif($capability == 'delete_ticket') {
            if(!aiosc_is_ticket($ticket)) return false;
            if(@$caps['delete_tickets'] === true) return true; //delete_tickets (not delete_ticket) is available to aiosc_admin role only
            if($ticket->op_id != $this->ID) return false;
            return true;
        }
        elseif($capability == 'delete_reply') {
            if(!aiosc_is_ticket($ticket) || !aiosc_is_reply($reply)) return false;
            if(@$caps['delete_replies'] === true) return true; //delete_replies (not delete_reply) is available to aiosc_admin role only
            if(!$this->can('edit_ticket',array('ticket_id'=>$ticket))) return false;
            return true;
        }
        elseif($capability == 'request_ticket_closure') {
            if(!aiosc_is_ticket($ticket)) return false;
            if($ticket->status == 'closed') return false;
            if($ticket->closure_requested) return false;
            if($ticket->author_id == $this->ID) return true;
            return false;
        }
        elseif($capability == 'close_ticket') {
            if(!aiosc_is_ticket($ticket)) return false;
            if(@$caps['close_tickets'] === true) return true; //close_tickets (not close_ticket) is available to aiosc_admin role only
            if($ticket->op_id == $this->ID) return true;
            return false;
        }
        elseif($capability == 'leave_feedback') {
            if(!aiosc_is_ticket($ticket)) return false;
            if($ticket->author_id != $this->ID) return false;
            if($ticket->status != 'closed') return false;
            return true;
        }
        elseif($capability == 'download_file') {
            if(!aiosc_is_attachment($attachment)) return false;
            if(!aiosc_is_ticket($ticket)) return false;

            if(@$caps['download_files'] === true) return true; //download_files (not download_file) is specific roles only
            if($this->can('reply_ticket',array('ticket_id',$ticket))) {
                if(@$caps['staff'] == true) return true;
                else return $aiosc_settings->get('allow_download') ? true : false;
            }
            else return true;
        }
        elseif($capability == 'manage_options') {
            return isset($caps[$capability]) && $caps[$capability] == true;
        }
        elseif($capability == 'manage_customers') { //not implemented yet
            return isset($caps[$capability]) && $caps[$capability] == true;
        }
        else {
            //for custom capabilities
            return apply_filters('aiosc_capability_check', @$caps[$capability] === true, $capability, $data);
        }
    }
    function aiosc_meta() {
        return array(
            'aiosc_notifications'=>1,
            'aiosc_department_notifications'=>1, //whether to receive email-templates even if ticket is not assigned to him, but is in the same department
            'aiosc_staff_create_form_disable'=>1
        );
    }

    /**
     * Get user meta.
     * However, if meta is not set, function will try to return
     * AIOSC_META default value based on $key, otherwise, it will return $default string
     *
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    function get_meta($key,$default='') {
        $data = get_user_meta($this->ID,$key,true);
        if( $data !== '') return $data;
        else {
            $mymeta = $this->aiosc_meta();
            if(array_key_exists($key, $mymeta)) return $mymeta[$key];
            else return $default;
        }
    }
    function set_meta($key, $value) {
        update_user_meta($this->ID, $key, $value);
    }
    /** Magic */
    function __isset( $key ) {
        return isset( $this->wpUser->$key );
    }
    function __get( $key ) {
        $value = '';
        if ( isset( $this->wpUser->$key ) )
            $value = $this->wpUser->$key;

        return $value;
    }
    function __set( $key, $value ) {
        $this->wpUser->$key = $value;
    }
    /* Install */
    function install_meta() {
        $mymeta = $this->aiosc_meta();
        foreach($mymeta as $k=>$v) {
            //only add those that are not present, do not update
            add_user_meta($this->ID, $k, $v, true);
        }
    }
    function uninstall_meta() {
        $mymeta = $this->aiosc_meta();
        foreach($mymeta as $k=>$v) {
            delete_user_meta($this->ID, $k, $v);
        }
    }
}

/**
 * Initialize current user on WP init, instead of calling @get_current_user_id() it immediately.
 * This is to prevent conflict with bbPress and other plugins.
 * @uses get_current_user_id()
 * @since 1.0.8
 */
global $aiosc_user;
function aiosc_init_aiosc_user() {
    global $aiosc_user;
    $aiosc_user = new aiosc_User(get_current_user_id());
}
add_action('init','aiosc_init_aiosc_user');

/**
 * Check if given object is type of aiosc_User and not empty
 * @param $object
 * @return bool
 */
function aiosc_is_user($object) {
    if($object === false || empty($object) || $object == null) return false;
    if(is_a($object,'aiosc_User') && is_numeric($object->ID) && $object->ID > 0 && $object->wpUser != '' && $object->has_aiosc_role()) return true;
    return false;
}
