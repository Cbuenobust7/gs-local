<?php aisoc_print_js_debug() ?>
<?php
/**
 * Available Variables
 * $aiosc_user aiosc_User - current aiosc user
 * $aiosc_settings - can be used for retrieving AIOSC settings (->get or aiosc_settings_get())
 * $ticket_count stdClass - contains ticket count for all/queue/open/closed ($ticket_count->queue for example)
 * $departments array - contains array of aiosc_Department instances that current user has access to
 * $priorities array - contains array of aiosc_Priority instances that current user has access to
 * $publicOnly bool - is publicOnly enabled (true) or (false)
 */
?>
<div class="aiosc-ticket-list">
    <form method="post" id="aiosc-form" action="<?php echo get_admin_url()?>admin-ajax.php">
        <input type="hidden" name="action" value="aiosc_tickets_list" />
        <input type="hidden" name="status" id="list-screen" value="<?php echo in_array(aiosc_pg('status'),aiosc_TicketManager::get_statuses())?aiosc_pg('status'):'all'?>" />
        <input type="hidden" name="order" id="list-order" value="desc" />
        <input type="hidden" name="sort" id="list-sort" value="ID" />
        <input type="hidden" name="frontend" value="1" /> <!-- Important field, don't remove it! -->
        <?php if($publicOnly) : ?>
            <input type="hidden" name="is_public" value="Y" />
        <?php endif; ?>
        <div class="aiosc-toolbar">
            <ul class="aiosc-tabs">
                <li data-screen="all"><?php _e('All','aiosc')?> (<span class="ticket-count-all"><?php echo $ticket_count->all?></span>)</li>
                <li data-screen="queue"><?php _e('In Queue','aiosc')?> (<span class="ticket-count-queue"><?php echo $ticket_count->queue?></span>)</li>
                <li data-screen="open"><?php _e('Open','aiosc')?> (<span class="ticket-count-open"><?php echo $ticket_count->open?></span>)</li>
                <li data-screen="closed"><?php _e('Closed','aiosc')?> (<span class="ticket-count-closed"><?php echo $ticket_count->closed?></span>)</li>
            </ul>
            <!-- Search -->
            <div class="aiosc-search-box">
                <input type="text" name="search" id="ticket-search" placeholder="<?php _e('Search by #ID or term','aiosc')?>" value="<?php echo @$_POST['search']?>" />
                <button type="submit" name="search-submit" id="search-submit" class="button" value="1" title="<?php _e('Search','aiosc')?>"><i class="dashicons dashicons-search"></i></button>
            </div>
        </div>
        <!-- Filters -->
        <div class="aiosc-filters">
            <div class="aiosc-filters-container">
                <?php if(!$publicOnly) : ?>
                <div class="aiosc-filter-cholder">
                    <select name="is_public" id="filter-is_public">
                        <?php if($aiosc_user->can('staff')) : ?>
                            <option value=""><?php _e('- Visibility -','aiosc')?></option>
                            <option value="N"><?php _e('Private','aiosc')?></option>
                            <option value="Y"><?php _e('Public','aiosc')?></option>
                        <?php else : ?>
                            <option value=""><?php _e('My Tickets','aiosc')?></option>
                            <option value="Y"><?php _e('Public Tickets','aiosc')?></option>
                        <?php endif; ?>
                    </select>
                    </div>
                <?php endif; ?>
                <?php if(is_array($priorities) && count($priorities) > 0) : ?>
                    <div class="aiosc-filter-cholder">
                        <select name="priority" id="filter-priorities">
                            <option value="0"><?php _e('- Priority -','aiosc')?></option>
                            <?php foreach($priorities as $pri) : ?>
                                <option value="<?php echo $pri->ID?>" <?php echo aiosc_pg('priority') == $pri->ID?"selected":""?>><?php echo $pri->name?></option>
                            <?php endforeach; ?>
                        </select>
                        </div>
                <?php endif; ?>
                <?php if(is_array($departments) && count($departments) > 0) : ?>
                    <div class="aiosc-filter-cholder">
                        <select name="department" id="filter-departments">
                            <option value="0"><?php _e('- Department -','aiosc')?></option>
                            <?php foreach($departments as $dep) : ?>
                                <option value="<?php echo $dep->ID?>" <?php echo aiosc_pg('department') == $dep->ID?"selected":""?>><?php echo $dep->name?></option>
                            <?php endforeach; ?>
                        </select>
                        </div>
                <?php endif; ?>
                <?php if(!$publicOnly && $aiosc_user->can('staff')) : ?>
                    <div class="aiosc-filter-cholder" style="width: 200px;">
                        <select name="author" id="filter-authors" data-placeholder="<?php _e('- Author -','aiosc')?>">
                            <option value=""><?php _e('- Author -','aiosc')?></option>
                            <?php if($selected_author) : ?>
                                <option value="<?php echo $selected_author->ID?>" selected><?php echo $selected_author->display_name?> (<?php echo $selected_author->user_login?>)</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="aiosc-filter-cholder" style="width: 200px;">
                        <select name="operator" id="filter-operators" data-placeholder="<?php _e('- Operator -','aiosc')?>" style="width: 150px;" <?php if(aiosc_pg('me_operator', true, false)) : ?>disabled="disabled"<?php endif; ?>>
                            <option value=""><?php _e('- Operator -','aiosc')?></option>
                            <?php if($selected_operator) : ?>
                                <option value="<?php echo $selected_operator->ID?>" selected><?php echo $selected_operator->display_name?> (<?php echo $selected_operator->user_login?>)</option>
                            <?php endif; ?>
                            <option value="<?php echo $aiosc_user->ID?>" <?php echo aiosc_pg('operator') == $aiosc_user->ID?"selected":""?>>
                                <?php _e('Me', 'aiosc')?></option>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="aiosc-filter-cholder aiosc-filter-checks">
                    <label for="filter-awaiting_staff_reply" title="<?php _e('Only show tickets which require staff reply.', 'aiosc')?>"><input type="checkbox" id="filter-awaiting_staff_reply" name="awaiting_staff_reply" <?php checked(aiosc_pg('awaiting_staff_reply', true, false)) ?>/> <strong><?php _e('Awaiting reply', 'aiosc')?></strong></label>
                    &nbsp; &nbsp;
                    <label for="filter-requested_closure" title="<?php _e('Only show tickets with requested closure.', 'aiosc')?>"><input type="checkbox" id="filter-requested_closure" name="requested_closure" <?php checked(aiosc_pg('requested_closure', true, false)) ?>/> <strong><?php _e('Requested closure', 'aiosc')?></strong></label>
                </div>
                <?php do_action('aiosc_ticket_filters') ?>
                <div class="aiosc-filter-buttons">
                    <button type="submit" value="1" name="view-change" id="view-change" class="button button-primary"><?php _e('Apply Filters', 'aiosc')?></button>
                    <button type="button" id="reset-filters" class="button"><?php _e('Reset', 'aiosc')?></button>
                </div>
            </div>
        </div>

        <div class="aiosc-clear"></div>
        <div id="ajax-response"></div>
        <div class="aiosc-form">
            <div class="aiosc-tab-content-holder">
                <div class="aiosc-loading-holder"><div class="aiosc-loading-bar"><span><?php _e('Loading Screen...','aiosc')?></span></div></div>
                <div class="tablenav">
                    <!-- PAGINATION GOES HERE -->
                </div>
                <div class="aiosc-clear"></div>
                <div class="aiosc-tab-content">
                    <!-- Here we use one-time params for LIST query, they will be replaced the first time LIST is loaded -->
                    <?php if(isset($_GET['paged'])) : ?>
                        <input type="hidden" name="paged" value="<?php echo aiosc_pg('paged')?>" />
                    <?php endif; ?>
                </div>
            </div>
    </form>
</div>
</div>
<script>
    jQuery(document).ready(function($) {
        var filter_authors = $('#filter-authors');
        var filter_operators = $('#filter-operators');
        var select2_options = {
            language: {
                noResults: function() {
                    return "<?php _e('No users found', 'aiosc')?>"
                },
                inputTooShort: function(args) {
                    var remainingChars = args.minimum - args.input.length;
                    return "<?php _e('Enter %d more characters.', 'aiosc')?>".replace('%d', remainingChars);
                }
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 3,
            templateResult: function (repo) {
                if(repo.loading) return '<?php _e('Searching...', 'aiosc')?>';
                return '<div class="clearfix">' + repo.name + ' (<em>'+repo.login+'</em>)</div>';
            },
            templateSelection: function (repo) {
                if(typeof repo.name == "undefined")
                    return repo.text;

                return repo.name + " (" + repo.login + ")";
            }
        };
        filter_authors.select2($.extend({
            placeholder: filter_authors.attr('data-placeholder'),
            ajax: {
                url: '<?php echo admin_url('admin-ajax.php')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        action: 'aiosc_get_user_list'
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        }, select2_options));
        filter_operators.select2($.extend({
            placeholder: filter_operators.attr('data-placeholder'),
            ajax: {
                url: '<?php echo admin_url('admin-ajax.php')?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        action: 'aiosc_get_user_list',
                        staff: true
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        }, select2_options));
    })
</script>