<?php
class aiosc_Priority {
    public $ID;
    public $data;
    function __construct($id, $data=null) {
        $this->ID = $id;
        $this->data = empty($data)?$this->get_priority():$data;
        if($this->data !== false) {
            $this->data->is_active = aiosc_enumToBool($this->data->is_active);
            $this->data->name = aiosc_clean_content($this->data->name,"",false);
            /** @var meta
             * @since 1.0.1
             */
            $this->data->meta = (!empty($this->data->meta)) ? unserialize($this->data->meta) : false;
        }
    }
    private function get_priority() {
        if(!empty($this->ID) && $this->ID > 0) {
            global $wpdb;
            $q = "SELECT * FROM `".aiosc_get_table(aiosc_tables::priorities)."` WHERE ID=".(int)$this->ID;
            $data = $wpdb->get_results($q);
            if(!$data || !is_array($data)) return false;
            else return $data[0];
        }
        return false;
    }
    function ticket_count() {
        global $wpdb;
        $q = "SELECT COUNT(*) FROM `".aiosc_get_table(aiosc_tables::tickets)."` WHERE priority_id=".(int)$this->ID;
        $count = $wpdb->get_var($q, 0, 0);
        return $count;;
    }
    function get_color_style() {
        $priority_colorstyle = !empty($this->color)?"background: $this->color;":'';
        if(!empty($priority_colorstyle))
            $priority_colorstyle .= aiosc_get_brightness($this->color) > 180?' color: black;':' color: white;';
        return $priority_colorstyle;
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

        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::priorities)."` SET meta='$meta' WHERE ID=".$this->ID);
    }

    /**
     * @since 1.0.1
     */
    public function load_meta() {
        global $wpdb;
        $q = "SELECT meta FROM `".aiosc_get_table(aiosc_tables::priorities)."` WHERE ID=".$this->ID;
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
function aiosc_is_priority($object) {
    if($object === false || empty($object) || $object == null) return false;
    if(is_a($object,'aiosc_Priority') && is_numeric($object->ID) && $object->ID > 0 && $object->data !== false) return true;
    return false;
}
