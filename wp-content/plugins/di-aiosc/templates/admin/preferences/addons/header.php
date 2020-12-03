<?php
global $aiosc_settings, $aiosc_capabilities, $aiosc_user;
$addon_pages = aiosc_AddonManager::get_addon_pages();
?>
<div class="aiosc-subtoolbar">
    <ul class="aiosc-subtabs">
        <?php
        $y = 0;
        foreach($addon_pages as $page_slug=>$data) :
            if(!aiosc_str_startsWith('aiosc-addonscreen-',aiosc_pg('screen')) && $y == 0) $active = 'class="active"';
            else $active = @$_POST['screen'] == 'aiosc-addonscreen-'.$page_slug?'class="active"':'';
            ?>
            <li <?php echo $active ?> data-screen="aiosc-addonscreen-<?php echo $page_slug?>"><?php echo $data['title']?></li>
            <?php $y++;
        endforeach; ?>
    </ul>
</div>