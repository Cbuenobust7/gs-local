<?php
class aiosc_PremadeResponse {
    public $ID;
    public $data;
    function __construct($id, $data=null) {
        $this->ID = $id;
        $this->data = empty($data)?$this->get_object():$data;
        if($this->data !== false) {
            $this->data->is_shared = aiosc_enumToBool($this->data->is_shared);
            $this->data->name = aiosc_clean_content($this->data->name,"",false);
            $this->data->content = aiosc_clean_content($this->data->content);
        }
    }
    private function get_object() {
        if(!empty($this->ID) && $this->ID > 0) {
            global $wpdb;
            $q = "SELECT * FROM `".aiosc_get_table(aiosc_tables::premade_responses)."` WHERE ID=".(int)$this->ID;
            $data = $wpdb->get_results($q);
            if(!$data || !is_array($data)) return false;
            else return $data[0];
        }
        return false;
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
function aiosc_is_premade_response($object) {
    if($object === false || empty($object) || $object == null) return false;
    if(is_a($object,'aiosc_PremadeResponse') && is_numeric($object->ID) && $object->ID > 0 && $object->data !== false) return true;
    return false;
}
