<?php
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();
else {
    //settings
    delete_option("aiosc_settings");

    //roles & capabilities
    $roles = array("aiosc_admin","aiosc_editor",'aiosc_support_op','aiosc_customer','aiosc_exclusive_customer');
    $users = get_users();
    foreach($users as $user) {
        foreach($roles as $k) {
            if(in_array($k,$user->roles))
                $user->remove_role($k);
        }
    }
    foreach($roles as $k) {
        remove_role($k);
    }

    //user settings
    $users = get_users();
    foreach($users as $user) {
        delete_user_meta($user->ID,"aiosc_notifications");
    }
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS `".$wpdb->prefix."aiosc_tickets`");
    $wpdb->query("DROP TABLE IF EXISTS `".$wpdb->prefix."aiosc_replies`");
    $wpdb->query("DROP TABLE IF EXISTS `".$wpdb->prefix."aiosc_departments`");
    $wpdb->query("DROP TABLE IF EXISTS `".$wpdb->prefix."aiosc_priorities`");
    $wpdb->query("DROP TABLE IF EXISTS `".$wpdb->prefix."aiosc_uploads`");
    $wpdb->query("DROP TABLE IF EXISTS `".$wpdb->prefix."aiosc_premades`");
    $wpdb->query("DROP TABLE IF EXISTS `".$wpdb->prefix."aiosc_cron`");

    delete_option('aiosc_installed');
}