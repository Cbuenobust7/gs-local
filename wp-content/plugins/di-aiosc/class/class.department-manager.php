<?php

/**
 * Class aiosc_DepartmentManager
 * @last-update 1.0.1
 */
class aiosc_DepartmentManager {

    /**
     * Creates new department or updates existing one (if $id is passed)
     * @updated 1.0.1
     *
     * @hook aiosc_before_department_creation
     * @hook aiosc_before_department_update
     * @hook aiosc_after_department_creation ($id) is fired when department is created. It passes the ID of newly created department
     * @hook aiosc_after_department_update ($id) is fired when existing department is updated. It passes the ID of updated department
     *
     * @param int $id
     * @param $name
     * @param string $desc
     * @param array $ops
     * @param bool $active
     * @param bool $bypass_user_check - used when we create department programmatically (on installation for example)
     * @return mixed
     */
    static function update_department($id = 0, $name, $desc='', $ops = array(), $active = true, $bypass_user_check = false) {
        global $aiosc_user;
        $dep = new aiosc_Department($id);
        if(!$bypass_user_check && !$aiosc_user->can('manage_options')) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
        if(empty($name)) return aiosc_response(0,__('<strong>Error:</strong> Department name is missing.','aiosc'));

        if(aiosc_is_department($dep)) {
            if($name != $dep->name && !self::is_valid_name($name)) return aiosc_response(0,__('<strong>Error:</strong> Department name already exists. Please choose another one.','aiosc'));
        }
        else {
            if(!self::is_valid_name($name)) return aiosc_response(0,__('<strong>Error:</strong> Department name already exists. Please choose another one.','aiosc'));
        }

        if(count($ops) < 1 && $active == true) {
            return aiosc_response(0,__('<strong>Error:</strong> In order to be <strong>active</strong>, department must have at least one active operator assigned to it.','aiosc'));
        }
        $errors = array();

        if($dep === false) $errors = apply_filters('aiosc_before_department_creation',$errors);
        else $errors = apply_filters('aiosc_before_department_update',$errors, $id);

        if(!empty($errors)) return aiosc_response(0,implode('<br>',$errors));

        $name = esc_sql($name);
        $desc = esc_sql($desc);
        $active = aiosc_boolToEnum($active);

        for($i=0;$i<count($ops);$i++) {
            if(!is_numeric($ops[$i])) unset($ops[$i]);
        }
        $ops = !empty($ops)?serialize($ops):'';
        global $wpdb;
        if(!aiosc_is_department($dep)) {
            //create
            $q = $wpdb->query("INSERT INTO `".aiosc_get_table(aiosc_tables::departments)."`
            (name, description, operators, is_active) VALUES('$name','$desc','$ops','$active')");

            $id = $wpdb->insert_id;
            $dep = new aiosc_Department($id);
            do_action('aiosc_after_department_creation',$dep);

            return aiosc_response(1,sprintf(__('Department <strong>&quot;%s&quot;</strong> has been created.','aiosc'),$name), array('department_id'=>$id));
        }
        else {
            //update
            $q = $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::departments)."`
            SET name='$name', description='$desc', operators='$ops', is_active='$active' WHERE ID=".(int)$id);

            do_action('aiosc_after_department_update',$dep);
            return aiosc_response(1,sprintf(__('Department <strong>&quot;%s&quot;</strong> has been updated.','aiosc'),$name), array('department_id'=>$id));
        }
    }

    /**
     * Gets list of departments
     * @param bool $active_only - if true, function returns only ACTIVE departments
     * @param bool $objects - if true, function returns array of aiosc_Department objects, otherwise an array of IDs will be returned.
     * @param string $orderby - order results by column
     * @param string $order - ASC or DESC
     * @return array|bool
     */
    static function get_departments($active_only=false, $objects=true, $orderby="name", $order = "asc") {
        global $wpdb;
        $active = $active_only?' WHERE is_active="Y"':'';

        $order = $order == 'asc' ? 'asc' : 'desc';
        $cols = array('ID', 'name', 'description', 'is_active', 'operators', 'meta', 'date_created');
        if(!in_array($orderby, $cols)) $orderby = 'name';

        $q = $wpdb->get_results("SELECT ID FROM `".aiosc_get_table(aiosc_tables::departments)."`".$active." ORDER BY $orderby $order");
        if($q) {
            $deps = array();
            foreach($q as $d) {
                $deps[] = $objects?new aiosc_Department($d->ID):$d->ID;
            }
            if(!empty($deps)) return $deps;
            else return false;
        }
        else return false;
    }
    /**
     * Check if department with this name already exist.
     * @param $name
     * @return bool - TRUE if name doesn't exist already, FALSE otherwise.
     */
    static function is_valid_name($name) {
        global $wpdb;
        if(empty($name)) return false;
        if(strlen($name) > 255) return false;
        $name = strtolower($name);
        $name = esc_sql($name);
        $q = $wpdb->get_var("SELECT COUNT(*) FROM `".aiosc_get_table(aiosc_tables::departments)."` WHERE LOWER(name)='$name'", 0, 0);
        return $q < 1;
    }

    /**
     * Deletes an department and updates related tickets and operators
     * @param $id
     * @param int $move_data_to - New department ID. If 0, tickets will be removed, otherwise tickets and operators will be assigned to new department
     * @return aiosc_response string
     */
    static function delete_department($id, $move_data_to=0) {
        global $aiosc_user, $wpdb;
        if(!$aiosc_user->can('manage_options')) return aiosc_response(0,AIOSC_PERMISSION_ERROR);

        if($id == $move_data_to) return aiosc_response(0,__('<strong>Error:</strong> Department you are moving data to cannot be the same as the one you are trying to delete.','aiosc'));
        $dep = new aiosc_Department($id);
        if(!aiosc_is_department($dep)) return aiosc_response(0,sprintf(__('<strong>Error:</strong> Department with ID <code>%d</code> does not exist.','aiosc'),$id));

        $tickets = aiosc_TicketManager::get_tickets(aiosc_TicketManager::get_ticket_query(array('department_id'=>$id)));
        $ops = $dep->get_ops(true);

        $new_dep = new aiosc_Department($move_data_to);

        if(($tickets !== false || $ops !== false) && $move_data_to != 0 && !aiosc_is_department($new_dep)) return aiosc_response(0,__('<strong>Error:</strong> Department you are moving data to does not exist.','aiosc'));

        $tickets_changed = 0;
        $ops_moved = 0;
        foreach($tickets as $ticket) {
            if(aiosc_is_department($new_dep)) {
                $ticket->set_department($new_dep);
            }
            else {
                $wpdb->query("DELETE FROM `".aiosc_get_table(aiosc_tables::tickets)."` WHERE ID=$ticket->ID");
            }
            $tickets_changed++;
        }
        if(is_array($ops) && count($ops) > 0) {
            foreach($ops as $op) {
                if(aiosc_is_department($new_dep)) {
                    $new_dep->add_operator($op->ID);
                    $ops_moved++;
                }
            }
        }
        $wpdb->query("DELETE FROM `".aiosc_get_table(aiosc_tables::departments)."` WHERE ID=$dep->ID");
        if(aiosc_is_department($new_dep))
            return aiosc_response(1,sprintf(__('Department &quot;%s&quot; was deleted successfully. Total of %d tickets and %d Operators were moved to &quot;%s&quot; department.','aiosc'),
                $dep->name, $tickets_changed, $ops_moved, @$new_dep->name));
        else
            return aiosc_response(1,sprintf(__('Department &quot;%s&quot; was deleted successfully.','aiosc'),$dep->name));

    }
    static function activate_departments($ids, $activate=true) {
        global $aiosc_user, $wpdb;
        if(!$aiosc_user->can('manage_options')) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
        $activate = aiosc_boolToEnum($activate);
        $y = 0;
        foreach($ids as $id) {
            $id = (int)$id;
            global $wpdb;
            $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::departments)."` SET is_active='$activate' WHERE ID=$id");
            $y++;
        }
        if($y > 0) {
            if(aiosc_enumToBool($activate) === true)
                return aiosc_response(1,sprintf(__('Total of %d departments were activated successfully.','aiosc'),$y));
            else
                return aiosc_response(1,sprintf(__('Total of %d departments were deactivated successfully.','aiosc'),$y));
        }
        else return aiosc_response(0,__('No departments were selected so no action was taken.','aiosc'));
    }

    /** Install */
    /**
     * Create one department at the plugin activation, but only if
     * there are no other departments created before
     */
    static function install() {
        global $wpdb;
        $deps = self::get_departments(false,false);
        if(!is_array($deps)) {
            $ops = aiosc_UserManager::get_users_with_capability('staff', false);
            self::update_department(0,
                __('General Questions','aiosc'),
                __('This is default department created on AIOSC installation.','aiosc'),
                is_array($ops)?$ops:array(),
                is_array($ops),
                true
            );
        }
    }
}
