<?php
/**
 * Class aiosc_Department
 * @last-update 1.0.1
 */
class aiosc_Department {
    public $ID;
    public $data;
    function __construct($id, $data='') {
        $this->ID = $id;
        $this->data = !empty($data)?$data:$this->get_department();
        if($this->data !== false) {
            $this->data->operators = unserialize($this->data->operators);
            $this->data->is_active = aiosc_enumToBool($this->data->is_active);
            $this->data->name = aiosc_clean_content($this->data->name,"",false);
            $this->data->description = aiosc_clean_content($this->data->description);
            /** @var meta
             * @since 1.0.1
             */
            $this->data->meta = (!empty($this->data->meta)) ? unserialize($this->data->meta) : false;
        }
    }
    private function get_department() {
        if(!empty($this->ID) && $this->ID > 0) {
            global $wpdb;
            $q = "SELECT * FROM `".aiosc_get_table(aiosc_tables::departments)."` WHERE ID=".(int)$this->ID;
            $data = $wpdb->get_results($q);
            if(!$data || !is_array($data)) return false;
            else return $data[0];
        }
        return false;
    }

    /**
     * @since 1.0.1
     * Save meta data
     */
    public function save_meta() {
        global $wpdb;
        if($this->data->meta !== false)
            $meta = serialize($this->data->meta);
        else
            $meta = '';

        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::departments)."` SET meta='$meta' WHERE ID=".$this->ID);
    }

    /**
     * @since 1.0.1
     */
    public function load_meta() {
        global $wpdb;
        $q = "SELECT meta FROM `".aiosc_get_table(aiosc_tables::departments)."` WHERE ID=".$this->ID;
        $data = $wpdb->get_var($q);
        $this->data->meta = !empty($data) ? unserialize($data) : false;
    }

    /**
     * Meta is passed as 'key'=>'value'
     * @since 1.0.1
     * @param $key - meta key
     * @param string $default - if array key does not exist, return this value
     * @return string
     */
    function get_meta($key, $default='') {
        if(@$this->data->meta === false) return $default;
        if(!isset($this->data->meta[$key])) return $default;
        return $this->data->meta[$key];
    }

    /**
     * @since 1.0.1
     * @param $key_values
     * @param bool $save
     */
    function set_meta($key_values,$save=true) {
        global $wpdb;
        foreach($key_values as $k=>$v) {
            $this->data->meta[$k] = $v;
        }
        if($save) $this->save_meta();
    }
    function unset_meta($keys, $save=true) {
        global $wpdb;
        if(!is_array($keys)) $keys = array($keys);
        foreach($keys as $key) {
            unset($this->data->meta[$key]);
        }
        if($save) $this->save_meta();
    }
    function get_ops($objects=true) {
        if(!empty($this->ID) && $this->ID > 0) {
            global $wpdb;
            $q = "SELECT operators FROM `".aiosc_get_table(aiosc_tables::departments)."` WHERE ID=".(int)$this->ID;
            $data = $wpdb->get_var($q);
            $ops = $data != false && $data != ''?@unserialize($data):false;
            if($ops !== false && $objects) {
                $op_objects = array();
                foreach($ops as $op_id) {
                    $usr = new aiosc_User($op_id);
                    if(aiosc_is_user($usr) && $usr->can('staff')) $op_objects[] = $usr;
                }
                return $op_objects;
            }
            return $ops;
        }
        return false;
    }
    function add_operator($operator) {
        global $wpdb;
        $old_ops = $this->get_ops(false);
        if(aiosc_is_user($operator)) $operator = $operator->ID;
        if(!is_array($old_ops) || !in_array($operator,$old_ops)) {
            $old_ops[] = $operator;
            $new_ops = serialize($old_ops);
            return $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::departments)."` SET operators='$new_ops' WHERE ID=$this->ID");
        }
        return true;
    }
    function remove_operator($operator) {
        global $wpdb;
        $old_ops = $this->get_ops(false);
        if(aiosc_is_user($operator)) $operator = $operator->ID;
        if(is_array($old_ops) && in_array($operator,$old_ops)) {
            aiosc_log("DEP: Removing operator");
            unset($old_ops[array_search($operator,$old_ops)]);
            $new_ops = serialize($old_ops);
            return $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::departments)."` SET operators='$new_ops' WHERE ID=$this->ID");
        }
        return true;
    }
    function ticket_count() {
        global $wpdb;
        $q = "SELECT COUNT(*) FROM `".aiosc_get_table(aiosc_tables::tickets)."` WHERE department_id=".(int)$this->ID;
        $count = $wpdb->get_var($q, 0, 0);
        return $count;
    }
    function op_count() {
        return (is_array($this->data->operators))?count($this->data->operators):0;
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
function aiosc_is_department($object) {
    if($object === false || empty($object) || $object == null) return false;
    if(is_a($object,'aiosc_Department') && is_numeric($object->ID) && $object->ID > 0 && $object->data !== false) return true;
    return false;
}
