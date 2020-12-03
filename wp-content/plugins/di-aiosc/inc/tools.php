<?php
/**
 * Loads template file from /template directory
 * If $can_extend is true, method looks for file in Theme directory first, to check if there
 * is an child template file available and uses it. Otherwise, returns default file found in plugin/template directory.
 * @param $template_name
 * @param bool $can_extend
 * @return string
 */
function aiosc_load_template($template_name,$can_extend=false) {
    $file = aiosc_get_template_path($template_name, $can_extend);
    if(empty($file)) return '';

    if(file_exists($file)) {
        ob_start();
        @include $file;
        return ob_get_clean();
    }
    return '';
}
function aiosc_get_template_path($template_name, $can_extend = false) {
    if(empty($template_name)) return '';
    $file = AIOSC_DIR."/templates/$template_name";
    if($can_extend && file_exists(get_stylesheet_directory()."/di-aiosc/$template_name"))
        $file = get_stylesheet_directory()."/di-aiosc/$template_name";
    return $file;
}
function aiosc_get_table($table_name) {
    global $wpdb;
    return $wpdb->prefix.$table_name;
}

/**
 * Builds response for ajax usually.
 * @param int $result - 0 == error, everything above is success
 * @param string $message - Optional. An error / success message string can be passed here
 * @param array $data - Optional. Additional data can be passed to response string / array
 * @param bool $to_json - Optional. If true, it will return json string, otherwise an array will be returned
 * @return array|mixed|string|void
 */
function aiosc_response($result=1,$message='',$data=array(),$to_json=true) {
    /* Log first */
    if($result != 1) {
        global $aiosc_user;
        aiosc_log("[AIOSC Response] [UserID: ".@$aiosc_user->ID."] [Result: $result]\n[Message: $message]");
    }


    /* Return */
    $res = array('result'=>$result,'message'=>$message,'data'=>$data);
    if($to_json) $res = json_encode($res);
    return $res;
}

/**
 * Check if response string is an error or success
 * @see aiosc_response()
 * @param $response - array or json data, usually built with aiosc_response
 * @return bool
 */
function aiosc_is_error($response) {
    if($response === false || $response === 0) return true;
    if(!is_array($response)) $response = json_decode($response, true);
    return (!isset($response['result']) || $response['result'] < 1);
}

function aiosc_user_url($user_id, $echo=true) {
    if($echo) echo aiosc_get_user_url($user_id);
    else return aiosc_get_user_url($user_id);
}

function aiosc_boolToEnum($boolean) {
    return $boolean == false?'N':'Y';
}

function aiosc_enumToBool($enum) {
    return empty($enum) || $enum == 'N' || $enum === 0 || $enum === false?false:true;
}

/**
 * Returns brightness value from 0 to 255
 * This can be used to determine if we should use WHITE text over dark background, or black text over light background.
 * @param $hex
 * @return float
 */
function aiosc_get_brightness($hex) {
// returns brightness value from 0 to 255

    $hex = str_replace('#', '', $hex);

    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));

    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}

/**
 * Safe way for getting ini_get variable if ini_get is available
 * @param $key
 * @param $on_fail = null, if ini_get fails, this variable will be returned instead
 * @return mixed
 */
function aiosc_ini_get($key,$on_fail=null) {
    if(function_exists('ini_get')) return ini_get($key);
    else return $on_fail;
}

/**
 * Safe way for getting $_REQUEST key value (excluding $_COOKIE). On some servers, $_REQUEST doesn't work so it could
 * easily lead to malfunctioning code and unnecessary errors.
 *
 * If neither $_GET or $_POST were found, NULL will be returned
 *
 * @param $key
 * @param bool $post_has_priority - if true, $_POST will have priority over $_GET. Otherwise, $_GET will have priority over $_POST
 * @param $default - if nothing is found, function can return default value specified
 * @return string
 */
function aiosc_pg($key, $post_has_priority=true, $default=null) {
    if($post_has_priority) {
        if(isset($_POST[$key])) return $_POST[$key];
        elseif(isset($_GET[$key])) return $_GET[$key];
    }
    else {
        if(isset($_GET[$key])) return $_GET[$key];
        elseif(isset($_POST[$key])) return $_POST[$key];
    }
    return $default;
}

/**
 * Safe way for check if $_REQUEST key is set. On some servers, $_REQUEST doesn't work so it could
 * easily lead to malfunctioning code and unnecessary errors.
 *
 * If neither $_GET or $_POST were found, FALSE will be returned
 *
 * @param $key
 * @param bool $post_has_priority - if true, $_POST will have priority over $_GET. Otherwise, $_GET will have priority over $_POST

 * @return string
 */
function aiosc_isset_pg($key, $post_has_priority = true) {
    if($post_has_priority) {
        if(isset($_POST[$key])) return true;
        elseif(isset($_GET[$key])) return true;
    }
    else {
        if(isset($_GET[$key])) return true;
        elseif(isset($_POST[$key])) return true;
    }
    return false;
}
function aiosc_cookie_get($key, $default=false) {
    if(isset($_COOKIE['aiosc_'.$key])) return $_COOKIE['aiosc_'.$key];
    else return $default;
}
function aiosc_cookie_set($key, $value, $expires=null) {
    if(empty($expires)) $expires = 60*60*24*7; //7 days
    $expires = current_time('timestamp') + $expires;
    setcookie('aiosc_'.$key, $value, $expires, COOKIEPATH, COOKIE_DOMAIN);
}

/**
 * @update 1.1
 * @param $user_id
 * @param string $fallback
 * @return string
 */
function aiosc_get_user_url($user_id, $fallback='') {
    global $aiosc_user;
    if($aiosc_user->ID == $user_id) return get_admin_url()."profile.php";
    elseif($aiosc_user->wpUser->has_cap('edit_users')) return get_admin_url()."user-edit.php?user_id=$user_id";
    else return $fallback;
}
function aiosc_get_data_file($file, $default='') {
    $file = AIOSC_DIR."/data/$file";
    if(file_exists($file) && !is_dir($file)) return file_get_contents($file);
    else return $default;
}
function aiosc_clean_content($string,$html_tags="", $autop = true) {

    $string = aiosc_preclean_content($string);
    if(empty($html_tags)) $html_tags = "<b><strong><em><i><br><hr><p><span><small><h1><h2><h3><h4><h5><ul><ol><li><a><del><blockquote><pre><code>";

    $string = strip_tags($string,$html_tags);
    if($autop) $string = wpautop($string);
    return $string;
}
function aiosc_preclean_content($string) {
    $rplc = array(
        "\\'"=>"'",
        '\\"'=>'"',
        "\\\\"=>"\\"
    );
    return str_replace(array_keys($rplc),array_values($rplc),$string);
}
function aiosc_time_ago($date) {
    if(empty($date)) return "";

    $periods = array("s", "m", "h", "d", "w");
    $lengths = array("60","60","24","7");
    $now = current_time("timestamp"); //wp time zone
    $unix_date = strtotime($date);
    // check validity of date
    if(empty($unix_date)) {
        return $date; //bad date
    }
    // is it future date or past date
    if($now > $unix_date) {
        $difference = $now - $unix_date;
        $tense = __('ago','aiosc');
    }
    else {
        $difference = $unix_date - $now;
        $tense = __('from now','aiosc');
    }
    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }
    $difference = round($difference);
    if($periods[$j] == "s" && $difference == 0) return __('just now','aiosc');

    return "$difference$periods[$j] {$tense}";
}
function aiosc_get_search_terms() {
    $search = '';
    if(aiosc_pg('search',false,false) !== false && aiosc_pg('search-submit',false,false) !== false && aiosc_pg('search-submit',false,'') != '') {
        $term = esc_sql(aiosc_pg('search',false));
        if(aiosc_str_startsWith("#", $term)) {
            $id = substr($term, 1, strlen($term) - 1);
            if(is_numeric($id) && $id > 0) $search = "ID = $id";
        }
        else {
            $search = "((subject LIKE '%$term%' OR content LIKE '%$term%' OR ticket_meta LIKE '%$term%')
            OR ID IN (SELECT ticket_id FROM `".aiosc_get_table(aiosc_tables::replies)."` WHERE content LIKE '%$term%'))";
        }
    }
    return $search;
}

/**
 * Get current ticket query based on filters & search
 *
 * @filter aiosc_ticket_query
 *
 * @return mixed|string|void
 */
function aiosc_get_query($add_where = false) {
    global $wpdb, $aiosc_user, $aiosc_capabilities, $aiosc_settings;

    $break_query = false;

    $search = aiosc_get_search_terms();
    $departments = '';
    $additional = '';
    $awaiting_reply = '';

    $ticket_query = array();

    $status = !in_array(aiosc_pg('status'),aiosc_TicketManager::get_statuses())?'':aiosc_pg('status');
    if(!empty($status)) $ticket_query['status'] = $status;

    if(aiosc_pg('priority') > 0) {
        $ticket_query['priority_id'] = aiosc_pg('priority');
        $break_query = true;
    }
    if(aiosc_pg('department') > 0) {
        $ticket_query['department_id'] = aiosc_pg('department');
        $break_query = true;
    }
    if(aiosc_pg('is_public') === 'Y' || aiosc_pg('is_public') === 'N') $ticket_query['is_public'] = aiosc_pg('is_public');


    if(aiosc_pg('awaiting_staff_reply')) {
        $additional .= " (awaiting_reply = 'Y' AND status <> 'closed') ";
        //$break_query = true;
    }
    if(aiosc_pg('requested_closure')) {
        $ticket_query['closure_requested'] = 'Y';
        //$break_query = true;
    }
//Staff-only
    if($aiosc_user->can('staff')) {
        if (aiosc_pg('operator') > 0) {
            $ticket_query['op_id'] = aiosc_pg('operator');
            $break_query = true;
        }
        if (aiosc_pg('author') > 0) {
            $ticket_query['author_id'] = aiosc_pg('author');
            $break_query = true;
        }
    }
    if(aiosc_pg('frontend') == 1 && aiosc_isset_pg('public_only')) {
        $ticket_query['is_public'] = 'Y';
        unset($ticket_query['author_id']);
        $break_query = true;
    }

    //Now get tickets from current user's departments, only if above filters are not active,
    //otherwise ignore this additional query
    if(!$break_query && ($aiosc_user->can('staff') && !$aiosc_user->can('edit_tickets'))) {
        $ddd = $aiosc_user->get_departments(false);
        if($ddd !== false) {
            for($i=0;$i<count($ddd);$i++) {
                $dep = $ddd[$i]; //department_id
                $departments .= $i < 1  ? "department_id='$dep'" : " OR department_id='$dep'";
            }
        }
        if(!empty($departments))
            $departments = "($departments)";
    }


    $y = 0;
    $ticket_query_str = '';
    foreach($ticket_query as $k=>$v) {
        $ticket_query_str .= $y  < 1 ? "$k='$v'" : " AND $k='$v'";
        $y++;
    }
    if(!empty($ticket_query_str)) {
        $ticket_query_str = "($ticket_query_str)";
        $additional .= empty($additional) ? $ticket_query_str : " AND $ticket_query_str";
    }
    $y = 0;

    $query = '';

    if(!empty($search)) {
        if($add_where) $query .= " WHERE ";
        else $query .= " AND ";
        $query .= $search;
    }
    if(!empty($departments)) {
        $query .= empty($query) && $add_where ? " WHERE " : " AND ";
        $query .= $departments;
    }
    if(!empty($additional)) {
        $query .= empty($query) && $add_where ? " WHERE " : " AND ";
        $query .= $additional;
    }
    $query .= " ";

    $query = apply_filters('aiosc_ticket_query', $query);
    return $query;
}

/**
 * How many tickets per page to display?
 * @used-in ticket list templates
 *
 * @filter aiosc_tickets_per_page(int 10)
 * @cookie tickets-per-page (aiosc_cookie_get('tickets-per-page'))
 *
 * @see aiosc_cookie_get()
 *
 * @return bool
 */
function aiosc_tickets_per_page() {
    return aiosc_cookie_get('tickets-per-page',apply_filters('aiosc_tickets_per_page',10));
}
/**
 * Builds pagination for AIOSC tables
 * @param $total_count - total count of items table is displaying (total count of items gotten from SQL query)
 * @param $items_per_page - how many items to show per page?
 * @param $current_page - which page are we currently on?
 * @return string
 */
function aiosc_get_pagination($total_count, $items_per_page, $current_page) {
    $total_pages = ceil($total_count / $items_per_page);
    if($current_page < 1) $current_page = 1;

    //how many items are we showing?
    if($total_pages > 1)
        $num_items = $current_page < $total_pages ? $items_per_page : ($total_count - ($total_pages-1) * $items_per_page);
    else $num_items = $total_count;



    $html  = '<div class="tablenav-pages aiosc-pagination"><span class="displaying-num">'.sprintf(_n('%d item','%d items',$num_items,'aiosc'),$num_items).' ('.sprintf(__('total: %d', 'aiosc'), $total_count).')</span> &nbsp;';

    if($total_pages > 1) {
        $html .= '<span class="pagination-links">';
        if($current_page > 1) {
            $prev = $current_page - 1;
            $html .= '<a class="first-page" title="'.__('Go to the first page','aiosc').'" data-page=1 href="javascript:void(0)">«</a>
                <a class="prev-page" title="'.__('Go to the previous page','aiosc').'" data-page='.$prev.' href="javascript:void(0)">‹</a>';
        }
        else {
            $html .= '<a class="first-page disabled" title="'.__('Go to the first page','aiosc').'" data-page=1 href="javascript:void(0)">«</a>
                <a class="prev-page disabled" title="'.__('Go to the previous page','aiosc').'" data-page=1 href="javascript:void(0)">‹</a>';
        }

        $html .= '<span class="paging-input">
            <input class="current-page" title="'.__('Current page','aiosc').'" type="text" name="paged" value="'.$current_page.'" size="'.strlen($total_pages).'"> '.
            sprintf(__('of <span class="total-pages">%d</span>','aiosc'),$total_pages).'</span>';

        if($current_page < $total_pages) {
            $next = $current_page + 1;
            $html .= '<a class="next-page" title="'.__('Go to the next page','aiosc').'" data-page='.$next.' href="javascript:void(0)">›</a>
                <a class="last-page" title="'.__('Go to the last page','aiosc').'" data-page='.$total_pages.' href="javascript:void(0)">»</a>';
        }
        else {
            $html .= '<a class="next-page disabled" title="'.__('Go to the next page','aiosc').'" data-page='.$total_pages.' href="javascript:void(0)">›</a>
                <a class="last-page disabled" title="'.__('Go to the last page','aiosc').'" data-page='.$total_pages.' href="javascript:void(0)">»</a>';
        }

    }
    $html .= "</div>";
    return $html;
}

/**
 * Right now, it returns default WordPress e-mail address,
 * but in future this might be used for allowing user to set
 * custom FROM e-mail address
 */
function aiosc_get_from_email() {
    $sitename = strtolower( @$_SERVER['SERVER_NAME'] );
    if ( substr( $sitename, 0, 4 ) == 'www.' ) {
        $sitename = substr( $sitename, 4 );
    }
    return apply_filters('aiosc_from_email_address','wordpress@'.$sitename);
}
/**
 * Check if string is valid e-mail address
 * @param $string
 * @return bool
 */
function aiosc_is_email($string) {
    return (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $string))?false:true;
}

/**
 * Only log if WP_DEBUG and AIOSC_ERROR_LOGGING are defined and are set to TRUE
 * @param $message
 */
function aiosc_log( $message ) {
    if((defined('WP_DEBUG') && WP_DEBUG === true) &&
        (defined('AIOSC_ERROR_LOGGING') && AIOSC_ERROR_LOGGING === true)) {
        if( is_array( $message ) || is_object( $message ) ){
            error_log( print_r( $message, true ) );
        } else {
            error_log( $message );
        }
    }
}

function aiosc_get_datetime($date, $format='') {
    if(empty($format)) $format = get_option('date_format') . " " . get_option('time_format');
    if(!is_numeric($date)) $date = strtotime($date);
    return date_i18n($format, $date);
}
function aiosc_str_startsWith($needle, $haystack) {
    return $needle === "" || strpos($haystack, $needle) === 0;
}
function aiosc_str_endsWith($needle, $haystack) {
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

/**
 * Check if user can register to the site or not (if "Anyone can register" is enabled or disabled)
 * @return bool
 */
function aiosc_can_register() {
    if(wp_register('','',false) == '') return false;
    else return true;
}

/**
 * Check if AIOSC is in demo mode (for Preview)
 * @return bool
 */
function aiosc_is_demo() {
    if(!defined('AIOSC_DEMO_MODE') || AIOSC_DEMO_MODE != true) return false;
    else {
        if(current_user_can('manage_options')) return false;
        else return true;
    }
}

/**
 * Used to tell Javascript aiosc_log to log or not
 * @param bool $echo
 * @return string
 */
function aisoc_print_js_debug($echo = true) {
    if(defined('AIOSC_DEBUG') && AIOSC_DEBUG == true) {
        $src = "<script>var AIOSC_JS_DEBUG = true;</script>\n";
        if($echo) echo $src;
        else return $src;
    }
}

/**
 * Returns array containing plugin header data such as name, version, author, etc.
 * @uses get_plugin_data()
 * @since 1.0.5
 * @return array
 */
function aiosc_get_plugin_data() {
    return get_plugin_data(AIOSC_DIR."/di-aiosc.php");
}
function aiosc_get_version() {
    $data = aiosc_get_plugin_data();
    return @$data['Version'];
}
function aiosc_get_user_avatar($user) {
    if(!is_numeric($user) && aiosc_is_user($user)) $user = $user->ID;
    $avatar = get_avatar($user);
    preg_match("/src='(.*?)'/i", $avatar, $matches);
    return @$matches[1];
}

/**
 * Get domain of current WordPress site.
 * @since 2.0
 * @return mixed|string|void
 */
function aiosc_get_domain() {
    $domain = get_option('siteurl');
    $domain = str_replace('http://', '', $domain);
    $domain = str_replace('https://', '', $domain);
    $domain = str_replace('www.', '', $domain);
    $domain = strstr($domain, '/', true);
    return $domain;
}