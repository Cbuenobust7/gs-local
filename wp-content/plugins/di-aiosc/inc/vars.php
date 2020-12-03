<?php
/** URL for getting purchasable plugins */
define('AIOSC_DIWAVE_ADDONS_URL','http://diwave-coders.com/addon-list.php');
/**
 * Class aiosc_tables
 * Tables that AIO Support Center uses
 *
 * @update 2.0
 */
abstract class aiosc_tables {
    const tickets = 'aiosc_tickets';
    const replies = 'aiosc_replies';
    const departments = 'aiosc_departments';
    const priorities = 'aiosc_priorities';
    const uploads = 'aiosc_uploads';
    const premade_responses = 'aiosc_premades';

    /** @since 2.0 */
    const cron = 'aiosc_cron';
}

/**
 * These are content tags that will be replaced with correct values.
 * Usually used in e-mail templates (auto-responders)
 * Tag must be used like {%TAG_NAME%}
 *
 * @filter aiosc_content_tags
 * @return mixed|void
 */
function aiosc_get_content_tags() {
    $tags = array(
        'ticket'=>array(
            'label'=>__('Ticket','aiosc'),
            'fields'=>array(
                'ticket.url'=>__('URL','aiosc'),
                'ticket.front_url'=>__('Front-End URL','aiosc'),
                'ticket.dynamic_url'=>__('Dynamic URL','aiosc'),
                'ticket.id'=>__('ID','aiosc'),
                'ticket.priority.name'=>__('Priority Name','aiosc'),
                'ticket.priority.id'=>__('Priority ID','aiosc'),
                'ticket.priority.level'=>__('Priority Level','aiosc'),
                'ticket.priority.color'=>__('Priority Color','aiosc'),
                'ticket.department.name'=>__('Department Name','aiosc'),
                'ticket.department.id'=>__('Department ID','aiosc'),
                'ticket.subject'=>__('Subject','aiosc'),
                'ticket.content'=>__('Content','aiosc'),
                'ticket.status'=>__('Status','aiosc'),
                'ticket.date.created'=>__('Date Created','aiosc'),
                'ticket.date.open'=>__('Date Open','aiosc'),
                'ticket.date.closed'=>__('Date Closed','aiosc'),
                'ticket.closure_note'=>__('Closure Note','aiosc'),
                'ticket.scheduled_closure.days'=>__('Scheduled closure (days)', 'aiosc'),
                'ticket.scheduled_closure.date'=>__('Scheduled closure (date)', 'aiosc'),
                'ticket.scheduled_closure.datetime'=>__('Scheduled closure (date & time)', 'aiosc'),
                'ticket.attachments.count'=>__('Attachments Count','aiosc'),
                'ticket.attachments.size'=>__('Attachments Size (in Kb)','aiosc')
            )
        ),
        'reply'=>array(
            'label'=>__('Reply','aiosc'),
            'fields'=>array(
                'reply.id'=>__('ID','aiosc'),
                'reply.content'=>__('Content','aiosc'),
                'reply.date.created'=>__('Date Posted','aiosc'),
                'reply.attachments.count'=>__('Attachments Count','aiosc'),
                'reply.attachments.size'=>__('Attachments Size (in Kb)','aiosc')
            )
        ),
        'customer'=>array(
            'label'=>__('Customer','aiosc'),
            'fields'=>array(
                'customer.id'=>__('ID','aiosc'),
                'customer.first_name'=>__('First Name','aiosc'),
                'customer.last_name'=>__('Last Name','aiosc'),
                'customer.display_name'=>__('Display Name','aiosc'),
                'customer.email'=>__('E-Mail Address','aiosc'),
                'customer.role'=>__('AIOSC Role','aiosc'),
                'customer.url'=>__('Profile URL','aiosc')
            )
        ),
        'operator'=>array(
            'label'=>__('Operator','aiosc'),
            'fields'=>array(
                'operator.id'=>__('ID','aiosc'),
                'operator.first_name'=>__('First Name','aiosc'),
                'operator.last_name'=>__('Last Name','aiosc'),
                'operator.display_name'=>__('Display Name','aiosc'),
                'operator.email'=>__('E-Mail Address','aiosc'),
                'operator.role'=>__('AIOSC Role','aiosc'),
                'operator.url'=>__('Profile URL','aiosc')
            )
        ),
        'misc'=>array(
            'label'=>__('Miscellaneous','aiosc'),
            'fields'=>array(
                'site.name'=>__('Site Name','aiosc'),
                'site.url'=>__('Site URL','aiosc'),
                'login.url'=>__('Login URL','aiosc'),
                'aiosc.my_tickets'=>__('My Tickets URL','aiosc'),
                'aiosc.new_ticket'=>__('New Ticket URL','aiosc'),
                'aiosc.my_tickets_front'=>__('My Tickets URL - Front-End','aiosc'),
                'aiosc.new_ticket_front'=>__('New Ticket URL - Front-End','aiosc'),
                'date.now'=>__('Date - Now','aiosc'),
                'date.year'=>__('Date - Year','aiosc'),
                'date.month'=>__('Date - Month','aiosc'),
                'time.now'=>__('Time - Now','aiosc')
            )
        )
    );
    return apply_filters('aiosc_content_tags',$tags);
}