<?php
/*
 * Plugin Name: AIO Support Center
 * Plugin Class Name: aiosc
 * Description: All-in-One solution for providing quality support to your clients.
 * Author: DiWave Coders
 * Version: 2.21
 * Plugin URI: http://diwave-coders.com/plugins/aio-support-center-wordpress-ticketing-system/
 * Support URI: http://diwave-coders.com/support?department=2
 * Author URI: http://diwave-coders.com
 * Text Domain: aiosc
*/

//Defines
define('AIOSC_ERROR_LOGGING', false);
define('AIOSC_DEBUG',false); //for javascript debugging
define('AIOSC_DEMO_MODE',false); //for preview
define('AIOSC_EMAIL_DEBUG_ONLY', false);
define('AIOSC_DIR',plugin_dir_path(__FILE__));
define('AIOSC_URL',plugin_dir_url(__FILE__));
define('AIOSC_PERMISSION_ERROR',__("<strong>Permission Error:</strong> You do not have permission for this action.",'aiosc'));
define('AIOSC_CRON_TIME_LIMIT', 600); //600 seconds (10 minutes)
define('AIOSC_CRON_SK', '19117L9o70uH7r_0L'); //in case you change this, you will have to change it in your CRON command as well

$aiosc_wp_upload_dir = wp_get_upload_dir();
define('AIOSC_UPLOAD_DIR',apply_filters('aiosc_upload_dir', $aiosc_wp_upload_dir['basedir'] . '/aiosc'));
define('AIOSC_UPLOAD_URL',apply_filters('aiosc_upload_url', $aiosc_wp_upload_dir['baseurl'] . '/aiosc'));

//Maximum number of users for automatic plugin activation.
//If website has more users, activation will have to be done manually.
define('AIOSC_ACTIVATION_MAX_USERS', 300);

/** Textdomain Registration */
function aiosc_load_plugin_textdomain() {
    $locale = apply_filters( 'plugin_locale', get_locale(), 'aiosc' );
    load_plugin_textdomain( 'aiosc', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'aiosc_load_plugin_textdomain' );

//Init Variables
require_once AIOSC_DIR."/inc/vars.php";

//Init Tools
require_once AIOSC_DIR."/inc/tools.php";

//Classes
require_once AIOSC_DIR.'/class/class.capabilities.php';
require_once AIOSC_DIR.'/class/class.settings.php';

require_once AIOSC_DIR.'/class/class.user.php';
require_once AIOSC_DIR.'/class/class.user-manager.php';

require_once AIOSC_DIR.'/class/class.department.php';
require_once AIOSC_DIR.'/class/class.department-manager.php';

require_once AIOSC_DIR.'/class/class.priority.php';
require_once AIOSC_DIR.'/class/class.priority-manager.php';

require_once AIOSC_DIR.'/class/class.attachment.php';
require_once AIOSC_DIR.'/class/class.attachment-manager.php';

require_once AIOSC_DIR.'/class/class.premade-response.php';
require_once AIOSC_DIR.'/class/class.premade-response-manager.php';

require_once AIOSC_DIR.'/class/class.ticket.php';
require_once AIOSC_DIR.'/class/class.ticket-manager.php';

require_once AIOSC_DIR.'/class/class.reply.php';
require_once AIOSC_DIR.'/class/class.reply-manager.php';

require_once AIOSC_DIR.'/class/class.shortcodes.php';

//require_once AIOSC_DIR.'/class/class.email.php';
require_once AIOSC_DIR.'/class/class.email-manager.php';

require_once AIOSC_DIR.'/class/class.addon-manager.php';


require_once AIOSC_DIR.'/class/class.cron.php';

//Init Hooks & Pages
require_once AIOSC_DIR.'/inc/hooks.php';
require_once AIOSC_DIR.'/inc/hooks_tools.php';
require_once AIOSC_DIR.'/inc/pages.php';

//more-addons tab
require_once AIOSC_DIR.'/inc/more-addons.php';


/**
 * Main Plugin Class
 * Class aiosc
 */
/**
 * Installation hook, had to move it outside of class
 * because class is called on WP init, however registration
 * hooks are executed before WP init.
 * @since 1.0.81
 */
register_activation_hook(__FILE__,'aiosc_install');
function aiosc_install() {
    global $aiosc_capabilities, $aiosc_settings;
    $installed = $aiosc_capabilities->install();
    $aiosc_settings->install();

    //Install DB tables
    $file = aiosc_get_data_file('sql/install.php');
    $queries = explode(';',$file);

    global $wpdb;
    $y = $cnt = 0;
    $wpdb->hide_errors();
    foreach($queries as $query) {
        if(strlen(trim($query)) > 2) {
            $query = str_replace("{%wp_prefix%}",$wpdb->prefix,$query);
            try {
                if($wpdb->query($query))
                    $y++;
            }
            catch(Exception $e) {}
            $cnt++;
        };
    }
    /**
     * Update AWAITING_REPLY ticket row
     * @since 2.0
     */
    //All tickets in queue are awaiting reply
    $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::tickets)."` SET awaiting_reply = 'Y' WHERE status = 'queue'");

    //Update all tickets where last reply was from customer
    $tickets = array();
    $tmp = $wpdb->get_results("SELECT ticket_id, date_created, is_staff_reply FROM
    (SELECT * FROM `".aiosc_get_table(aiosc_tables::replies)."` ORDER BY date_created DESC) as replies_table
    GROUP BY ticket_id
    ORDER BY date_created DESC");
    if($tmp) {
        foreach($tmp as $t) {
            if($t->is_staff_reply == 'N' && !in_array($t->ticket_id, $tickets)) {
                $tickets[] = $t->ticket_id;
            }
        }
    }
    if(!empty($tickets)) {
        $wpdb->query("UPDATE `".aiosc_get_table(aiosc_tables::tickets)."` SET awaiting_reply = 'Y' WHERE ID IN (".implode(",", $tickets).")");
    }

    $wpdb->show_errors();
    aiosc_log("[AIOSC Installer] Total of $y queries were executed, out of $cnt.");
    //aiosc_UserManager::install_meta();
    aiosc_DepartmentManager::install();
    aiosc_PriorityManager::install();
    update_option('aiosc_installed', $installed ? 'Y' : 'N');
}
class aiosc {
    function __construct() {
        new aiosc_Hooks();
        new aiosc_HooksTools();
        new aiosc_Pages();

        do_action('aiosc_init');
    }
}

/**
 * Initialize AIOSC on WP Init to make sure every component required by AIOSC is loaded
 * before AIOSC initializes.
 * @since 1.0.8
 */
function aiosc_initialize() {
    new aiosc();
    //echo "<pre>".wpautop(aiosc_get_settings('email_templates_customer_creation_content')); die();
}
add_action('init','aiosc_initialize');


