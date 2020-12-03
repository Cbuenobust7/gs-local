<?php
class aiosc_Settings {
    public $settings;
    public $defaults;
    public $meta_key;
    function __construct() {
        $this->meta_key = 'aiosc_settings';
        $this->defaults = $this->get_defaults();
        $this->load_settings();
    }
    function load_settings() {
        $this->settings = get_option($this->meta_key);
    }
    function save_settings() {
        update_option($this->meta_key,$this->settings);
    }
    function get($key,$default=true) {
        $key = strtolower($key);
        if(isset($this->settings) && isset($this->settings[$key])) {
            if($key == 'min_content_len' || $key == 'min_reply_len') {
                if($this->settings[$key] < $this->defaults[$key]) return $this->defaults[$key];
                else return $this->settings[$key];
            }
            return @$this->settings[$key];
        }
        else {
            if($default) {
                if(isset($this->defaults[$key])) return $this->defaults[$key];
                else return '';
            }
            return '';
        }
    }
    function get_default($key) {
        $key = strtolower($key);
        if(isset($this->defaults[$key])) return $this->defaults[$key];
        else return false;
    }
    function set($key,$value, $save=false) {
        $key = strtolower($key);
        $this->settings[$key] = $value;
        if($save) {
            $this->save_settings();
        }
    }
    /**
     * @return array
     */
    private function get_defaults() {
        $settings = array(
            //ticket settings
            'min_subject_len'=>10, //0 == not required. Minimum string length for Subject
            'min_content_len'=>10, //always required. if 0, minimum will be 20.
            'min_reply_len'=>10, //always required. if 0, minimum will be 10
            'allow_upload'=>true, //allow users to upload files to their tickets & replies
            'allow_download'=>true, //allow users to download files from tickets & replies
            'upload_mimes'=>'txt,doc,docx,rtf,pdf,jpg,png,bmp,gif,rtf,zip,rar,gz,7z,mp3,sql', //* == all are allowed, but PHP, BAT and EXE will always be disallowed for security reasons.
            'upload_mimes_forbid'=>false, //if true, upload_mimes will be FORBIDDEN, instead of ONLY ALLOWED
            'max_upload_size_per_file'=>4 * 1024, /** in kilobytes (but @max_upload_size must be set in php.ini as well) */
            'max_files_per_ticket'=>4, //minimum: 1 - how many files can user attach on a single ticket?
            'max_files_per_reply'=>4, //minimum: 1 - how many files can user attach on a single reply?
            'creation_delay'=>60, //in seconds. How much time needs to pass before user can create new ticket
            'reply_delay'=>20, //in seconds. How much time needs to pass before user can post new reply (does not apply to staff members)
            "max_queue_exclusive"=>10,//how many Queued tickets can Exclusive Customer have at the time? 0 == infinite
            "max_queue_customer"=>5, //how many Queued tickets can Customer have at the time? 0 == infinite
            "max_open_exclusive"=>10, //how many Open tickets can Exclusive Customer have at the time? 0 == infinite
            "max_open_customer"=>5, //how many Open tickets can Customer have at the time? 0 == infinite
            "allow_reopen_tickets"=>false, //allow customers to re-open closed tickets
            //role settings
            'default_role'=>'aiosc_customer', //user role that will be set to user on every new user registration
            //general
            'enable_hints'=>false, //show tooltips next to input fields
            'enable_staff_ribbon'=>true,
            'enable_public_tickets'=>false,

            //pages
            'pages_frontend_enable'=>false,
            'page_ticket_form'=>0,
            'page_ticket_preview'=>0,
            'page_ticket_list'=>0,

            //cron
            'cron_enable'=>false, //for any part of cron to work, this option must be enabled, otherwise nothing will work
            'cron_autoclose_enable'=>false, //enables Auto-Closing cron feature (cron_enable still has to be enabled)
            'cron_autoclose_notify_customer'=>true, //notify customer when ticket is closed
            'cron_autoclose_requested_closure_note'=>__('Ticket closed upon request.', 'aiosc'), //note for closure on Closure-Requested tickets
            'cron_autoclose_closure_note'=>__('Ticket closed due to inactivity.', 'aiosc'), //note for closure due to inactivity
            'cron_autoclose_interval'=>7, //7 days of inactivity
            'cron_autoclose_requested_closure'=>true, //auto-close tickets where customer requested closure, regardless of ticket age
            'cron_autoclose_ignore_departments'=>array(), //tickets from these departments wont be affected by Auto-Closing cron
            'cron_reminder_queue_enable'=>false,
            'cron_reminder_queue_interval'=>2,
            'cron_reminder_queue_include_open'=>true,
            'cron_reminder_queue_ignore_departments'=>array(),
            'cron_reminder_inactivity_enable'=>false,
            'cron_reminder_inactivity_interval'=>2,
            'cron_reminder_inactivity_ignore_departments'=>array(),
            //email
            'email_piping_enable'=>false,
            'email_piping_domain'=>'',
            'email_piping_enable_html'=>true,
            'email_piping_support_addr'=>'',
            'email_piping_creation_department'=>0,
            'email_piping_creation_priority'=>0,
            //email responders
            'email_ar_customer_ticket_creation'=>true,
            'email_ar_customer_ticket_reply'=>true,
            'email_ar_customer_ticket_close'=>true,
            'email_ar_customer_ticket_reopen'=>true,
            'email_ar_staff_ticket_creation'=>true,
            'email_ar_staff_ticket_reply'=>true,
            'email_ar_staff_ticket_close'=>true,
            'email_ar_staff__ticket_reopen'=>true,
            //email templates
            'email_templates_customer_creation_subject'=>__('[Ticket #{%ticket.id%}] New Ticket Created', 'aiosc'),
            'email_templates_customer_reply_subject'=>__('[Ticket #{%ticket.id%}] You got a new reply!', 'aiosc'),
            'email_templates_customer_closure_subject'=>__('[Ticket #{%ticket.id%}] Your ticket has been closed', 'aiosc'),
            'email_templates_customer_reopen_subject'=>__('[Ticket #{%ticket.id%}] Your ticket is re-opened', 'aiosc'),

            'email_templates_customer_creation_content'=>aiosc_get_data_file('email-templates/customer/creation.html'),
            'email_templates_customer_reply_content'=>aiosc_get_data_file('email-templates/customer/reply.html'),
            'email_templates_customer_closure_content'=>aiosc_get_data_file('email-templates/customer/closure.html'),
            'email_templates_customer_reopen_content'=>aiosc_get_data_file('email-templates/customer/reopen.html'),

            'email_templates_staff_creation_subject'=>__('[Ticket #{%ticket.id%}] You\'ve been assigned to new ticket', 'aiosc'),
            'email_templates_staff_reply_subject'=>__('[Ticket #{%ticket.id%}] Customer posted a new reply', 'aiosc'),
            'email_templates_staff_closure_subject'=>__('[Ticket #{%ticket.id%}] Customer requested closure', 'aiosc'),
            'email_templates_staff_reopen_subject'=>__('[Ticket #{%ticket.id%}] Customer has reopened this ticket', 'aiosc'),

            'email_templates_staff_creation_content'=>aiosc_get_data_file('email-templates/staff/creation.html'),
            'email_templates_staff_reply_content'=>aiosc_get_data_file('email-templates/staff/reply.html'),
            'email_templates_staff_closure_content'=>aiosc_get_data_file('email-templates/staff/closure.html'),
            'email_templates_staff_reopen_content'=>aiosc_get_data_file('email-templates/staff/reopen.html'),

            'email_templates_cron_reminder_inactivity_subject'=>__('[Ticket #{%ticket.id%}] We haven\'t heard back from you...', 'aiosc'),
            'email_templates_cron_reminder_queue_subject'=>__('[Ticket #{%ticket.id%}] We haven\'t forgotten about you!', 'aiosc'),

            'email_templates_cron_reminder_inactivity_content'=>aiosc_get_data_file('email-templates/cron/inactivity.html'),
            'email_templates_cron_reminder_queue_content'=>aiosc_get_data_file('email-templates/cron/queue.html'),

        );
        $def = apply_filters('aiosc_defaults',$settings);
        if(empty($def)) $def = $settings;
        return $def;
    }
    /**
     * @called-on-activation
     */
    function install() {
        if(get_option($this->meta_key,false) == false) update_option($this->meta_key,$this->defaults);
        $this->load_settings();
        aiosc_log('[AIOSC Installer] [Settings] Installed successfully.');
    }

    /**
     * @called-on-uninstall
     */
    function uninstall() {
        delete_option($this->meta_key);
    }

}

global $aiosc_settings;
$aiosc_settings = new aiosc_Settings();

function aiosc_get_settings($key, $default='') {
    global $aiosc_settings;
    return $aiosc_settings->get($key, $default);
}
function aiosc_save_settings() {
    global $aiosc_settings;
    $aiosc_settings->save_settings();
}
function aiosc_set_settings($key, $value, $save=true) {
    global $aiosc_settings;
    $aiosc_settings->set($key, $value, $save);
}