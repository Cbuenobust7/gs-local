<?php
$addons = aiosc_AddonManager::get_addons_from_server();
if($addons !== false) :
?>
<h4><?php _e('List of Add-Ons available for purchase:','aiosc')?></h4>
<ul class="aiosc-addon-list">
    <?php foreach($addons as $addon) : ?>
    <li>

        <?php if(@$addon->thumbnail != '') : ?>
            <img class="aiosc-addon-icon" src="<?php echo $addon->thumbnail ?>" alt="" />
        <?php endif; ?>
        <div class="aiosc-addon-info">
            <?php if(@$addon->package_name != '' && file_exists(WP_PLUGIN_DIR."/".@$addon->package_name)) : ?>
                <small class="aiosc-addon-already-installed"><?php _e('Already installed','aiosc')?></small>
            <?php endif; ?>
            <?php if(version_compare(aiosc_get_version(), @$addon->aiosc_version, '<')) : ?>
                <small class="aiosc-addon-update-required" title="<?php _e('In order to use this Add-On, you must update your AIO Support Center first.','aiosc')?>"><?php _e('AIO Support Center update required','aiosc'); ?></small>
            <?php endif; ?>
            <h4><?php echo @$addon->name?>
                <small><?php printf(__('Version %s','aiosc'),@$addon->version)?>
                    |
                    <?php printf(__('AIO Support Center %s','aiosc'),@$addon->aiosc_version)?>
                </small>
            </h4>
            <p>
                <?php echo @$addon->description ?>
            </p>
            <p>
                <a href="<?php echo @$addon->info_url?>" target="_blank"><?php _e('More Info','aiosc')?></a> |
                <a href="<?php echo @$addon->purchase_url?>" target="_blank"><?php
                    if(@$addon->regular_price > 0)
                        printf(__('Purchase now for $%s','aiosc'),@$addon->regular_price);
                    else _e('Download for free','aiosc');
                    ?></a>
            </p>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php else : ?>
<h4><?php _e('There are no available add-ons at the moment or server may be down. Please return back later.','aiosc')?></h4>
<?php endif; ?>