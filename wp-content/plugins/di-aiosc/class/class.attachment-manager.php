<?php
class aiosc_AttachmentManager {
    /**
     * Uploads array of files to server.
     * It creates YYYY/MM directory structures and move uploaded files there. Uploaded file names are encrypted and stored in
     * database to prevent users to download their files directly. Once script requests file, it checks for database records first and builds
     * an temporary download link.
     *
     * On success, script returns aiosc_response with DATA=>array('files'=>ARRAY_OF_FILE_IDS_FROM_DATABASE),
     * On error, script returns aiosc_response errors.
     * @param $files
     * @param string $post_type - this can be 'ticket' or 'reply'. Many things will depend on this selection
     * @return array|mixed|string|void
     */
    static function upload_attachments($files, $post_type='ticket') {
        try {
            global $aiosc_settings, $aiosc_user;
            if($post_type != 'ticket') $post_type = 'reply';

            if(empty($files) || !is_array($files)) return aiosc_response(1); //no files uploaded, but return true. if they tried to hack, who cares about the files that were not uploaded...
            if(!$aiosc_settings->get('allow_upload')) return aiosc_response(0,__('<strong>Error:</strong> File uploads are disabled by system.','aiosc'));
            if(!$aiosc_user->can('upload_files')) return aiosc_response(0,__("<strong>Error:</strong> You don't have the right permission to upload files.",'aiosc'));

            //convert $_FILES to nice array if needed
            if(isset($files['name'])) $files = self::file_array_to_nice_array($files);

            $errors = array();

            //check if we have more files for upload than allowed
            if($post_type == 'ticket') $max_files = $aiosc_settings->get('max_files_per_ticket');
            else  $max_files = $aiosc_settings->get('max_files_per_reply');


            if($max_files < count($files)) {
                if($post_type == 'ticket')
                    return aiosc_response(0,sprintf(__('<strong>Error:</strong> Maximum files you can upload while creating ticket is %d but you attached %d.','aiosc'),$max_files,count($files)));
                else
                    return aiosc_response(0,sprintf(__('<strong>Error:</strong> Maximum files you can upload while posting reply is %d but you attached %d.','aiosc'),$max_files,count($files)));
            }

            //check for each file size
            $max_size = $aiosc_settings->get('max_upload_size_per_file');
            for($i=0;$i<count($files);$i++) {
                $f = $files[$i];
                $e = $f['error'];
                if($e == UPLOAD_ERR_OK) {
                    if($f['size'] > ($max_size * 1024)) {
                        $errors[] = sprintf(__('<strong>Error:</strong> File <code>%s</code> exceeds maximum filesize limit.','aiosc'),$files[$i]['name']);
                    }
                }
                elseif($e == UPLOAD_ERR_NO_FILE) {
                    unset($files[$i]);
                }
                elseif($e == UPLOAD_ERR_INI_SIZE || UPLOAD_ERR_FORM_SIZE) {
                    $errors[] = sprintf(__('<strong>Error:</strong> File <code>%s</code> exceeds maximum filesize limit.','aiosc'),$files[$i]['name']);
                }
                else {
                    if($e != UPLOAD_ERR_OK) {
                        if(UPLOAD_ERR_NO_TMP_DIR) 
							$errors[] = sprintf(__('<strong>Error:</strong> File <code>%s</code> | No temporary directory.','aiosc'),$files[$i]['name']);
						elseif(UPLOAD_ERR_CANT_WRITE) 
							$errors[] = sprintf(__('<strong>Error:</strong> File <code>%s</code> | Cant write to disk.','aiosc'),$files[$i]['name']);
						elseif(UPLOAD_ERR_PARTIAL) 
							$errors[] = sprintf(__('<strong>Error:</strong> File <code>%s</code> | Only partially uploaded','aiosc'),$files[$i]['name']);
						elseif(UPLOAD_ERR_EXTENSION) 
							$errors[] = sprintf(__('<strong>Error:</strong> File <code>%s</code> | A PHP extension stopped the file upload.','aiosc'),$files[$i]['name']);
						
						else $errors[] = sprintf(__('<strong>Error:</strong> File <code>%s</code> could not be uploaded due to unknown reason. | %s','aiosc'),$files[$i]['name'], $e);
                    }
                }
            }
            if(count($errors)) return aiosc_response(0,implode("<br>",$errors));
            //valid file size? continue...

            //check for file extensions
            foreach($files as $file) {
                if(!self::is_valid_ext($file['name'])) $errors[] = sprintf(__('<strong>Error:</strong> File <code>%s</code> has invalid extension.','aiosc'),$file['name']);
            }
            if(count($errors)) return aiosc_response(0,implode("<br>",$errors));
            //valid extension? continue...

            //create directory structure for current year & month
            $upload_dir = self::install_directory();
            //check if we successfully created directory
            if($upload_dir === false) return aiosc_response(0,__('<strong>Error:</strong> Files could not be uploaded due to unknown reason.','aiosc'));

            $successes = array();
            $y = 0;
            foreach($files as $file) {
                $name = $file['name'];
                $enc_name = sha1($name.rand(10000,99999).$y.$aiosc_user->ID).time(); //badass!
                $ext = self::get_file_ext($name);
                if(!move_uploaded_file($file['tmp_name'],$upload_dir."/".$enc_name.".".$ext)) $errors[] = sprintf(__('<strong>Error:</strong> File <code>%s</code> could not be uploaded due to unknown reason.','aiosc'),$name);
                else {
                    $id = self::insert_into_db($name, $enc_name, $ext);
                    if($id < 1) {
                        $errors[] = sprintf(__('<strong>Error:</strong> File <code>%s</code> could not be uploaded due to unknown reason.','aiosc'),$name);
                        @unlink($upload_dir."/".$enc_name.".".$ext);
                    }
                    else $successes[] = $id;
                }
                $y++;
            }
            if(!empty($errors)) return aiosc_response(0,implode('<br>',$errors));
            else return aiosc_response(1,sprintf(__('Total of %d files were uploaded successfully.','aiosc'),count($successes)),array('files'=>$successes));
        }
        catch(RuntimeException $e) {
            //something went wrong seriously...
            return aiosc_response(0,__('<strong>Error:</strong> Files could not be uploaded due to unknown error.','aiosc'));
        }
    }

    /**
     * Create required directories for YEAR-MONTH
     * If created, returns full path, otherwise returns FALSE
     * @return bool|string
     */
    private static function install_directory() {
        if(!is_dir(AIOSC_UPLOAD_DIR)) @mkdir(AIOSC_UPLOAD_DIR,0777);
        self::create_index_file(AIOSC_UPLOAD_DIR);
        $upload_dir = AIOSC_UPLOAD_DIR."/".date('Y');
        if(!is_dir($upload_dir)) @mkdir($upload_dir,0777);
        self::create_index_file($upload_dir);
        $upload_dir .= '/'.date("m");
        if(!is_dir($upload_dir)) @mkdir($upload_dir,0777);
        self::create_index_file($upload_dir);
        //check if we successfully created directory
        return (is_dir($upload_dir))?$upload_dir:false;
    }
    private static function create_index_file($path) {
        if(!file_exists($path."/index.php") && is_dir($path)) {
            file_put_contents($path."/index.php",'<?php //silence is golden');
        }
    }
    static function file_array_to_nice_array($files) {
        if(!empty($files) && is_array($files)) {
            $nice = array();
            for($i=0;$i<count($files['name']);$i++) {
                foreach(array_keys($files) as $key) {
                    $nice[$i][$key] = $files[$key][$i];
                    /*
                     *
                $nice[$i] = array(
                    'name'=>@$files['name'][$i],
                    'type'=>@$files['type'][$i],
                    'size'=>@$files['size'][$i],
                    'tmp_name'=>@$files['tmp_name'][$i],
                    'error'=>@$files['error'][$i]
                );
                     */
                }
            }
            return $nice;
        }
        else return $files;
    }
    /**
     * Insert newly created file into database
     * On success returns newly created row ID, otherwise returns 0
     * @param $name - original file name (for displaying)
     * @param $enc_name - encrypted file name (for download)
     * @param $ext - file extension (for download)
     * @return int
     */
    private static function insert_into_db($name, $enc_name, $ext) {
        global $wpdb, $aiosc_user;
        $name = esc_sql($name);
        $enc_name = esc_sql($enc_name);
        $ext = esc_sql(strtolower($ext));
        $date = date("Y-m-d H:i:s"); //used for building download link (to find correct directory where file is stored)
        $q = $wpdb->query("INSERT INTO `".aiosc_get_table(aiosc_tables::uploads)."` (owner_id, file_name, encrypted_name, file_ext, date_uploaded)
        VALUE ($aiosc_user->ID, '$name', '$enc_name', '$ext', '$date')");
        if($q) return $wpdb->insert_id;
        else return 0;
    }
    /**
     * Check if file extension is allowed (defined in aiosc_settings->upload_mimes)
     * There are few extensions that are always forbidden for safety reasons, such as PHP, EXE, BAT...
     * @param $file_name
     * @return bool
     */
    static function is_valid_ext($file_name) {
        global $aiosc_settings;
        $file_ext = self::get_file_ext($file_name);
        $always_forbidden = array('php','exe','bat');
        if(in_array($file_ext,$always_forbidden)) return false;
        $exts = strtolower($aiosc_settings->get('upload_mimes'));
        $exts = str_replace(" ","",$exts);
        $forbid = $aiosc_settings->get('upload_mimes_forbid');
        if((empty($exts) && !$forbid) || $exts == '*') return true;
        $exts = explode(",",$exts);
        for($i=0;$i<count($exts);$i++) {
            if(empty($exts[$i])) unset($exts[$i]);
        }
        if(($forbid && in_array($file_ext,$exts)) || (!$forbid && !in_array($file_ext,$exts))) return false;
        else return true;
    }

    /**
     * Get file extension from file name. If file has no extension, an empty string will be returned.
     * @param $file_name
     * @return mixed|string
     */
    static function get_file_ext($file_name) {
        $parts = explode(".",strtolower($file_name));
        if(count($parts) < 2) return "";
        else return strtolower(end($parts));
    }

    static function delete_attachment($id) {

    }
    static function shortcode_downloader() {
        global $aiosc_user, $aiosc_settings;
        $att = new aiosc_Attachment(aiosc_pg('file_id'));
        $ticket = new aiosc_Ticket(aiosc_pg('ticket_id'));
        if(aiosc_is_attachment($att) && $att->_file_exists()) {
            if($aiosc_user->can('download_file',array('file_id'=>$att, 'ticket_id'=>$ticket)) || ($aiosc_user->ID == $att->data->owner_id && $aiosc_settings->get('allow_download'))) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($att->file_name));
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . @filesize($att->get_file_path()));

                readfile($att->get_file_path());
                exit;
            }
            else aiosc_log('[AIOSC Downloader] [File ID: '.aiosc_pg('file_id').'] [Ticket ID: '.aiosc_pg('ticket_id').'] Attachment found but user has no right permission to download it.');
        }
        else aiosc_log('[AIOSC Downloader] [File ID: '.aiosc_pg('file_id').'] [Ticket ID: '.aiosc_pg('ticket_id').'] Attachment not found.');
        exit;
    }
}
