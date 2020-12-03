<?php
class aiosc_PriorityManager {

    /**
     * Creates new priority or updates existing one (if $id is passed)
     *
     * @hook aiosc_priority_creation ($id) is fired when priority is created. It passes the ID of newly created priority
     * @hook aiosc_priority_update ($id) is fired when existing priority is updated. It passes the ID of updated priority
     *
     * @param int $id
     * @param $name
     * @param string $desc
     * @param int $level
     * @param string $color
     * @param bool $active
     * @return string
     */
    static function update_priority($id = 0, $name, $desc='', $level = 0, $color = null, $active = true, $bypass_user_check=false) {
        global $aiosc_user;
        if(!$bypass_user_check && !$aiosc_user->can('manage_options')) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
        if(empty($name)) return aiosc_response(0,__('<strong>Error:</strong> Priority name is missing.','aiosc'));

        $pri = new aiosc_Priority($id);

        if(aiosc_is_priority($pri)) {
            if($name != $pri->name && !self::is_valid_name($name)) return aiosc_response(0,__('<strong>Error:</strong> Priority name already exists. Please choose another one.','aiosc'));
        }
        else {
            if(!self::is_valid_name($name)) return aiosc_response(0,__('<strong>Error:</strong> Priority name already exists. Please choose another one.','aiosc'));
        }
        $errors = array();
        if($pri === false) $errors = apply_filters('aiosc_before_priority_creation',$errors);
        else $errors = apply_filters('aiosc_before_priority_update',$errors, $pri);

        $name = esc_sql($name);
        $desc = esc_sql($desc);
        $active = aiosc_boolToEnum($active);
        $level = (int)$level;
        $color = strlen($color) != 7?'':$color; //# + 6 characters

        global $wpdb;
        if(!aiosc_is_priority($pri)) {
            //create
            $q = $wpdb->query("INSERT INTO `".aiosc_get_table(aiosc_tables::priorities)."`
            (name, description, level, color, is_active) VALUES('$name','$desc','$level','$color','$active')");

            $id = $wpdb->insert_id;

            do_action('aiosc_after_priority_creation',$id);

            return aiosc_response(1,sprintf(__('Priority <strong>&quot;%s&quot;</strong> has been created.','aiosc'),$name), array('priority_id'=>$id));
        }
        else {
            //update
            $q = $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::priorities)."`
            SET name='$name', description='$desc', level='$level', color='$color', is_active='$active' WHERE ID=".(int)$id);

            $tickets = aiosc_TicketManager::get_tickets(aiosc_TicketManager::get_ticket_query(array('priority_id'=>$id)));
            if(!empty($tickets) && is_array($tickets)) {
                foreach($tickets as $ticket) {
                    $ticket->set_priority($id);
                }
            }

            do_action('aiosc_after_priority_update',$id);
            return aiosc_response(1,sprintf(__('Priority <strong>&quot;%s&quot;</strong> has been updated.','aiosc'),$name), array('priority_id'=>$id));
        }
    }

    /**
     * Gets list of priorities
     * @param bool $active_only - if true, function returns only ACTIVE departments
     * @param $objects - if true, returns array of aiosc_Priority objects
     * @param string $orderby - order results by column
     * @param string $order - ASC or DESC
     * @return array|bool
     */
    static function get_priorities($active_only=false, $objects=true, $orderby="level", $order = "asc") {
        global $wpdb;
        $active = $active_only?' WHERE is_active="Y"':'';

        $order = $order == 'asc' ? 'asc' : 'desc';
        $cols = array('ID', 'name', 'description', 'is_active', 'level', 'meta', 'color', 'date_created');
        if(!in_array($orderby, $cols)) $orderby = 'level';

        $q = $wpdb->get_results("SELECT * FROM `".aiosc_get_table(aiosc_tables::priorities)."`".$active. " ORDER BY $orderby $order");
        if($q) {
            $pris = array();
            foreach($q as $p) {
                $pris[] = $objects?new aiosc_Priority($p->ID, $p):$p->ID;
            }
            if(!empty($pris))
                return $pris;
            else return false;
        }
        else return false;
    }
    /**
     * Check if priority with this name already exist.
     * @param $name
     * @return bool - TRUE if name doesn't exist already, FALSE otherwise.
     */
    static function is_valid_name($name) {
        global $wpdb;
        if(empty($name)) return false;
        if(strlen($name) > 255) return false;
        $name = strtolower($name);
        $name = esc_sql($name);
        $q = $wpdb->get_var("SELECT COUNT(*) FROM `".aiosc_get_table(aiosc_tables::priorities)."` WHERE LOWER(name)='$name'", 0, 0);
        return $q < 1;
    }

    /**
     * Deletes an priority and updates related tickets
     * @param $id
     * @param int $move_data_to - New priority ID. If 0, tickets will be removed, otherwise will be assigned to new priority
     * @return aiosc_response string
     */
    static function delete_priority($id, $move_data_to=0) {
        global $aiosc_user, $wpdb;
        if(!$aiosc_user->can('manage_options')) return aiosc_response(0,AIOSC_PERMISSION_ERROR);

        if($id == $move_data_to) return aiosc_response(0,__('<strong>Error:</strong> Priority you are moving data to cannot be the same as the one you are trying to delete.','aiosc'));
        $pri = new aiosc_Priority($id);
        if(!aiosc_is_priority($pri)) return aiosc_response(0,sprintf(__('<strong>Error:</strong> Priority with ID <code>%d</code> does not exist.','aiosc'),$id));

        $tickets = aiosc_TicketManager::get_tickets(aiosc_TicketManager::get_ticket_query(array('priority_id'=>$id)));

        $new_pri = new aiosc_Priority($move_data_to);

        if($tickets !== false && $move_data_to != 0 && !aiosc_is_priority($new_pri)) return aiosc_response(0,__('<strong>Error:</strong> Priority you are moving data to does not exist.','aiosc'));

        $tickets_changed = 0;
        $ops_moved = 0;
        foreach($tickets as $ticket) {
            if(aiosc_is_priority($new_pri)) {
                $ticket->set_priority($new_pri);
            }
            else {
                $wpdb->query("DELETE FROM `".aiosc_get_table(aiosc_tables::tickets)."` WHERE ID=$ticket->ID");
            }
            $tickets_changed++;
        }
        $wpdb->query("DELETE FROM `".aiosc_get_table(aiosc_tables::priorities)."` WHERE ID=$pri->ID");
        if(aiosc_is_priority($new_pri))
            return aiosc_response(1,sprintf(__('Priority &quot;%s&quot; was deleted successfully. Total of %d tickets were moved to &quot;%s&quot; priority.','aiosc'),
                $pri->name, $tickets_changed, @$new_pri->name));
        else
            return aiosc_response(1,sprintf(__('Priority &quot;%s&quot; was deleted successfully.','aiosc'),$pri->name));
    }
    static function activate_priorities($ids, $activate=true) {
        global $aiosc_user, $wpdb;
        if(!$aiosc_user->can('manage_options')) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
        $activate = aiosc_boolToEnum($activate);
        $y = 0;
        foreach($ids as $id) {
            $id = (int)$id;
            global $wpdb;
            $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::priorities)."` SET is_active='$activate' WHERE ID=$id");
            $y++;
        }
        if($y > 0) {
            if(aiosc_enumToBool($activate) === true)
                return aiosc_response(1,sprintf(__('Total of %d priorities were activated successfully.','aiosc'),$y));
            else
                return aiosc_response(1,sprintf(__('Total of %d priorities were deactivated successfully.','aiosc'),$y));
        }
        else return aiosc_response(0,__('No priorities were selected so no action was taken.','aiosc'));
    }

    /** Install */
    /**
     * Create one priority at the plugin activation, but only if
     * there are no other priorities created before
     */
    static function install() {
        global $wpdb;
        $objs = self::get_priorities(false,false);
        if(!is_array($objs)) {
            self::update_priority(0,
                __('No Priority','aiosc'),
                __('This is default priority created on AIOSC installation.','aiosc'),
                0,null,true,
                true);
        }
    }
}
