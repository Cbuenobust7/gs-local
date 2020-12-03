<?php
global $aiosc_addon_pages;
class aiosc_AddonManager {
    static function get_addon_pages() {
        global $aiosc_addon_pages;
        $addons = apply_filters('aiosc_addon_page',$aiosc_addon_pages);
        if(is_array($addons)) {
            uasort($addons, "aiosc_AddonManager::cmp_by_order");
            return $addons;
        }
        return false;
    }
    private static function cmp_by_order($a, $b) {
        return @$a["order"] - @$b["order"];
    }

    /**
     * Get list of available addons for purchase from DiWave server
     * If nothing found, returns FALSE, otherwise returns array of addon objects from JSON data
     * @return array|bool
     */
    static function get_addons_from_server() {
        $page = file_get_contents(AIOSC_DIWAVE_ADDONS_URL);
        if(!empty($page)) {
            try {
                $data = json_decode($page);
                return is_array($data)?$data:false;
            }
            catch(Exception $e) {
                return false;
            }
        }
        else return false;
    }
}
function aiosc_addon_page( $menu_title, $menu_slug, $display_callback = '', $save_callback = '', $position = null ) {
    global $aiosc_addon_pages;
    $aiosc_addon_pages[$menu_slug] = array(
        'title'=>$menu_title,
        'display_callback'=>$display_callback,
        'save_callback'=>$save_callback,
        'position'=>$position
    );
}