<?php

/**
 * @updated 1.1
 * Class aiosc_Attachment
 */
class aiosc_Attachment {
    public $ID;
    public $data;
    function __construct($id) {
        $this->ID = $id;
        $this->data = $this->get_object();
    }
    private function get_object() {
        if(!empty($this->ID) && $this->ID > 0) {
            global $wpdb;
            $q = "SELECT * FROM `".aiosc_get_table(aiosc_tables::uploads)."` WHERE ID=".(int)$this->ID;
            $data = $wpdb->get_results($q);
            if(!$data || !is_array($data)) return false;
            else return $data[0];
        }
        return false;
    }
    function set_owner($owner) {
        global $wpdb;
        if(is_numeric($owner)) $owner = new aiosc_User($owner);
        if(!aiosc_is_user($owner)) return false;
        if($this->owner_id == $owner->ID) return true;
        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::uploads)."` SET owner_id=$owner->ID WHERE ID=".$this->ID);
        $this->data->owner_id = $owner->ID;
        return true;
    }
    function get_file_size($measure='kb',$format=true) {
        $size = @filesize($this->get_file_path());
        if($measure == 'kb') return $format?number_format($size / 1024, 2):round($size / 1024);
        elseif($measure == 'mb') return $format?number_format($size / 1024 / 1024, 2):round($size / 1024/ 1024);
        elseif($measure == 'gb') return $format?number_format($size / 1024 / 1024 / 1024, 2):round($size / 1024 / 1024 / 1024);
        else return $format?number_format($size,0):$size; //bytes
    }
    function get_short_name($char_limit=0) {
        if($char_limit < 1 || strlen($this->data->file_name) <= $char_limit + 3) return $this->data->file_name;
        else {
            $total = $char_limit - 3; //3 == strlen('...')
            $half = round($char_limit / 2);
            $second_half = $char_limit - $half;
            $name = substr($this->data->file_name,0,$half);
            $name .= "...";
            $name .= substr($this->data->file_name,strlen($this->data->file_name) - $second_half,$second_half);
            return $name;
        }
    }

    /**
     * Returns URL for downloading this file.
     * If file exists AND (user has capability to download all files, or user is owner of the file), link will be returned.
     * Otherwise, null string will be returned.
     * @param $object
     * @return null|string
     */
    function get_download_url($object=null) {
        global $aiosc_user, $aiosc_settings;

        $no_dl = 'javascript:alert(\''.strip_tags(AIOSC_PERMISSION_ERROR).'\');';
        if(aiosc_is_ticket($object)) {
            $ticket = $object;
            $reply = false;
            $obj_files = is_array($object->attachment_ids)?$object->attachment_ids:array();
        }
        elseif(aiosc_is_reply($object)) {
            $reply = $object;
            $ticket = new aiosc_Ticket($reply->ticket_id);
            $obj_files = is_array($object->attachment_ids)?$object->attachment_ids:array();
        }
        else return $no_dl;

        if(empty($this->data)) return $no_dl;
        if(!$this->_file_exists()) return $no_dl;
        $url = 'javascript:aiosc_download_file('.$this->ID.','.$ticket->ID.');';
        if($aiosc_user->can('download_file', array('ticket_id'=>$ticket, 'file_id'=>$this))) return $url;
        else return $no_dl;
    }

    /**
     * Check whether file exists on server or not
     * @return bool
     */
    function _file_exists() {
        if(empty($this->data)) return false;
        $path = $this->get_file_path();
        return (!file_exists($path) || is_dir($path))?false:true;
    }

    /**
     * Returns full path to the file
     * @return bool|string
     */
    function get_file_path() {
        if(empty($this->data)) return false;
        $m = date("m",strtotime($this->data->date_uploaded));
        $y = date("Y",strtotime($this->data->date_uploaded));
        return AIOSC_UPLOAD_DIR."/$y/$m/".$this->get_file_name();
    }

    /**
     * Returns full url to the file (but this wont be used because we have custom downloader)
     * @return bool|string
     */
    function get_file_uri() {
        if(empty($this->data)) return false;
        $m = date("m",strtotime($this->data->date_uploaded));
        $y = date("Y",strtotime($this->data->date_uploaded));
        return AIOSC_UPLOAD_URL."/$y/$m/".$this->get_file_name();
    }

    /**
     * Get encrypted (saved) file name for building download links
     * @return bool|string
     */
    function get_file_name() {
        if(empty($this->data)) return false;
        return $this->data->encrypted_name.".".$this->data->file_ext;
    }
    function remove() {
        $path = $this->get_file_path();
        if(file_exists($path) && !is_dir($path)) {
            //delete from database
            global $wpdb;
            $wpdb->query("DELETE FROM `".aiosc_get_table(aiosc_tables::uploads)."` WHERE ID=$this->ID");
            do_action('aiosc_attachment_removed',$this->ID);
            //and then delete file
            @unlink($path);
        }
    }

    /**
     * Get icon url that best matches the extension of this file.
     *
     * @filter aiosc_attachment_icons - can add more icons and more extensions
     *
     * @return string
     */
    function get_icon_url() {
        $ext = $this->data->file_ext;
        $icons = array(
            'csv'=>array('csv','xls','xlsx','xlsm','xlsb','xltx','xltm','xlt','xml','xlam','xla','xlw'),
            'archive'=>array('gz','rar','tar'),
            'zip'=>array('zip','7z','7zip'),
            'doc'=>array('rtf','doc','docx'),
            'img'=>array('png','jpg','jpeg','gif','bmp','tiff','psd','ai'),
            'pdf'=>array('pdf'),
            'sql'=>array('sql','db'),
            'html'=>array('html','css','javascript')
        );
        $icons = apply_filters('aiosc_attachment_icons',$icons);
        $icon = 'default';
        foreach($icons as $k=>$v) {
            if(in_array($ext,$v)) {
                $icon = $k;
                break;
            }
        }
        return AIOSC_URL."assets/css/img/mimetypes/$icon.png";
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
function aiosc_is_attachment($object) {
    if($object === false || empty($object) || $object == null) return false;
    if(is_a($object,'aiosc_Attachment') && is_numeric($object->ID) && $object->ID > 0 && $object->data !== false) return true;
    return false;
}
