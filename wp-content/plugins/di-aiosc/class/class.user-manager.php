<?php
class aiosc_UserManager {
    /**
     * Returns an array of ID's of users with specific AIOSC capability.
     * @param $capability - name of capability
     * @param bool $objects - if TRUE, an array of aiosc_User objects will be returned
     * @return array
     */
    static function get_users_with_capability($capability, $objects = false) {
        global $aiosc_capabilities;
        $users = array();
        $roles = $aiosc_capabilities->get_roles();
        foreach($roles as $role=>$data) {
            if(array_key_exists($capability,$data['caps']) && $data['caps'][$capability] != false) {
                $usrs = get_users(array('role'=>$role));
                foreach($usrs as $user) {
                    if(!in_array($user->ID, $users)) $users[] = $user->ID;
                }
            }
        }
        if($objects) {
            $objs = array();
            foreach($users as $user) {
                $objs[] = new aiosc_User($user);
            }
            return $objs;
        }
        return $users;
    }
    static function get_free_operator(aiosc_Department $department, $new_ops = array(), $exclude = array()) {
        if(is_numeric($department)) $department = new aiosc_Department($department);
        if(!aiosc_is_department($department)) return 0;
        if(empty($new_ops)) $new_ops = $department->get_ops(false);
        if(!is_array($new_ops) || empty($new_ops)) return 0;
        if(!empty($exclude) && is_array($exclude)) {
            foreach($exclude as $ind) {
                if(isset($new_ops[$ind]))
                    unset($new_ops[$ind]);
            }
        }
        if(empty($new_ops)) return 0;
        global $wpdb;
        $where = '';
        foreach($new_ops as $nop) {
            if(empty($where)) $where .= " WHERE op_id = $nop ";
            else $where .= "OR op_id = $nop ";
        }
        $cnt = count($new_ops);
        $free_ops = $wpdb->get_results("SELECT op_id as ID,COUNT(*) as cnt FROM `".aiosc_get_table(aiosc_tables::tickets)."` $where GROUP BY op_id ORDER BY 2 ASC LIMIT 0,$cnt", 0 , 0);
        //if there are unused operators, use them!
        if(count($free_ops) != count($new_ops)) {
            foreach($free_ops as $k=>$fo) {
                if(in_array($fo->ID,$new_ops)) {
                    unset($new_ops[array_search($fo->ID,array_values($new_ops))]);
                }
            }
            $new_ops = array_values($new_ops); //reset array keys
            $free_op = $new_ops[rand(0,count($new_ops) - 1)];
        }
        //no unused? use first from query then
        else {
            $free_op = @$free_ops[0]->ID;
        }
        if($free_op < 1) {
            //nothing found, then get random operator from department
            $free_op = $department->operators[rand(0, count($department->operators) - 1)];
            return $free_op;
        }
        else {
            return $free_op;
        }
    }

    /**
     * Fired when admin deletes WP user, right before user gets deleted
     * so we can do stuff with user's content, transfer his tickets to someone else etc.
     *
     * @hook delete_user
     *
     * @param $user_id
     * @param null $reassign_id
     */
    static function delete_user($user_id, $reassign_id = null ) {
        $reassign = $reassign_id == null?false:new aiosc_User($reassign_id);
        $user = new aiosc_User($user_id);
        if(!aiosc_is_user($user)) return;

        //remove deleted user from Departments if he's staff member
        if($user->can('staff')) {
            $u_deps = $user->get_departments(true);
            if(is_array($u_deps)) {
                foreach($u_deps as $u_dep) {
                    $u_dep->remove_operator($user->ID);
                }
            }
        }
        //delete everything if no reassign user is passed
        if(!aiosc_is_user($reassign)) {
            //delete tickets owned by removed user
            $tickets = aiosc_TicketManager::get_tickets(aiosc_TicketManager::get_ticket_query(array('author_id'=>$user_id)));
            if(is_array($tickets))
                aiosc_TicketManager::delete_tickets($tickets, true);
        }
        else {
            //change author of Premade Responses
            if($user->can('staff')) {
                aiosc_PremadeResponseManager::transfer_responses($user, $reassign);
            }

            //change author to reassign user
            $tickets = aiosc_TicketManager::get_tickets(aiosc_TicketManager::get_ticket_query(array('author_id'=>$user_id)));
            if(is_array($tickets)) {
                foreach($tickets as $ticket) {
                    $ticket->set_author($reassign);
                }
            }
            //change operator to reassign user (if deleted user was staff member and had tickets assigned to him)
            $tickets = aiosc_TicketManager::get_tickets(aiosc_TicketManager::get_ticket_query(array('op_id'=>$user_id)));
            if(is_array($tickets)) {
                $r_deps = $reassign->get_departments(false);
                foreach($tickets as $ticket) {
                    if($reassign->can('staff')) {
                        if(!is_array($r_deps) || (is_array($r_deps) && empty($r_deps)) || !in_array($ticket->department_id,$r_deps)) {
                            $dep = new aiosc_Department($ticket->department_id);
                            $dep->add_operator($reassign->ID);
                        }
                        $ticket->set_department($ticket->department_id, $reassign->ID);
                    }
                    else
                        $ticket->set_department($ticket->department_id);
                }
            }
        }
    }
    static function install_meta() {
        update_user_meta(get_current_user_id(),'aiosc_notifications',1);
    }
}
