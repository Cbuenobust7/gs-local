<div id="aiosc-global-warning">
    <p>
        <?php printf(
            __('<strong>AIO Support Center</strong> is not fully installed. Click <a href="%s"><strong>here</strong></a> to finish installation & activation.', 'aiosc'),
            admin_url('admin.php?page=aiosc-list')
            )?>
    </p>
</div>
<script>
    jQuery(document).ready(function($) {
        $('#aiosc-global-warning').prependTo($('#wpbody-content > .wrap:eq(0)'));
    })
</script>