<?php
/**
 * Last update: 1.0.6
 */
class aiosc_Shortcodes {
    /**
     * Create New Ticket form shortcode
     * @update 1.0.6
     * @update 1.1 - added @aiosc_enqueue_front hook
     * @param $atts
     * @return string
     */
    static function ticket_form($atts) {
        global $aiosc_user, $aiosc_settings;
        wp_enqueue_style('aiosc-frontend',AIOSC_URL.'assets/css/front.css');
        if($aiosc_user->can('create_ticket')) {
            global $priorities, $departments;
            $departments = aiosc_DepartmentManager::get_departments(true);
            $priorities = aiosc_PriorityManager::get_priorities(true);

            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-form');
            wp_enqueue_script('jquery-browser',AIOSC_URL.'assets/js/jquery.browser.min.js', array('jquery'), '', true);
            wp_enqueue_script('aiosc-common',AIOSC_URL.'assets/js/common.js', array('jquery'), '', true);
            wp_enqueue_script('aiosc-uploader',AIOSC_URL.'assets/js/uploader.js', array('jquery'), '', true);
            wp_enqueue_script('aiosc-new-ticket',AIOSC_URL.'assets/js/new-ticket.js', array('jquery'), '', true);

            add_action('wp_footer', array('aiosc_Shortcodes', 'ticket_form_javascript'));

            do_action('aiosc_enqueue_front', 'ticket_form');

            return aiosc_load_template('shortcodes/new-form/form.php',true);
        }
        else {
            return aiosc_load_template('shortcodes/new-form/error.php', true);
        }
    }
    static function ticket_form_javascript() { ?>
<script>
    jQuery(document).ready(function($) {
        aioscupload = new aioscUploader({
            ul: 'ul.aiosc-uploader-files',
            inputName: 'attachments[]',
            maxFiles: <?php echo aiosc_get_settings('max_files_per_ticket') ?>
        });
    });
</script>
<?php
    }

    /**
     * @shortcode [aiosc_ticket_list publicOnly=false]
     * if @publicOnly is true, table will display only tickets with Public visibility and will
     * ignore user's tickets, which means this should be used on a "FAQ" page for example.
     * Otherwise, table will display user's tickets (where user is author)
     * @update 1.0.6
     * @update 1.1 - added @aiosc_enqueue_front hook
     * @param $atts
     * @return string
     */
    static function ticket_list($atts) {
        extract( shortcode_atts( array(
            "publiconly"=>false
        ), $atts ) );
        global $aiosc_user, $aiosc_settings;
        wp_enqueue_style('aiosc-frontend',AIOSC_URL.'assets/css/front.css');
        if($aiosc_user->can('create_ticket')) {
            $file = aiosc_get_template_path('shortcodes/list/page.php',true);
            if(file_exists($file)) {
                ob_start();

                wp_enqueue_script('jquery');
                wp_enqueue_script('jquery-form');
                wp_enqueue_script('jquery-browser',AIOSC_URL.'assets/js/jquery.browser.min.js', array('jquery'), '', true);
                wp_enqueue_style('dashicons');
                wp_enqueue_script('aiosc-common',AIOSC_URL.'assets/js/common.js', array('jquery'), '', true);
                wp_enqueue_script('aiosc-list',AIOSC_URL.'assets/js/tickets-list.js', array('jquery'), '', true);

                wp_deregister_script('select2');
                wp_register_script('select2', AIOSC_URL . 'assets/js/select2.min.js', array('jquery'), '4.0.0');
                wp_deregister_style('select2');
                wp_register_style('select2', AIOSC_URL . 'assets/css/select2.css', array(), '4.0.0');

                wp_enqueue_script('select2');
                wp_enqueue_style('select2');

                do_action('aiosc_enqueue_front', 'ticket_list');

                /** Define variables for use in template */

                $publicOnly = ("{$publiconly}" == true)?true:false;
                if($aiosc_user->can('staff') && !$aiosc_user->can('answer_tickets'))
                    $departments = $aiosc_user->get_departments(true);
                else
                    $departments = aiosc_DepartmentManager::get_departments(!$aiosc_user->can('staff'), true);

                $priorities = aiosc_PriorityManager::get_priorities(!$aiosc_user->can('staff'), true);

                $ticket_query = array();

                $additional_query = '';
                if(!$publicOnly && aiosc_pg('is_public') != 'Y') {
                    if(!$aiosc_user->can('staff')) $ticket_query['author_id'] = $aiosc_user->ID;
                }
                else {
                    $ticket_query['is_public'] = 'Y';
                }
                if(!$aiosc_user->can('edit_tickets') && $aiosc_user->can('staff')) {
                    $ddd = $aiosc_user->get_departments(false);
                    if($ddd !== false) {
                        $additional_query .= " (";
                        for($i=0;$i<count($ddd);$i++) {
                            $additional_query .= " department_id = ".$ddd[$i]." ";
                            if($i == count($ddd) - 1) $additional_query .= ")";
                            else $additional_query .= " OR ";
                        }
                    }
                }

                $ticket_count = (object) array(
                    'all'=>aiosc_TicketManager::get_count_by($ticket_query,$additional_query),
                    'queue'=>aiosc_TicketManager::get_count_by(array_merge($ticket_query,array('status'=>'queue')),$additional_query),
                    'open'=>aiosc_TicketManager::get_count_by(array_merge($ticket_query,array('status'=>'open')),$additional_query),
                    'closed'=>aiosc_TicketManager::get_count_by(array_merge($ticket_query,array('status'=>'closed')),$additional_query)
                );
                /** Display shortcode */
                include $file;
                return ob_get_clean();
            }
            else return '';
        }
        else {
            return aiosc_load_template('shortcodes/list/error.php',true);
        }
    }
    /**
     * Singular ticket preview shortcode
     * @update 1.0.6
     * @update 1.1 - added @aiosc_enqueue_front hook
     * @param $atts
     * @return string
     */
    static function ticket_preview($atts) {
        extract( shortcode_atts( array(
            "ticket_id"=>0
        ), $atts ) );
        global $aiosc_user, $aiosc_settings, $ticket;
        $ticket = isset($_GET['ticket_id']) && is_numeric($_GET['ticket_id'])?$_GET['ticket_id']:"{$ticket_id}";
        $ticket = new aiosc_Ticket($ticket);
        wp_enqueue_style('aiosc-frontend',AIOSC_URL.'assets/css/front.css');
        if($aiosc_user->can('view_ticket',array('ticket_id'=>$ticket))) {
            global $replies, $department, $priority, $author, $operator;
            $reply_limit = apply_filters('aiosc_reply_limit',3);
            $replies = aiosc_ReplyManager::get_replies(array('ticket_id'=>$ticket->ID),' ORDER BY ID DESC LIMIT 0,'.$reply_limit);
            $department = new aiosc_Department($ticket->department_id);
            $priority = new aiosc_Priority($ticket->priority_id);
            $author = new aiosc_User($ticket->author_id);
            $operator = new aiosc_User($ticket->op_id);
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-form');
            wp_enqueue_script('jquery-browser',AIOSC_URL.'assets/js/jquery.browser.min.js');
            wp_enqueue_script('aiosc-common',AIOSC_URL.'assets/js/common.js');
            wp_enqueue_script('aiosc-uploader',AIOSC_URL.'assets/js/uploader.js');
            wp_enqueue_script('aiosc-downloader',AIOSC_URL.'assets/js/downloader.js');
            wp_enqueue_script('aiosc-ticket-preview',AIOSC_URL.'assets/js/ticket-preview.js');
            do_action('aiosc_enqueue_front', 'ticket_preview');

            return aiosc_load_template('shortcodes/single/page.php',true);

        }
        else {
            return aiosc_load_template('shortcodes/single/error.php',true);
        }
    }
}