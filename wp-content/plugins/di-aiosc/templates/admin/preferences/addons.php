<?php
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;
$addon_pages = aiosc_AddonManager::get_addon_pages();
if(!empty($addon_pages)) :
?>
    <?php
    //have addons? call first addon screen
    reset($addon_pages);
    $addon = key($addon_pages);
    $callback = $addon_pages[$addon]['display_callback'];

    echo aiosc_load_template('admin/preferences/addons/header.php');
    echo call_user_func($callback);
    ?>
<?php else :

    echo aiosc_load_template('admin/preferences/addons/none.php');

endif; ?>