<?php
class aiosc_more_addons {
    function __construct() {
        //add page to AIOSC and tell this is an AIOSC plugin
        add_filter('aiosc_addon_page',array($this,'settings_page'));
    }
    function settings_page($pages) {
        $pages[] = array(
            'name'=>'more-addons',
            'title'=>__('More Add-Ons','aiosc'),
            'display_callback'=>array($this,'display'),
            'save_callback'=>array($this,'save'),
            'order'=>99999
        );
        return $pages;
    }
    function display() {
        echo aiosc_load_template('admin/preferences/addons/list.php',false);
    }
    function save() {
        //silence is golden
    }
}
new aiosc_more_addons();