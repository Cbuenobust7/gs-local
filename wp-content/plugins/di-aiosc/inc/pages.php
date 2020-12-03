<?php
class aiosc_Pages {
    function __construct() {
        add_action('admin_menu',array($this,'init_admin_pages'));

    }
    private function admin_assets() {
        wp_enqueue_style('aiosc-admin',AIOSC_URL."assets/css/admin.css");
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script('jquery-browser',AIOSC_URL.'assets/js/jquery.browser.min.js');
        wp_enqueue_script('aiosc-common',AIOSC_URL."assets/js/common.js", array( 'wp-color-picker' ), false, true );
        wp_enqueue_script('aiosc-downloader',AIOSC_URL.'assets/js/downloader.js');
        wp_enqueue_media('dashicons');
        do_action('aiosc_enqueue_admin');
    }
    /**
     * Add Admin pages to Back-end menu
     */
    function init_admin_pages() {
        //Support Center
        global $aiosc_user;
        if(aiosc_is_user($aiosc_user)) {

            if(get_option('aiosc_installed') != 'Y' && current_user_can('manage_options')) {
                add_menu_page(__('AIO Support Center - Finalize Activation','aiosc'),__('AIOSC<br />Finalize Activation','aiosc'),'read','aiosc-list',array($this,'admin_page_sc_install'), 'dashicons-tickets');
            }
            else {
                $new_tickets_count = aiosc_TicketManager::count_new_tickets();
                if($new_tickets_count > 0) {
                    $main_name = sprintf(__('Support %s','aiosc'),'<span class="awaiting-mod count-'.$new_tickets_count.'"><span class="pending-count">'.$new_tickets_count.'</span></span>');
                }
                else {
                    $main_name = __('Support Center','aiosc');
                }

                add_menu_page(__('AIO Support Center','aiosc'), $main_name, 'read', 'aiosc-list', array($this,'admin_page_sc_list'),'dashicons-tickets');

                add_submenu_page('aiosc-list',__('AIO Support Center - All Tickets','aiosc'),__('All Tickets','aiosc'),'read','aiosc-list',array($this,'admin_page_sc_list'));
                if(aiosc_pg('page',false) == 'aiosc-ticket-preview') {
                    add_submenu_page('aiosc-list',__('AIO Support Center - Ticket Preview','aiosc'),__('Ticket Preview','aiosc'),'read','aiosc-ticket-preview',array($this,'admin_page_sc_preview'));
                }
                if($aiosc_user->can('staff')) {
                    add_submenu_page('aiosc-list',__('AIO Support Center - My Account','aiosc'),__('My Account','aiosc'),'read','aiosc-account',array($this,'admin_page_sc_account'));
                }
                if((!$aiosc_user->can('staff') && $aiosc_user->can('create_ticket')) || ($aiosc_user->can('staff') && !$aiosc_user->get_meta('aiosc_staff_create_form_disable'))) {
                    add_submenu_page('aiosc-list',__('AIO Support Center - Create New','aiosc'),__('Create New','aiosc'),'read','aiosc-new',array($this,'admin_page_sc_new_ticket'));
                }
                do_action('aiosc_admin_menu');

                if($aiosc_user->can('manage_options')) {
                    add_submenu_page('aiosc-list',__('AIO Support Center - Preferences','aiosc'),__('Preferences','aiosc'),'read','aiosc-preferences',array($this,'admin_page_sc_preferences'));
                    add_submenu_page('aiosc-list',__('AIO Support Center - Tools','aiosc'),__('Tools','aiosc'),'read','aiosc-tools',array($this,'admin_page_sc_tools'));
                }
                if($aiosc_user->can('view_statistics')) {
                    //add_submenu_page('aiosc-list',__('AIO Support Center - Statistics','aiosc'),__('Statistics','aiosc'),'read','aiosc-statistics',array($this,'admin_page_sc_statistics'));
                }
            }
        }
    }
    /**
     * @since 1.0.7
     */
    function admin_page_sc_main() {
		
    }
    /** Admin Pages */
    function admin_page_sc_install() {
        $this->admin_assets();
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');
        wp_enqueue_script('aiosc-install',AIOSC_URL.'assets/js/install.js');
        wp_enqueue_style('aiosc-install',AIOSC_URL.'assets/css/install.css');


        /* Output */
        $tpl = aiosc_load_template('admin/install.php',true);
        echo $tpl;
    }
    /**
     * This page displays list of tickets for all users. Most of the code is in template file
     * @slug aiosc-list
     * @title AIO Support Center
     * @template templates/admin/ticket/list/page.php
     * @sub-template templates/admin/ticket/list/items.php
     */
    function admin_page_sc_list() {

        $this->admin_assets();
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');
        wp_enqueue_script('aiosc-tickets-list',AIOSC_URL.'assets/js/tickets-list.js');

        wp_enqueue_script('select2');
        wp_enqueue_style('select2');

        /* Output */
        $tpl = aiosc_load_template('admin/ticket/list/page.php',true);
        echo $tpl;
    }

    /**
     * This page displays details of an ticket (in backend)
     * @slug aiosc-ticket-preview
     * @title AIO Support Center - Ticket Preview
     * @template templates/admin/ticket/preview.php
     */
    function admin_page_sc_preview() {
        $this->admin_assets();
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');
        wp_enqueue_script('aiosc-uploader',AIOSC_URL.'assets/js/uploader.js');
        wp_enqueue_script('aiosc-ticket-prev',AIOSC_URL.'assets/js/ticket-preview.js');

        if(aiosc_pg('edit_mode')) {
            wp_enqueue_script('select2');
            wp_enqueue_style('select2');
        }

        $tpl = aiosc_load_template('admin/ticket/single/page.php',true);
        echo $tpl;
    }

    /**
     * This page displays ticket creation form
     * @slug aiosc-new
     * @title AIO Support Center - Create New
     * @template templates/admin/ticket/form.php
     */
    function admin_page_sc_new_ticket() {
        $this->admin_assets();
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');
        wp_enqueue_script('aiosc-uploader',AIOSC_URL.'assets/js/uploader.js');
        wp_enqueue_script('aiosc-new-ticket',AIOSC_URL.'assets/js/new-ticket.js');

        wp_enqueue_script('select2');
        wp_enqueue_style('select2');

        $tpl = aiosc_load_template('admin/ticket/form.php',true);
        echo $tpl;
    }

    function admin_page_sc_preferences() {
        $this->admin_assets();
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');
        wp_enqueue_script('aiosc-preferences',AIOSC_URL.'assets/js/preferences.js');
        $tpl = aiosc_load_template('admin/preferences.php',false);
        echo $tpl;
    }
    function admin_page_sc_tools() {
        $this->admin_assets();
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');
        wp_enqueue_script('aiosc-tools',AIOSC_URL.'assets/js/tools.js');
        $tpl = aiosc_load_template('admin/tools.php',false);
        echo $tpl;
    }

    function admin_page_sc_account() {
        $this->admin_assets();
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');
        wp_enqueue_script('aiosc-account',AIOSC_URL.'assets/js/account.js');
        $tpl = aiosc_load_template('admin/my-account.php',false);
        echo $tpl;
    }
}
/** Get URL of specific AIOSC page based on Page settings */
function aiosc_get_page_ticket_preview($ticket, $edit_mode = false, $frontend = false) {
    if(aiosc_is_ticket($ticket)) $ticket = $ticket->ID;
    global $aiosc_settings;
    if($frontend && $aiosc_settings->get('pages_frontend_enable'))
    	$page = $aiosc_settings->get('page_ticket_preview');
    else
    	$page = 0;

    $admin_page = aiosc_url_apply_query(get_admin_url(null,"/admin.php"), array('page'=>'aiosc-ticket-preview','ticket_id'=>$ticket));

    if($edit_mode) $admin_page = aiosc_url_apply_query($admin_page,array('edit_mode'=>1));
    if(!$frontend || ($frontend && $page < 1) || $edit_mode) {
        $url = $admin_page;
    }
    else {
        $page = get_permalink($page);
        if($page !== false) {
            $url = aiosc_url_apply_query($page,array('ticket_id'=>$ticket));
        }
        else
        	$url = $admin_page;
    }
    return apply_filters('aiosc_get_page_ticket_preview', $url, $ticket, $edit_mode, $frontend);
}
function aiosc_get_page_ticket_form($frontend=false, $additional_query=array()) {
    global $aiosc_settings;
    if($frontend && $aiosc_settings->get('pages_frontend_enable'))
    	$page = $aiosc_settings->get('page_ticket_form');
    else
    	$page = 0;
    $admin_page = aiosc_url_apply_query(get_admin_url(null,"/admin.php"),array('page'=>'aiosc-new'));
    $admin_page = aiosc_url_apply_query($admin_page,$additional_query);
    if(!$frontend || ($frontend && $page < 1))
    	$url = $admin_page;
    else {
        $page = get_permalink($page);
        $url = $page === false ? $admin_page : aiosc_url_apply_query($page,$additional_query);
    }
    return apply_filters('aiosc_get_page_ticket_form', $url, $frontend, $additional_query);
}
function aiosc_get_page_ticket_list($frontend=false, $additional_query=array()) {
    global $aiosc_settings;
    if($frontend && $aiosc_settings->get('pages_frontend_enable'))
    	$page = $aiosc_settings->get('page_ticket_list');
    else
    	$page = 0;
    $admin_page = aiosc_url_apply_query(get_admin_url(null,"/admin.php"),array('page'=>'aiosc-list'));
    $admin_page = aiosc_url_apply_query($admin_page,$additional_query);
    if(empty($page) || is_admin())
    	$url = $admin_page;
    else {
        $page = get_permalink($page);
        $url = $page === false ? $admin_page : aiosc_url_apply_query($page,$additional_query);
    }
    return apply_filters('aiosc_get_page_ticket_list', $url, $frontend, $additional_query);
}
function aiosc_url_apply_query($url, $query=array()) {
    $q = '';
    if(is_array($query) && !empty($query)) {
        $y = 0;
        foreach($query as $k=>$v) {
            if($y < count($query)) $q .= '&';
            $q .= $k.'='.$v;
            $y++;
        }
    }
    if(strpos($url,"?") !== false) $url .= $q;
    else $url .= "?".$q;

    return $url;
}

/**
 * Get profile URL of the user. Right now only backend, but might be
 * used for front-end in the future
 * @param aiosc_User|int $user
 * @return string
 */
function aiosc_get_page_user_profile($user) {
    global $aiosc_user;
    //if(aiosc_is_user($user)) $user = $user->ID;
    if(!is_numeric($user) && !aiosc_is_user($user)) return get_admin_url(null,"/profile.php");
    else {
        $id = is_numeric($user)?$user:$user->ID;
        if($id == $aiosc_user->ID) return get_admin_url(null,"/profile.php");
        return aiosc_url_apply_query(get_admin_url(null,"/user-edit.php"),array('user_id'=>$id));
    }
}