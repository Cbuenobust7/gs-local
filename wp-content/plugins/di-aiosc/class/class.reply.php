<?php

/**
 * @updated 1.1
 * Class aiosc_Reply
 */
class aiosc_Reply {
    public $ID;
    public $data;
    function __construct($id, $data = false) {
        $this->ID = $id;
        if($id > 0) {
            if($data !== false) $this->data = $data;
            else $this->data = $this->get_data();
        }
        else $this->data = false;
        if($this->data != false) {
            $this->data->content = aiosc_clean_content($this->data->content);
            $this->data->attachment_ids = (!empty($this->data->attachment_ids)) ? unserialize($this->data->attachment_ids) : false;
            $this->data->is_staff_reply = aiosc_enumToBool($this->data->is_staff_reply);
            $this->data->is_public = aiosc_enumToBool($this->data->is_public);
            /** @var meta
             * @since 1.0.1
             */
            $this->data->meta = (!empty($this->data->meta)) ? unserialize($this->data->meta) : false;
        }
    }

    /**
     * Changes Author ID of this reply. This method is called from aiosc_Ticket->set_author, not directly.
     * @since 1.0.9
     * @param $author
     * @return bool
     */
    function set_author($author) {
        global $wpdb;
        if(is_numeric($author)) $author = new aiosc_User($author);
        if(!aiosc_is_user($author)) return false;
        if($author->ID == $this->author_id) return true;
        //set ownership of files attached to this reply.
        if(is_array($this->attachment_ids)) {
            foreach($this->attachment_ids as $att) {
                $a = new aiosc_Attachment($att);
                $a->set_owner($author);
            }
        }
        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::replies)."` SET author_id='$author->ID' WHERE ID=$this->ID");
        $this->data->author_id = $author->ID;
        return true;
    }
    private function get_data() {
        global $wpdb;
        $q = "SELECT * FROM `".aiosc_get_table(aiosc_tables::replies)."` WHERE ID=".$this->ID;
        $data = $wpdb->get_results($q);
        if(count($data) > 0) return $data[0];
        else return false;
    }
    function get_attachments_count() {
        if(isset($this->data->attachment_ids) && is_array($this->data->attachment_ids)) return count($this->data->attachment_ids);
        else return 0;
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

        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::replies)."` SET meta='$meta' WHERE ID=".$this->ID);
    }

    /**
     * @since 1.0.1
     */
    public function load_meta() {
        global $wpdb;
        $q = "SELECT meta FROM `".aiosc_get_table(aiosc_tables::replies)."` WHERE ID=".$this->ID;
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
        if($this->data->meta === false) return $default;
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
        if(!empty($this->attachment_ids) && $attachments) {
            foreach($this->attachment_ids as $att_id) {
                $att = new aiosc_Attachment($att_id);
                $att->remove();
            }
        }
        global $wpdb;
        $wpdb->query("DELETE FROM `".aiosc_get_table(aiosc_tables::replies)."` WHERE ID=$this->ID");
        do_action('aiosc_reply_removed',$this->ID);
        return true;
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
function aiosc_is_reply($object) {
    if($object === false || empty($object) || $object == null) return false;
    if(is_a($object,'aiosc_Reply') && is_numeric($object->ID) && $object->ID > 0 && $object->data != false) return true;
    return false;
}