<?php
error_reporting(0);
require_once '../../../wp-blog-header.php';

if(!isset($_GET['sk']) || $_GET['sk'] != AIOSC_CRON_SK)
    die();

set_time_limit(AIOSC_CRON_TIME_LIMIT);

global $aiosc_settings, $aiosc_capabilities, $aiosc_user, $wpdb;

//Check if AIOSC cron is enabled first
if(!$aiosc_settings->get('cron_enable')) {
    die();
}

//Auto-Closing tickets
$admin = get_user_by('email', get_option('admin_email'));
//$aiosc_user = new aiosc_User($admin->ID);
if($admin != false) {
    $aiosc_user = new aiosc_User($admin->ID);
}

aiosc_Cron::run();

die();
