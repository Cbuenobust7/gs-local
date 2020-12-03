<?php
wp_enqueue_media();
?>
<?php aisoc_print_js_debug() ?>
<div class="wrap">
    <div class="aiosc-window">
        <h2 class="page-title"><?php _e('Finalize Activation','aiosc') ?>
            <span><?php printf(__('You either have more than %d users already registered or you had < 2.1.2 version installed.','aiosc'), AIOSC_ACTIVATION_MAX_USERS)?></span></h2>
        <div class="aiosc-toolbar">
            <ul class="aiosc-tabs">
                <li><?php _e('Activator','aiosc')?></li>
            </ul>
        </div>
        <div class="aiosc-clear"></div>
        <div id="ajax-response"></div>
        <div class="aiosc-form">
            <div class="aiosc-tab-content-holder">
                <div class="aiosc-tab-content">
                    <p>
                        <?php printf(__('You either have more than %d users already registered or you had < 2.1.2 version installed.','aiosc'), AIOSC_ACTIVATION_MAX_USERS)?>
                        <br />
                        <br />
                        <?php _e("Once activation starts, <strong>please don't refresh the page</strong> until it's completed. It shouldn't take long so please be patient.",'aiosc')?>
                    </p>
                    <br />
                    <div id="aiosc-installer-window">
                        <div><span id="aiosc-progress"><?php _e('Press "Run Activator" to start activation progress.','aiosc')?></span>
                            <span id="aiosc-elapsed"></span>
                            <span id="aiosc-eta"></span>
                        </div>
                        <div class="diwave-pbar"></div>
                    </div>
                    <div class="aiosc-installer-controls">
                        <button type="button" id="aiosc-installer-start" class="button button-primary"><?php _e('Run Activator','aiosc')?></button>
                        <button type="button" id="aiosc-installer-pause" disabled class="button"><?php _e('Pause','aiosc')?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

</script>