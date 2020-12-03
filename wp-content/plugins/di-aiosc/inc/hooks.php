<?php
class aiosc_Hooks {
    function __construct() {
        global $aiosc_user;
        if($aiosc_user->can('staff')) {
            //show NEW TICKETS notification in toolbar
            add_action( 'admin_bar_menu', array($this,'toolbar_new_tickets'), 999 );
        }
        if(get_option('aiosc_installed') != 'Y' && (!isset($_GET['page']) || $_GET['page'] != 'aiosc-list')) {
            add_action("admin_footer",array($this,'show_activation_warning'));
        }
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 9000);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'), 9000);
        add_action('wp_head', array($this, 'wp_head'));
        add_action('admin_head', array($this, 'wp_head'));

        /** Shortcodes */
        add_shortcode('aiosc_ticket_form',array('aiosc_Shortcodes','ticket_form'));
        add_shortcode('aiosc_ticket_list',array('aiosc_Shortcodes','ticket_list'));
        add_shortcode('aiosc_ticket_preview',array('aiosc_Shortcodes','ticket_preview'));
        /** TeenyMCE */

        //add custom buttons to TeenyMCE editor, only if editor_id is specific (AIOSC editors)
        add_filter('mce_buttons',array($this,'mce_buttons'),10,2);
        add_filter('mce_buttons_2',array($this,'mce_buttons_2'),10,2);
        /** Roles */

        //remove AIOSC roles from default role selection on User Profile page
        add_filter('editable_roles',array($this,'editable_roles'));

        //add default AIOSC role to newly registered user
        add_action('user_register',array($this,"hook_save_user_role_register"));
        //add default AIOSC role to newly registered user via BuddyPress plugin
        add_action('bp_core_signup_user', array($this, 'hook_save_user_role_register'));
        //update plugin roles whenever WP calls set_role
        add_action('set_user_role',array($this,'set_user_roles'),1,3);

        //delete / reassign tickets, replies and attachments on user DELETION
        add_action('delete_user',array('aiosc_UserManager','delete_user'),1,2);

        if(current_user_can('manage_options')) {
            //add "AIOSC Role" select box on User Profile page
            add_action('edit_user_profile',array($this, 'aiosc_role_user_page'),6000);

            //This is triggered before EDIT_USER_PROFILE_UPDATE. We are passing current user ID to below action
            //so we can save AIOSC role to correct user.
            add_action('profile_update',array($this, 'aiosc_role_user_page_secondary'));

            //save "AIOSC Role" field on User Profile update
            add_action('edit_user_profile_update',array($this, 'save_aiosc_role'),9999);
            //
            add_filter( 'manage_users_columns', array($this,'modify_user_table') );
            add_filter( 'manage_users_custom_column', array($this,'modify_user_table_row'), 10, 3 );
        }

        add_action( 'load-users.php',             array( $this, 'user_role_bulk_change'   )        );
        add_action( 'restrict_manage_users',      array( $this, 'user_bulk'));

        /** AJAX */
        //Activator
        add_action('wp_ajax_aiosc_finalize_activation',array($this,'aiosc_finalize_activation'));

        //ticket related calls
        add_action('wp_ajax_aiosc_new_ticket',array($this,'new_ticket'));
        add_action('wp_ajax_aiosc_new_reply',array($this,'new_reply'));
        add_action('wp_ajax_aiosc_load_replies',array($this,'load_replies'));
        add_action('wp_ajax_aiosc_request_closure',array($this,'request_closure'));
        add_action('wp_ajax_aiosc_close_ticket',array($this,'close_ticket'));
        add_action('wp_ajax_aiosc_reopen_ticket',array($this,'reopen_ticket'));
        add_action('wp_ajax_aiosc_reply_remove',array($this,'reply_remove'));
        add_action('wp_ajax_aiosc_load_premade_response',array($this,'load_premade_response'));
        add_action('wp_ajax_aiosc_load_operator_list',array($this,'load_operator_list'));
        add_action('wp_ajax_aiosc_screen_options_tickets',array($this,'aiosc_screen_options_tickets'));

        //Download attachments
        add_action('wp_ajax_aiosc_download_file',array('aiosc_AttachmentManager','shortcode_downloader'));

        /** AJAX - PREFERENCES */

        //screen switching
        add_action('wp_ajax_aiosc_pref_screen',array($this,'preferences_screen'));
        add_action('wp_ajax_aiosc_account_screen',array($this,'account_screen'));
        //saving
        add_action('wp_ajax_aiosc_pref_save',array($this,'preferences_save'));
        add_action('wp_ajax_aiosc_account_save',array($this,'account_save'));
        //updating priorities & departments
        add_action('wp_ajax_aiosc_departments_update',array($this,'aiosc_departments_update'));
        add_action('wp_ajax_aiosc_priorities_update',array($this,'aiosc_priorities_update'));

        /** AJAX - TICKETS LIST */
        add_action('wp_ajax_aiosc_get_user_list', array($this, 'aiosc_get_user_list'));

        //screen switching
        add_action('wp_ajax_aiosc_tickets_list',array($this,'tickets_list'));
        add_action('wp_ajax_nopriv_aiosc_tickets_list',array($this,'tickets_list'));
    }
    function enqueue_scripts() {
        global $aiosc_settings;
        if(is_admin()) {
            if(!isset($_GET['page']) || substr($_GET['page'], 0, strlen('aiosc')) != 'aiosc') return;
        }
        else {
            global $post;

            if(!$aiosc_settings->get('pages_frontend_enable') || (int)@$post->ID < 1) return;
            $pages = array($aiosc_settings->get('page_ticket_list')); //$aiosc_settings->get('page_ticket_preview'), $aiosc_settings->get('page_ticket_form'),
            if(!in_array((int)@$post->ID, $pages)) return;
        }
		
		/** WooCommerce Extra Fee plugin fix */
		wp_dequeue_script('rpwcf-select2');
		wp_deregister_script('rpwcf-select2');
		wp_dequeue_style('rpwcf-select2');
		wp_deregister_style('rpwcf-select2');
		
		/** WooCommerce Fix */
        wp_deregister_script('select2');
        wp_register_script('select2', AIOSC_URL . 'assets/js/select2.min.js', array('jquery'), '4.0.0');
        wp_deregister_style('select2');
        wp_register_style('select2', AIOSC_URL . 'assets/css/select2.css', array(), '4.0.0');

    }
    function show_activation_warning() {
        wp_enqueue_script("jquery");
        wp_enqueue_style('aiosc-warning',AIOSC_URL.'assets/css/warning.css');
        echo aiosc_load_template('admin/activation-warning.php',true);
    }
    function aiosc_get_user_list() {
        global $aiosc_user;
        $q = aiosc_pg('q', true, '');
        $role = aiosc_pg('role', true, '');
        if(empty($q)) {
            echo json_encode(array());
        }
        else {
            $q .= "*";
            $users = get_users(array('search'=>$q));
            if(empty($users)) echo json_encode(array());
            else {
                $u = array();

                foreach($users as $user) {
                    if(aiosc_isset_pg('staff') || aiosc_isset_pg('authors')) {
                        $aUser = new aiosc_User($user->ID);
                        if(aiosc_isset_pg('staff') && !$aUser->can('staff')) continue;
                        if(aiosc_isset_pg('authors') && !$aUser->can('create_ticket')) continue;
                    }
                    $u[] = array('id'=>$user->ID, 'name'=>$user->display_name, 'login'=>$user->user_login);
                }

                echo json_encode($u);
            }
        }
        die();
    }
    function aiosc_finalize_activation() {
        global $aiosc_capabilities;
        echo $aiosc_capabilities->partial_install();
        die();
    }
    function wp_head() {
        ?>
        <script>
        var AIOSC_AJAX_URL = '<?php echo admin_url('admin-ajax.php')?>';
        </script>
    <?php
    }
    function toolbar_new_tickets($wp_admin_bar) {
        $cnt = aiosc_TicketManager::count_new_tickets();
        if($cnt > 0) {
            $args = array(
                'id'    => 'aiosc',
                'title' => '<span class="ab-icon" title="'.sprintf(__('You have %d new tickets in Queue','aiosc'),$cnt).'"><i class="dashicons-before dashicons-tickets"></i></span><span class="ab-label">'.$cnt.'</span>',
                'href'  => aiosc_get_page_ticket_list(false,array('status'=>'queue')),
                'meta'  => array( 'class' => 'menupop' )
            );
            $wp_admin_bar->add_node( $args );
        }
    }
    /** TinyMCE */
    function mce_buttons($buttons, $editor_id) {
        $aiosc_editors = array('aiosc-content','aiosc-reply-content', 'aiosc-demo-wp_editor', 'aiosc-ticket-closure-content');
        if(in_array($editor_id,$aiosc_editors)) {
            $buttons = array('bold', 'italic', 'underline', 'strikethrough', 'bullist', 'numlist', 'link', 'unlink', 'spellchecker', 'fullscreen', 'wp_adv' );
        }
        return $buttons;
    }
    function mce_buttons_2($buttons, $editor_id) {
        $aiosc_editors = array('aiosc-content','aiosc-reply-content', 'aiosc-demo-wp_editor', 'aiosc-ticket-closure-content');
        if(in_array($editor_id,$aiosc_editors)) {
            $buttons = array( 'formatselect', 'forecolor', 'pastetext', 'removeformat', 'charmap', 'outdent', 'indent', 'undo', 'redo', 'wp_help' );
        }
        return $buttons;
    }
    /** ROLES  */
    public function user_bulk() {
        global $aiosc_capabilities, $aiosc_user;
        // Bail if current user cannot promote users
        if ( !current_user_can( 'promote_users' ) )
            return;

        // Get the roles
        $roles = $aiosc_capabilities->get_roles();

        // Only keymasters can set other keymasters
        if (!$aiosc_user->can('manage_options'))
            unset($roles[$aiosc_user->aiosc_role]);

        $rand = microtime() . rand(154123,9931512);
        ?>

        <label class="screen-reader-text" for="aiosc_new_role-<?php echo $rand?>"><?php esc_html_e( 'Change AIOSC role to&hellip;', 'aiosc' ) ?></label>
    <select name="aiosc_new_role" id="aiosc_new_role-<?php echo $rand?>" style="max-width: 100%; display: inline-block; float: none;">
        <option value=''><?php esc_html_e( 'Change AIOSC role to...', 'aiosc') ?></option>
        <?php foreach ( $roles as $id=>$data ) : ?>
            <option value="<?php echo $id; ?>"><?php echo $data['name']; ?></option>
        <?php endforeach; ?>
        </select><?php submit_button( __( 'Change', 'aiosc' ), 'secondary', 'ds-submit-role-change', false );
    }
    function user_role_bulk_change() {
        global $aiosc_user, $aiosc_capabilities;

        wp_enqueue_script('aiosc-user-bulk-script', AIOSC_URL . '/assets/js/user-bulk.js');

        if(!$aiosc_user->can('manage_options')) //only godfathers may change user roles
            return;
        if(!isset($_GET['users']) || empty($_GET['users']))
            return;
        if(!isset($_GET["aiosc_new_role"]))
            return;
        $new_role = $_GET["aiosc_new_role"];
        if(!empty($new_role) && !$aiosc_capabilities->role_exists($new_role))
            return;
        $users = $_GET['users'];
        foreach($users as $user_id) {
            if($aiosc_user != $user_id) {
                $user = new aiosc_User($user_id);
                $user->set_role($new_role);
            }
        }
        return;
    }

    /**
     * Display new column row on users.php page
     *
     * @see modify_user_table_row()
     * @update 1.1 - Added "Ticket Count" column
     * @param $column
     * @return mixed
     */
    function modify_user_table( $column ) {
        $column['aiosc_role'] = __('AIOSC role','aiosc');
        $column['aiosc_ticket_count'] = __('Tickets','aiosc');
        return $column;
    }

    /**
     * @update 1.1 - Added "Ticket Count" column value
	 * @update 2.1.6 - Fixed "return null" to "return $val"
     * @param $val
     * @param $column_name
     * @param $user_id
     * @return null|string|void
     */
    function modify_user_table_row( $val, $column_name, $user_id ) {
        global $aiosc_capabilities;
        $user = new aiosc_User( $user_id );

        switch ($column_name) {
            case 'aiosc_role' :
                return $aiosc_capabilities->get_role_name($user->aiosc_role);
                break;
            case 'aiosc_ticket_count' :
                //http://diwave-coders.com/wp-admin/admin.php?page=aiosc-list&author=98
                $cnt = aiosc_TicketManager::get_count_by(array('author_id'=>$user_id));
                if($cnt < 1) return $cnt;
                else return '<a href="'.aiosc_get_page_ticket_list(false, array('author'=>$user_id)).'">'.$cnt.'</a>';
                break;
            default:
        }
        return $val;
    }
    function editable_roles( $all_roles = array() ) {
        global $aiosc_capabilities;
        $roles = $aiosc_capabilities->get_roles();
        foreach($all_roles as $k=>$v) {
            if(array_key_exists($k,$roles)) {
                unset($all_roles[$k]);
            }
        }
        return $all_roles;
    }

    /**
     * Update AIOSC roles whenever WP calls set_role
     * @hook set_user_role
     * @param $user_id
     * @param string $role_id
     * @param array $old_roles
     */
    function set_user_roles($user_id,$role_id="",$old_roles=array()) {
        global $aiosc_capabilities;
        if(empty($role_id)) return;
        $u = new aiosc_user($user_id);
        //get plugin role from old roles
        foreach($old_roles as $role) {
            if($aiosc_capabilities->role_exists($role)) {
                $u->wpUser->add_role($role);
                return;
            }
        }
    }

    /**
     * Add default AIOSC role to newly registered user
     *
     * @hook user_register
     * @param $user_id
     */
    function hook_save_user_role_register($user_id) {
        global $aiosc_user, $aiosc_settings, $aiosc_capabilities;
		
		if(is_wp_error($user_id)) return;
		
        $user = new aiosC_User($user_id);
        $role = $aiosc_settings->get("default_role");
        if(!$role) return;
        if($aiosc_capabilities->role_exists($role)) {
            if($aiosc_capabilities->role_has_cap('staff', $role)) return;
            $user->wpUser->add_role($role);
        }
    }
    /**
     * Adds "AIOSC ROLE" field to User Profile page
     *
     * @hook edit_user_profile
     * @param $wp_user
     */
    function aiosc_role_user_page($wp_user) {
        global $aiosc_capabilities;
        $user = new aiosc_User($wp_user->ID);
        $roles = $aiosc_capabilities->get_roles();
        ?>
        <h3><?php _e('AIO Support Center','aiosc'); ?></h3>
        <table class="form-table">
            <input type="hidden" name="aiosc_curr_user_id" value="<?php echo $wp_user->ID ?>" />
            <tr>
                <th><label for="aiosc-role"><?php printf(__('AIO Support Center Role', 'aiosc')); ?></label></th>
                <td>
                    <select id="aiosc-role" name="aiosc_role">
                        <option value=""><?php _e('- No role for this user -','aiosc')?></option>
                        <?php
                        foreach($roles as $role_id=>$data) :
                            $selected = @$user->aiosc_role == $role_id?" selected":"";
                            echo "<option value='$role_id'$selected>".$data['name']."</option>";
                            ?>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
    <?php
    }
    function aiosc_role_user_page_secondary($args=array()) {
        $id = @$_POST['aiosc_curr_user_id'];
        if($id > 0)
            do_action('edit_user_profile_update',$id);
    }
    /**
     * Saves AIOSC role for user (on User Profile page)
     *
     * @hook edit_user_profile_update
     * @param $user_id
     */
    function save_aiosc_role($user_id) {
        global $aiosc_capabilities;
        if(!current_user_can("edit_users",$user_id)) {
            return;
        }
        $user = new aiosc_User($user_id);
        $role = @$_POST['aiosc_role'];
        $roles = $aiosc_capabilities->get_roles();
        //remove AIOSC roles
        foreach($roles as $r=>$data) {
            $user->wpUser->remove_role($r);
        }
        //if new role is valid AIOSC role, assign it to user
        if($aiosc_capabilities->role_exists($role)) {
            $user->wpUser->add_role($role);
        }
    }
    /** AJAX */
    //save screen options for Ticket List
    function aiosc_screen_options_tickets() {
        $fields = array('id','status','replies','department','priority','author','operator', 'last_update', 'date_created');
        foreach($fields as $f) {
            if(aiosc_pg($f.'-hide')) aiosc_cookie_set($f.'-hide',false, 0);
            else aiosc_cookie_set($f.'-hide',true);
        }
        $tags = array('awaiting_reply', 'requested_closure', 'attachments');
        foreach($tags as $tag) {
            if(aiosc_pg('tag-'.$tag.'-hide')) aiosc_cookie_set('tag-'.$tag.'-hide',false, 0);
            else aiosc_cookie_set('tag-'.$tag.'-hide',true);
        }
        aiosc_cookie_set('tickets-per-page',@$_POST['tickets-per-page']);
    }
    function new_ticket() {
        global $aiosc_user;
        if(isset($_POST['ticket_id']) && $aiosc_user->can('edit_ticket',array('ticket_id'=>$_POST['ticket_id']))) {
            $ticket_id = $_POST['ticket_id'];
            $operator_id = isset($_POST['operator'])?$_POST['operator']:0;
            //change status from closed to open, if set
            if(isset($_POST['reopen'])) {
                aiosc_TicketManager::reopen_ticket($ticket_id, false);
            }
        }
        else {
            $ticket_id = 0;
            $operator_id = 0;
        }
        if($aiosc_user->can('create_tickets') && isset($_POST['author']))
            $author_id = $_POST['author'];
        else $author_id = 0;

        echo aiosc_TicketManager::update_ticket($ticket_id, $author_id, @$_POST['subject'], @$_POST['content'], @$_POST['department'],$operator_id, @$_POST['priority'], @$_FILES['attachments'], isset($_POST['is_public']), isset($_POST['frontend']));
        die();
    }
    function new_reply() {
        global $aiosc_user;
        if((isset($_POST['ticket_id']) && isset($_POST['reply_id'])) && $aiosc_user->can('edit_ticket',array('ticket_id'=>$_POST['ticket_id'])))
            $reply_id = $_POST['reply_id'];
        else $reply_id = 0;
        $res = aiosc_ReplyManager::update_reply($reply_id, @$_POST['ticket_id'], @$_POST['content'], @$_FILES['attachments']);
        if(aiosc_is_error($res)) {
            echo $res;
            die();
        }
        else {
            $res = json_decode($res);
            $html = aiosc_ReplyManager::load_ajax_replies($_POST['ticket_id'], 0, 1, @$_POST['frontend']);
            $html = json_decode($html);
            $res->data['html'] = $html->data->html;
            $res = json_encode($res);
            echo $res;
            die();
        }
    }
    function load_replies() {
        echo aiosc_ReplyManager::load_ajax_replies(@$_POST['ticket_id'], @$_POST['from'], apply_filters('aiosc_reply_limit',3), @$_POST['frontend']);
        die();
    }
    function request_closure() {
        echo aiosc_TicketManager::request_closure(@$_POST['ticket_id']);
        die();
    }
    function close_ticket() {
        echo aiosc_TicketManager::close_ticket(@$_POST['ticket_id'], @$_POST['content'], isset($_POST['notify_customer']));
        die();
    }
    function reopen_ticket() {
        global $aiosc_user;
        $notify = !$aiosc_user->can('staff') ? true : isset($_POST['notify_customer']);
        echo aiosc_TicketManager::reopen_ticket(@$_POST['ticket_id'], $notify);
        die();
    }
    function reply_remove() {
        echo aiosc_ReplyManager::remove(@$_POST['reply_id']);
        die();
    }
    function load_premade_response() {
        global $aiosc_user;
        $response = new aiosc_PremadeResponse(@$_POST['response_id']);
        if(!aiosc_is_premade_response($response) || ($response->author_id != $aiosc_user->ID && !$response->is_shared))  {
            echo aiosc_response(0,AIOSC_PERMISSION_ERROR);
            die();
        }
        else {
            echo aiosc_response(1,'',array('html'=>$response->content));
            die();
        }
    }
    function load_operator_list() {
        $dep = new aiosc_Department(@$_POST['department_id']);
        if(!aiosc_is_department($dep) || !$dep->is_active) {
            echo aiosc_response(0,'',array('html'=>'<option value="0">'.__('- Pick operator -','aiosc').'</option>'));
            die();
        }
        else {
            $ops = $dep->get_ops(true);
            if(empty($ops) || $ops === false) {
                echo aiosc_response(0,'',array('html'=>'<option value="0">'.__('- Pick operator -','aiosc').'</option>'));
                die();
            }
            else {
                $html = '<option value="0">'.__('- Pick operator -','aiosc').'</option>';
                foreach($ops as $op) {
                    $html .= "<option value='".$op->ID."'>".$op->wpUser->display_name."</option>";
                }
                echo aiosc_response(1,'',array('html'=>$html));
                die();
            }
        }
    }

    function tickets_list() {
        global $aiosc_user, $aiosc_settings, $wpdb;
        $screens = array(
            'all'=>'admin/ticket/list/table.php',
            'queue'=>'admin/ticket/list/table.php',
            'open'=>'admin/ticket/list/table.php',
            'closed'=>'admin/ticket/list/table.php'
        );
        $bulkactions = array(
            'edit'=>'admin/ticket/list/table-confirm.php',
            'delete'=>'admin/ticket/list/table-confirm.php'
        );
        $confirms = array('edit','delete');
        if(!isset($_POST['status']) || !array_key_exists($_POST['status'], $screens)) {
            echo aiosc_response(0,__("<strong>Error:</strong> Selected tab doesn't exist.",'aiosc'));
            die();
        }
        else {
            if(isset($_POST['bulkaction-submit']) && array_key_exists($_POST['bulkaction'],$bulkactions)
                && isset($_POST['tickets']) && count($_POST['tickets']) > 0) {
                echo aiosc_response(1,'',array('html'=>aiosc_load_template($bulkactions[$_POST['bulkaction']])));
                die();
            }
            elseif(
                isset($_POST['ticket-confirmation-submit']) &&
                @$_POST['section'] == 'tickets-confirmation' &&
                isset($_POST['confirmation']) &&
                in_array($_POST['confirmation'],$confirms)) {

                if(count(@$_POST['tickets']) < 1) {
                    echo aiosc_response(1,'',array('html'=>aiosc_load_template($screens[$_POST['status']])));
                    die();
                }
                else {
                    $res_data = '';
                    if($_POST['confirmation'] == 'edit') {
                        $res = aiosc_TicketManager::quick_edit(@$_POST['tickets'], @$_POST['new_department'], @$_POST['new_operator'], @$_POST['new_priority'], @$_POST['new_visibility']);
                    }
                    elseif($_POST['confirmation'] == 'delete') {
                        $res = aiosc_TicketManager::delete_tickets(@$_POST['tickets'], isset($_POST['delete_attachments']));
                    }
                    $res_data = json_decode($res);
                    $html = '<br />';
                    if(aiosc_is_error($res))
                        $html .= "<div class='error'><p>$res_data->message</p></div>";
                    else $html .= "<div class='updated'><p>$res_data->message</p></div>";
                    $html .= aiosc_load_template($bulkactions[$_POST['confirmation']]);
                    echo aiosc_response(1,'',array('html'=>$html));
                    die();
                }
            }
            else {
                if(isset($_POST['frontend'])) {
                    $html = '';
                    $file = aiosc_get_template_path('shortcodes/list/table.php',true);
                    if(file_exists($file)) {
                        ob_start();

                        $items_per_page = aiosc_tickets_per_page();
                        $current_page = aiosc_pg('paged',false,1) > 0 ? aiosc_pg('paged',false,1) : 1;

                        //sorting
                        $ending_query = '';
                        $order = strtolower(aiosc_pg('order')) == 'desc'?'desc':'asc';
                        if(in_array(aiosc_pg('sort'),aiosc_TicketManager::get_columns())) $ending_query = 'ORDER BY '.esc_sql(aiosc_pg('sort')).' ';
                        if(!empty($ending_query)) $ending_query .= $order;

                        $c_query = "SELECT COUNT(*) FROM `".aiosc_get_table(aiosc_tables::tickets)."` ";
                        if(aiosc_pg('is_public') != 'Y' && !$aiosc_user->can('staff')) $c_query .= " WHERE author_id=$aiosc_user->ID ";
                        $ticket_count = $wpdb->get_var( $c_query . aiosc_get_query(aiosc_pg('is_public') == 'Y' || $aiosc_user->can('staff')));

                        //get LIMIT (from where to start)
                        $ticket_limit = ($current_page - 1) * $items_per_page;
                        if($ticket_limit < 0) $ticket_limit = 0;

                        $ending_query .= " LIMIT $ticket_limit, $items_per_page";

                        $query = 'SELECT * FROM `'.aiosc_get_table(aiosc_tables::tickets).'`';
                        if(aiosc_pg('is_public') != 'Y' && !$aiosc_user->can('staff')) $query .= " WHERE author_id=$aiosc_user->ID ";
                        $tickets = aiosc_TicketManager::get_tickets($query . aiosc_get_query(aiosc_pg('is_public') == 'Y' || $aiosc_user->can('staff')) . $ending_query);

                        include $file;
                        $html = ob_get_clean();
                    }

                    echo aiosc_response(1,'',array('html'=>$html));
                    die();
                }
                echo aiosc_response(1,'',array('html'=>aiosc_load_template($screens[$_POST['status']])));
                die();
            }
        }
    }
    /**
     * Loads preferences tab template from defined $_POST['screen']
     * @POST['screen'] - determines which screen we are loading
     */
    function preferences_screen() {
        $screens = array(
            'general'=>'admin/preferences/general.php',
            'tickets'=>'admin/preferences/tickets.php',
            'departments'=>'admin/preferences/departments.php',
            'departments-new'=>'admin/preferences/departments-new.php',
            'priorities'=>'admin/preferences/priorities.php',
            'priorities-new'=>'admin/preferences/priorities-new.php',
            'email'=>'admin/preferences/email.php',
            'email-piping'=>'admin/preferences/email-piping.php',
            //email-templates
            'email-templates-customer-creation'=>'admin/preferences/email-templates/customer-creation.php',
            'email-templates-customer-reply'=>'admin/preferences/email-templates/customer-reply.php',
            'email-templates-customer-closure'=>'admin/preferences/email-templates/customer-closure.php',
            'email-templates-customer-reopen'=>'admin/preferences/email-templates/customer-reopen.php',
            'email-templates-cron-reminder-queue'=>'admin/preferences/email-templates/cron-reminder-queue.php',
            'email-templates-cron-reminder-inactivity'=>'admin/preferences/email-templates/cron-reminder-inactivity.php',
            //email-templates STAFF
            'email-templates-staff-creation'=>'admin/preferences/email-templates/staff-creation.php',
            'email-templates-staff-reply'=>'admin/preferences/email-templates/staff-reply.php',
            'email-templates-staff-closure'=>'admin/preferences/email-templates/staff-closure.php',
            'email-templates-staff-reopen'=>'admin/preferences/email-templates/staff-reopen.php',
            //pages
            'pages'=>'admin/preferences/pages/general.php',
            //cron

            'cron'=>'admin/preferences/cron/general.php',
            'cron-autoclose'=>'admin/preferences/cron/auto-close.php',
            'cron-reminder-queue'=>'admin/preferences/cron/reminder-queue.php',
            'cron-reminder-inactivity'=>'admin/preferences/cron/reminder-inactivity.php',

            'addons'=>'admin/preferences/addons.php'
        );
        //ADDONS
        $addons = aiosc_AddonManager::get_addon_pages();

        if(isset($_POST['screen']) && array_key_exists($_POST['screen'], $screens)) {
            echo aiosc_response(1,'',array('html'=>aiosc_load_template($screens[$_POST['screen']])));
            die();
        }
        elseif(isset($_POST['screen']) && array_key_exists(str_replace('aiosc-addonscreen-','',$_POST['screen']),$addons)) {
            $addon = str_replace('aiosc-addonscreen-','',$_POST['screen']);
            $callback = $addons[$addon]['display_callback'];

            ob_start();
            echo aiosc_load_template('admin/preferences/addons/header.php');
            echo call_user_func($callback);
            $html = ob_get_clean();
            echo aiosc_response(1,'',array('html'=>$html));
            die();
        }
        else {
            echo aiosc_response(0,__("<strong>Error:</strong> Selected tab doesn't exist.",'aiosc'));
            die();
        }
    }
    function account_screen() {
        $screens = array(
            'general'=>'admin/my-account/general.php',
            'premade-responses'=>'admin/my-account/premade-responses.php',
            'premade-responses-new'=>'admin/my-account/premade-responses-new.php'
        );
        if(isset($_POST['screen']) && array_key_exists($_POST['screen'], $screens)) {
            echo aiosc_response(1,'',array('html'=>aiosc_load_template($screens[$_POST['screen']])));
            die();
        }
        else {
            echo aiosc_response(0,__("<strong>Error:</strong> Selected tab doesn't exist.",'aiosc'));
            die();
        }
    }

    /**
     * Save settings from Preferences admin page
     * @POST['section'] - determines which tab we are saving
     */
    function preferences_save() {
        global $aiosc_settings, $aiosc_user, $aiosc_capabilities;
        if(!$aiosc_user->can('manage_options') || aiosc_is_demo()) {
            echo aiosc_response(0,AIOSC_PERMISSION_ERROR);
            die();
        }
        $sections = array(
            'general',
            'tickets',
            'email','email-piping',
            'email-templates',
            'email-templates-customer-creation',
            'email-templates-customer-reply',
            'email-templates-customer-closure',
            'email-templates-customer-reopen',
            'email-templates-staff-creation',
            'email-templates-staff-reply',
            'email-templates-staff-closure',
            'email-templates-staff-reopen',
            'email-templates-cron-reminder-queue',
            'email-templates-cron-reminder-inactivity',
            'pages',
            'cron-general',
            'cron-autoclose',
            'cron-reminder-queue',
            'cron-reminder-inactivity',
            'addons',
            'departments-new','departments-new-update','departments-edit',
            'departments-update', //in combination with $_POST['confirmation'] which can be delete / activate / deactivate
            'priorities-new','priorities-new-update','priorities-edit',
            'priorities-update' //in combination with $_POST['confirmation'] which can be delete / activate / deactivate
        );


        $p = $_POST;
        $s = $_POST['section'];

        //ADDONS
        $addons = aiosc_AddonManager::get_addon_pages();
        if(isset($_POST['section']) && in_array($_POST['section'], $sections)) {

            $aiosc_settings->load_settings();

            if($s == 'general') { //GENERAL
                $aiosc_settings->set('enable_hints',isset($p['enable_hints']));
                $aiosc_settings->set('enable_reply_ribbon',isset($p['enable_reply_ribbon']));
                $aiosc_settings->set('enable_staff_ribbon',isset($p['enable_staff_ribbon']));
                $aiosc_settings->set('enable_public_tickets',isset($p['enable_public_tickets']));
                $aiosc_settings->set('allow_upload',isset($p['allow_upload']));
                $aiosc_settings->set('allow_download',isset($p['allow_download']));
                $aiosc_settings->set('max_upload_size_per_file',$p['max_upload_size_per_file'] > 0?$p['max_upload_size_per_file']:1);
                $aiosc_settings->set('max_files_per_ticket',$p['max_files_per_ticket'] > 0?$p['max_files_per_ticket']:1);
                $aiosc_settings->set('max_files_per_reply',$p['max_files_per_reply'] > 0?$p['max_files_per_reply']:1);
                $aiosc_settings->set('upload_mimes',@$p['upload_mimes']);
                $aiosc_settings->set('upload_mimes_forbid',isset($p['upload_mimes_forbid']));

                if(isset($p['default_role']) && array_key_exists($p['default_role'],$aiosc_capabilities->get_roles()))
                    $aiosc_settings->set('default_role',$p['default_role']);
                else $aiosc_settings->set('default_role','');
            }
            elseif($s == 'tickets') { //TICKETS
                $nbox_pos = array('top-left','top-right','bottom-left','bottom-right');
                $aiosc_settings->set('min_subject_len',@(int)$p['min_subject_len']);
                $aiosc_settings->set('min_content_len',@(int)$p['min_content_len']);
                $aiosc_settings->set('creation_delay',@(int)$p['creation_delay']);
                $aiosc_settings->set('allow_reopen_tickets', isset($p['allow_reopen_tickets']));

                //reply
                $aiosc_settings->set('min_reply_len',@(int)$p['min_reply_len']);
                $aiosc_settings->set('reply_delay',@(int)$p['reply_delay']);

            }
            /** @DEPARTMENTS */
            elseif($s == 'departments-new' || $s == 'departments-edit') { //DEPARTMENTS - NEW DEPARTMENT / EDIT DEPARTMENT
                $res = aiosc_DepartmentManager::update_department(@$p['department_id'], @$p['name'], @$p['description'], @$p['ops'], isset($p['active']));

                if(!aiosc_is_error($res)) {
                    $res = json_decode($res,true);
                    $res['data']['html'] = aiosc_load_template('admin/preferences/departments-new.php');
                    $res = json_encode($res);
                    echo $res;
                }
                else {
                    echo $res;
                }
                die();
            }
            elseif($s == 'departments-new-update') {
                echo aiosc_response(1,aiosc_preclean_content(@$p['message']),array('html'=>aiosc_load_template('admin/preferences/departments-new.php')));
                die();
            }
            elseif($s == 'departments-update') {
                //delete / activate / deactivate

                if($p['confirmation'] == 'delete') {
                    $deps = @$_POST['departments'];
                    $new_dep = @$_POST['new_department'];
                    if(!is_numeric($new_dep)) $new_dep = 0;
                    $tmp_res = array();
                    foreach($deps as $dep) {
                        $res = aiosc_DepartmentManager::delete_department($dep, $new_dep);
                        if(aiosc_is_error($res)) {
                            echo $res;
                            die();
                        }
                        else {
                            $res = json_decode($res);
                            $tmp_res[] = @$res->message;
                        }
                    }
                    echo aiosc_response(1,implode('<br>',$tmp_res));
                    die();
                }
                elseif($p['confirmation'] == 'activate') {
                    echo aiosc_DepartmentManager::activate_departments(@$p['departments']);
                    die();
                }
                elseif($p['confirmation'] == 'deactivate') {
                    echo aiosc_DepartmentManager::activate_departments(@$p['departments'], false);
                    die();
                }
                else {
                    echo aiosc_response(0,AIOSC_PERMISSION_ERROR);
                    die();
                }
            }
            /** @PRIORITIES */
            elseif($s == 'priorities-new' || $s == 'priorities-edit') { //PRIORITIES - NEW PRIORITY / EDIT PRIORITY
                $res = aiosc_PriorityManager::update_priority(@$p['priority_id'], @$p['name'], @$p['description'], @$p['level'], @$p['color'], isset($p['active']));

                if(!aiosc_is_error($res)) {
                    $res = json_decode($res,true);
                    $res['data']['html'] = aiosc_load_template('admin/preferences/priorities-new.php');
                    $res = json_encode($res);
                    echo $res;
                }
                else {
                    echo $res;
                }
                die();
            }
            elseif($s == 'priorities-new-update') {
                echo aiosc_response(1,aiosc_preclean_content(@$p['message']),array('html'=>aiosc_load_template('admin/preferences/priorities-new.php')));
                die();
            }
            elseif($s == 'priorities-update') {
                //delete / activate / deactivate

                if($p['confirmation'] == 'delete') {
                    $pris = @$_POST['priorities'];
                    $new_pri = @$_POST['new_priority'];
                    if(!is_numeric($new_pri)) $new_pri = 0;
                    $tmp_res = array();
                    foreach($pris as $pri) {
                        $res = aiosc_PriorityManager::delete_priority($pri, $new_pri);
                        if(aiosc_is_error($res)) {
                            echo $res;
                            die();
                        }
                        else {
                            $res = json_decode($res);
                            $tmp_res[] = @$res->message;
                        }
                    }
                    echo aiosc_response(1,implode('<br>',$tmp_res));
                    die();
                }
                elseif($p['confirmation'] == 'activate') {
                    echo aiosc_PriorityManager::activate_priorities(@$p['priorities']);
                    die();
                }
                elseif($p['confirmation'] == 'deactivate') {
                    echo aiosc_PriorityManager::activate_priorities(@$p['priorities'], false);
                    die();
                }
                else {
                    echo aiosc_response(0,AIOSC_PERMISSION_ERROR);
                    die();
                }
            }
            /** @EMAIL-SETTINGS */

            /** @EMAIL-SETTINGS - Auto Responders */
            elseif($s == 'email') {

                $aiosc_settings->set('email_ar_customer_ticket_creation',isset($p['email_ar_customer_ticket_creation']));
                $aiosc_settings->set('email_ar_customer_ticket_reply',isset($p['email_ar_customer_ticket_reply']));
                $aiosc_settings->set('email_ar_customer_ticket_close',isset($p['email_ar_customer_ticket_close']));
                $aiosc_settings->set('email_ar_customer_ticket_reopen', isset($p['email_ar_customer_ticket_reopen']));

                $aiosc_settings->set('email_ar_staff_ticket_creation',isset($p['email_ar_staff_ticket_creation']));
                $aiosc_settings->set('email_ar_staff_ticket_reply',isset($p['email_ar_staff_ticket_reply']));
                $aiosc_settings->set('email_ar_staff_ticket_close',isset($p['email_ar_staff_ticket_close']));
                $aiosc_settings->set('email_ar_staff_ticket_reopen', isset($p['email_ar_staff_ticket_reopen']));
            }
            elseif($s == 'email-piping') {

                $aiosc_settings->set('email_piping_enable',isset($p['email_piping_enable']));
                $aiosc_settings->set('email_piping_domain',aiosc_pg('email_piping_domain',true,''));
                $aiosc_settings->set('email_piping_enable_html',isset($p['email_piping_enable_html']));

                /** @since 2.0 */
                $aiosc_settings->set('email_piping_support_addr', aiosc_is_email(@$p['email_piping_support_addr']) ? @$p['email_piping_support_addr'] : '');
                $aiosc_settings->set('email_piping_creation_department', @$p['email_piping_creation_department']);
                $aiosc_settings->set('email_piping_creation_priority', @$p['email_piping_creation_priority']);
            }
            /** @EMAIL-TEMPLATES - Customer Ticket Creation */
            elseif($s == 'email-templates-customer-creation') {
                $aiosc_settings->set('email_templates_customer_creation_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_customer_creation_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_customer_creation_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_customer_creation_content') : @$p['aiosc-content']);
            }
            elseif($s == 'email-templates-customer-reply') {
                $aiosc_settings->set('email_templates_customer_reply_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_customer_reply_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_customer_reply_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_customer_reply_content') : @$p['aiosc-content']);
            }
            elseif($s == 'email-templates-customer-closure') {
                $aiosc_settings->set('email_templates_customer_closure_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_customer_closure_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_customer_closure_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_customer_closure_content') : @$p['aiosc-content']);
            }
            elseif($s == 'email-templates-customer-reopen') {
                $aiosc_settings->set('email_templates_customer_reopen_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_customer_reopen_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_customer_reopen_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_customer_reopen_content') : @$p['aiosc-content']);
            }
            elseif($s == 'email-templates-cron-reminder-queue') {
                $aiosc_settings->set('email_templates_cron_reminder_queue_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_cron_reminder_queue_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_cron_reminder_queue_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_cron_reminder_queue_content') : @$p['aiosc-content']);
            }
            elseif($s == 'email-templates-cron-reminder-inactivity') {
                $aiosc_settings->set('email_templates_cron_reminder_inactivity_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_cron_reminder_inactivity_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_cron_reminder_inactivity_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_cron_reminder_inactivity_content') : @$p['aiosc-content']);
            }
            elseif($s == 'email-templates-staff-creation') {
                $aiosc_settings->set('email_templates_staff_creation_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_staff_creation_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_staff_creation_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_staff_creation_content') : @$p['aiosc-content']);
            }
            elseif($s == 'email-templates-staff-reply') {
                $aiosc_settings->set('email_templates_staff_reply_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_staff_reply_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_staff_reply_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_staff_reply_content') : @$p['aiosc-content']);
            }
            elseif($s == 'email-templates-staff-closure') {
                $aiosc_settings->set('email_templates_staff_closure_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_staff_closure_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_staff_closure_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_staff_closure_content') : @$p['aiosc-content']);
            }
            elseif($s == 'email-templates-staff-reopen') {
                $aiosc_settings->set('email_templates_staff_reopen_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_staff_reopen_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_staff_reopen_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_staff_reopen_content') : @$p['aiosc-content']);
            }
            elseif($s == 'email-templates-cron-reminder-inactivity') {
                $aiosc_settings->set('email_templates_cron_reminder_inactivity_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_cron_reminder_inactivity_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_cron_reminder_inactivity_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_cron_reminder_inactivity_content') : @$p['aiosc-content']);
            }
            elseif($s == 'email-templates-cron-reminder-queue') {
                $aiosc_settings->set('email_templates_cron_reminder_queue_subject', @$p['email-subject'] == '' ? $aiosc_settings->get_default('email_templates_cron_reminder_queue_subject') : @$p['email-subject']);
                $aiosc_settings->set('email_templates_cron_reminder_queue_content', @$p['aiosc-content'] == '' ? $aiosc_settings->get_default('email_templates_cron_reminder_queue_content') : @$p['aiosc-content']);
            }
            /** @PAGE-SETTINGS */
            elseif($s == 'pages') {
                $aiosc_settings->set('pages_frontend_enable',isset($p['pages_frontend_enable']));
                $aiosc_settings->set('page_ticket_form',@$p['page_ticket_form']);
                $aiosc_settings->set('page_ticket_preview',@$p['page_ticket_preview']);
                $aiosc_settings->set('page_ticket_list',@$p['page_ticket_list']);
            }
            /** @CRON-SETTINGS
             * @since 2.0
             */
            elseif($s == 'cron-general') {
                $aiosc_settings->set('cron_enable', isset($p['cron_enable']));
            }
            elseif($s == 'cron-autoclose') {
                $aiosc_settings->set('cron_autoclose_enable', isset($p['cron_autoclose_enable']));
                $aiosc_settings->set('cron_autoclose_notify_customer', isset($p['cron_autoclose_notify_customer']));
                $aiosc_settings->set('cron_autoclose_closure_note', @$p['content']);
                $aiosc_settings->set('cron_autoclose_requested_closure_note', @$p['content-2']);
                $aiosc_settings->set('cron_autoclose_interval', (int)@$p['cron_autoclose_interval']);
                $aiosc_settings->set('cron_autoclose_requested_closure', isset($p['cron_autoclose_requested_closure']));
                $aiosc_settings->set('cron_autoclose_ignore_departments', @$p['cron_autoclose_ignore_departments']);

                $aiosc_settings->set('cron_reminder_inactivity_enable', isset($p['cron_reminder_inactivity_enable']));
                $aiosc_settings->set('cron_reminder_inactivity_interval', (int)@$p['cron_reminder_inactivity_interval']);
            }
            elseif($s == 'cron-reminder-queue') {
                $aiosc_settings->set('cron_reminder_queue_enable', isset($p['cron_reminder_queue_enable']));
                $aiosc_settings->set('cron_reminder_queue_interval', (int)@$p['cron_reminder_queue_interval']);
                $aiosc_settings->set('cron_reminder_queue_include_open', isset($p['cron_reminder_queue_include_open']));
                $aiosc_settings->set('cron_reminder_queue_ignore_departments', @$p['cron_reminder_queue_ignore_departments']);
            }
            elseif($s == 'cron-reminder-inactivity') {
                $aiosc_settings->set('cron_reminder_inactivity_enable', isset($p['cron_reminder_inactivity_enable']));
                $aiosc_settings->set('cron_reminder_inactivity_interval', (int)@$p['cron_reminder_inactivity_interval']);
                $aiosc_settings->set('cron_reminder_inactivity_ignore_departments', @$p['cron_reminder_inactivity_ignore_departments']);
            }
            $aiosc_settings->save_settings();
            echo aiosc_response(1,__('Settings were saved successfully.','aiosc'));
            die();
        }
        elseif(isset($_POST['section']) && array_key_exists(str_replace('aiosc-addonscreen-','',$_POST['section']),$addons)) {
            $aiosc_settings->load_settings();

            $addon = str_replace('aiosc-addonscreen-','',$_POST['section']);
            $callback = @$addons[$addon]['save_callback'];

            call_user_func($callback);

            $aiosc_settings->save_settings();
            echo aiosc_response(1,__('Settings were saved successfully.','aiosc'));
            die();
        }
        else {
            echo aiosc_response(0,__("Cheatin' huh?.",'aiosc'));
            die();
        }
    }
    function account_save() {
        global $aiosc_settings, $aiosc_user, $aiosc_capabilities;
        if(!$aiosc_user->can('staff') || aiosc_is_demo()) {
            echo aiosc_response(0,AIOSC_PERMISSION_ERROR);
            die();
        }
        $sections = array(
            'general',
            'premade-responses-new','premade-responses-new-update','premade-responses-edit',
            'premade-responses-update' //in combination with $_POST['confirmation'] which can be delete / activate / deactivate
        );

        $p = $_POST;
        $s = $_POST['section'];

        if(isset($_POST['section']) && in_array($_POST['section'], $sections)) {

            if($s == 'general') { //GENERAL
                if($aiosc_user->can('staff')) {
                    $aiosc_user->set_meta('aiosc_notifications', isset($_POST['aiosc_notifications'])?1:0);
                    /** @since 1.0.3 */
                    $aiosc_user->set_meta('aiosc_department_notifications',isset($_POST['aiosc_department_notifications']) ? 1 : 0);
                    /** @since 1.0.9 */
                    $aiosc_user->set_meta('aiosc_staff_create_form_disable',isset($_POST['aiosc_staff_create_form_disable']) ? 1 : 0);
                }
            }
            elseif($s == 'premade-responses-new' || $s == 'premade-responses-edit') {
                $response_id = isset($_POST['response_id']) && is_numeric($_POST['response_id']) ? $_POST['response_id'] : 0;
                $res = aiosc_PremadeResponseManager::update_response($response_id,@$_POST['name'], @$_POST['aiosc-content'], isset($_POST['is_shared']));
                if(!aiosc_is_error($res)) {
                    $res = json_decode($res,true);
                    $res['data']['html'] = aiosc_load_template('admin/my-account/premade-responses-new.php');
                    $res = json_encode($res);
                    echo $res;
                }
                else {
                    echo $res;
                }
                die();
            }
            elseif($s == 'premade-responses-update') {
                if(@$_POST['action2'] == 'delete') {
                    echo aiosc_PremadeResponseManager::remove(@$_POST['responses']);
                }
                elseif(@$_POST['action2'] == 'private') {
                    echo aiosc_PremadeResponseManager::sharing(@$_POST['responses'], false);
                }
                elseif(@$_POST['action2'] == 'public') {
                    echo aiosc_PremadeResponseManager::sharing(@$_POST['responses'], true);
                }
                die();
            }
            echo aiosc_response(1,__('Account Settings were saved successfully.','aiosc'));
            die();
        }
        else {
            echo aiosc_response(0,__("Cheatin' huh?.",'aiosc'));
            die();
        }
    }
    function aiosc_priorities_update() {
        global $aiosc_user;
        $actions = array('delete','activate','deactivate');
        if(!in_array($_POST['action2'], $actions) || !$aiosc_user->can('manage_options') || aiosc_is_demo()) {
            echo aiosc_response(0,AIOSC_PERMISSION_ERROR);
            die();
        }
        else {
            echo aiosc_response(1,'',array('html'=>aiosc_load_template('admin/preferences/priorities-confirm.php')));
            die();
        }
    }
    function aiosc_departments_update() {
        global $aiosc_user;
        $actions = array('delete','activate','deactivate');
        if(!in_array($_POST['action2'], $actions) || !$aiosc_user->can('manage_options') || aiosc_is_demo()) {
            echo aiosc_response(0,AIOSC_PERMISSION_ERROR);
            die();
        }
        else {
            echo aiosc_response(1,'',array('html'=>aiosc_load_template('admin/preferences/departments-confirm.php')));
            die();
        }
    }
}
?>