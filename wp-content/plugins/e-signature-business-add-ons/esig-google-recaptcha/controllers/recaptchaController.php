<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class WP_E_recaptchasController {
    
    public function __construct() {
        
    }
    
     public function google(){
         
         
         if( count($_POST) > 0 && esigpost('esig-recaptcha-submit')){
             
               esigRecaptchaSettings::saveKeys(esigpost('esig_recaptcha_site_key'), esigpost('esig_recaptcha_secret_key'));
               esigRecaptchaSettings::enableGlobalRecaptcha(esigpost('esig_recaptcha_global'));
               esigRecaptchaSettings::hideRecaptchaBadge(esigpost('esig_recaptcha_badge'));
         }
         
         
         $misc_more_actions = apply_filters('esig_misc_more_document_actions', '');

         $class = (isset($_GET['page']) && $_GET['page'] == 'esign-google-captcha') ? 'misc_current' : '';
         $esigGeneral = new WP_E_General();
         
            $template_data = array(
                "post_action" => 'admin.php?page=esign-google-recaptcha',
                "misc_tab_class" => 'nav-tab-active',
                "customizztion_more_links" => $misc_more_actions,
                "Licenses" => $esigGeneral->checking_extension(),
                "link_active" => $class,
               
            );
            
             $template_filter = apply_filters('esig-misc-form-data', $template_data, array());
             $template_data = array_merge($template_data, $template_filter);
             
         $templatePath  = ESIGN_GOOGLE_CAPTCHA_PATH . "/views/settings.php" ; 
         $esigView = new WP_E_View();
         $esigView->render(false,$templatePath,$template_data);
     }
    
}