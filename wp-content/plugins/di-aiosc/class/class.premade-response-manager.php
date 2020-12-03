<?php
class aiosc_PremadeResponseManager {
    static function update_response($id = 0, $name, $content, $is_shared = true) {
        global $aiosc_user;
        if(!$aiosc_user->can('staff')) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
        if(empty($name)) return aiosc_response(0,__('<strong>Error:</strong> Pre-made Response must have a name.','aiosc'));

        $pr = new aiosc_PremadeResponse($id);

        if(aiosc_is_premade_response($pr)) {
            if($pr->author_id != $aiosc_user->ID) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
            else {
                if($name != $pr->name && !self::is_valid_name($name, $aiosc_user->ID))
                    return aiosc_response(0,sprintf(__('<strong>Error:</strong> Pre-made Response with &quot;%s&quot; name already exists. To avoid confusion later, please choose unique name.','aiosc'),$name));
            }
        }
        else {
            if(!self::is_valid_name($name, $aiosc_user->ID))
                return aiosc_response(0,sprintf(__('<strong>Error:</strong> Pre-made Response with &quot;%s&quot; name already exists. To avoid confusion later, please choose unique name.','aiosc'),$name));
        }
        $name = esc_sql($name);
        $content = esc_sql($content);
        $is_shared = aiosc_boolToEnum($is_shared);

        global $wpdb;
        if(!aiosc_is_premade_response($pr)) {
            //create
            $now = current_time('mysql');
            $q = $wpdb->query("INSERT INTO `".aiosc_get_table(aiosc_tables::premade_responses)."`
            (name, content, author_id, is_shared, date_created) VALUES('$name','$content',$aiosc_user->ID,'$is_shared','$now')");

            $id = $wpdb->insert_id;

            do_action('aiosc_premade_response_creation',$id);

            return aiosc_response(1,sprintf(__('Pre-made Response <strong>&quot;%s&quot;</strong> has been created.','aiosc'),$name), array('response_id'=>$id));
        }
        else {
            //update
            $q = $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::premade_responses)."`
            SET name='$name', content='$content', is_shared='$is_shared' WHERE ID=".(int)$id);

            do_action('aiosc_premade_response_update',$id);
            return aiosc_response(1,sprintf(__('Pre-made Response <strong>&quot;%s&quot;</strong> has been updated.','aiosc'),$name), array('response_id'=>$id));
        }
    }
    static function is_valid_name($name, $uid) {
        global $wpdb;
        if(empty($name)) return false;
        if(strlen($name) > 255) return false;
        $name = strtolower($name);
        $name = esc_sql($name);
        $uid = esc_sql($uid);
        $q = $wpdb->get_var("SELECT COUNT(*) FROM `".aiosc_get_table(aiosc_tables::premade_responses)."` WHERE author_id = $uid AND LOWER(name)='$name'", 0, 0);
        return $q < 1;
    }
    static function get_responses($shared=false, $objects=true) {
        global $aiosc_user, $wpdb;
        $shared = $shared?' WHERE is_shared="Y" OR author_id='.$aiosc_user->ID:' WHERE author_id='.$aiosc_user->ID;
        $q = $wpdb->get_results("SELECT * FROM `".aiosc_get_table(aiosc_tables::premade_responses)."`".$shared);
        if($q) {
            $pris = array();
            foreach($q as $p) {
                $pris[] = $objects?new aiosc_PremadeResponse($p->ID, $p):$p->ID;
            }
            return $pris;
        }
        else return false;
    }
    static function remove($ids=array()) {
        global $aiosc_user, $wpdb;
        if(!$aiosc_user->can('staff')) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
        $deleted = 0;
        if(is_array($ids)) {
            foreach($ids as $id) {
                if(is_numeric($id)) $pr = new aiosc_PremadeResponse($id);
                else $pr = $id;
                if(!aiosc_is_premade_response($pr)) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
                if($pr->author_id != $aiosc_user->ID) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
                $wpdb->query("DELETE FROM `".aiosc_get_table(aiosc_tables::premade_responses)."` WHERE ID=$pr->ID");
                $deleted++;
            }
        }
        if($deleted != 1)
            return aiosc_response(1,sprintf(__('Total of <code>%s</code> Pre-made Responses were deleted successfully.','aiosc'),$deleted));
        else
            return aiosc_response(1,__('Pre-made Response was deleted successfully.','aiosc'));
    }

    /**
     * Change ownership of pre-made responses from old owner to new owner
     *
     * @param $from_user - old owner
     * @param $to_user - new owner
     * @return bool
     */
    static function transfer_responses($from_user, $to_user) {
        global $wpdb;;
        if(is_numeric($from_user)) $from_user = new aiosc_User($from_user);
        if(is_numeric($to_user)) $to_user = new aiosc_User($to_user);
        if((!aiosc_is_user($from_user) || $from_user->can('staff')) ||
            (!aiosc_is_user($to_user) || !$to_user->can('staff'))) return false;
        if($from_user->ID == $to_user->ID) return true;
        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::premade_responses)."` SET author_id=$to_user->ID WHERE author_id=$from_user->ID");
        return true;
    }
    static function sharing($ids=array(),$share = true) {
        global $aiosc_user, $wpdb;
        if(!$aiosc_user->can('staff')) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
        $processed = 0;
        if(is_array($ids)) {
            foreach($ids as $id) {
                if(is_numeric($id)) $pr = new aiosc_PremadeResponse($id);
                else $pr = $id;
                if(!aiosc_is_premade_response($pr)) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
                if($pr->author_id != $aiosc_user->ID) return aiosc_response(0,AIOSC_PERMISSION_ERROR);
                $shared = aiosc_boolToEnum($share);
                $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::premade_responses)."` SET is_shared='$shared' WHERE ID=$pr->ID");
                $processed++;
            }
        }
        if($processed != 1) {
            if($share)
                return aiosc_response(1,sprintf(__('Total of <code>%s</code> Pre-made Responses are shared.','aiosc'),$processed));
            else
                return aiosc_response(1,sprintf(__('Total of <code>%s</code> Pre-made Responses are made private.','aiosc'),$processed));
        }

        else {
            if($share)
                return aiosc_response(1,__('Pre-made Response is shared.','aiosc'));
            else
                return aiosc_response(1,__('Pre-made Response is made private.','aiosc'));
        }
    }
}