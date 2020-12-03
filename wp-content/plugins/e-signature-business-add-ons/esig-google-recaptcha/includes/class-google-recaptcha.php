<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!class_exists('ESIG_GOOGLE_CAPTCHA')) :

    class ESIG_GOOGLE_CAPTCHA {

        /**
         * Initialize the plugin by loading admin scripts & styles and adding a
         * settings page and menu.
         * @since     0.1
         */
        public static function Init() {
            // usr action 
            add_filter('esig_misc_more_document_actions', array(__CLASS__, 'esig_misc_page_more_acitons'), 10, 1);
            add_action('admin_menu', array(__CLASS__, 'adminMenu'));
            add_action('esig_register_scripts', array(__CLASS__, 'register_scripts'));
            add_filter("esig_print_footer_scripts", array(__CLASS__, "load_scripts"), 10, 1);
            add_filter("esig_print_header_styles", array(__CLASS__, "load_styles"), 10, 1);
            add_action("esig_before_agreement_page_loads", array(__CLASS__, "esig_verify_google_recaptcha"));
            add_filter('esig_admin_advanced_document_contents', array(__CLASS__, 'add_captcha_document_contents'), 10, 1);
            
            add_action('esig_document_after_save', array(__CLASS__, 'esig_document_after_save'), 10, 1);
            add_action('esig_addons_logo_esig-google-recaptcha', array(__CLASS__, 'recaptcha_logo'), 10, 1);
        }
        
        static public function recaptcha_logo($logofile){
           return ESIGN_RECAPTCHA_ASSET_URI . '/images/esig-google-recaptcha.png';
        }
        
        static public function esig_document_after_save($args) {
           
            $document_id = $args['document']->document_id;
            
            $enableRecaptcha = esigpost('esig_google_recaptha');
            if($enableRecaptcha){
                esigRecaptchaSettings::enableDocRecaptcha($document_id);
            }
            else {
                esigRecaptchaSettings::disableDocRecaptcha($document_id);
            }
        }

        final static public function add_captcha_document_contents($advanced_more_options) {
            
            if(!esigRecaptchaSettings::isKeysEnabled()){
                return $advanced_more_options;
            }
             //cheacked user document
            $temp_id = esigget('temp_id');

            if ($temp_id) {
                $document_id = $temp_id;
            } else {
                $document_id = esigget('document_id');
            }
            
            $esig_captcha_checked = (esigRecaptchaSettings::isDocRecaptchaEnabled($document_id))? "checked" : false; 
            
            $advanced_more_options .='<p><a href="#" class="tooltip">
                                    <img src="' . ESIGN_ASSETS_DIR_URI . '/images/help.png" height="20px" width="20px" align="left"><span>' . __('Enable google Recaptcha option for this document contracts.', 'esig') . '</span>
                                    </a><input type="checkbox" id="esig_google_recaptha" name="esig_google_recaptha" value="1" ' . $esig_captcha_checked . '>
                                   <label class="leftPadding-5"> ' . __('Enable google Recaptcha for this document', 'esig') . '</label></p>';
            
            return $advanced_more_options; 
        }

        final static function esig_verify_google_recaptcha() {

            if (isset($_POST) && count($_POST) == 0) {
                return false;
            }


            $documentId = esigRecaptchaSettings::findDocumentId();

            if (!esigRecaptchaSettings::isRecaptchaEnabled($documentId)) {
                return false;
            }

            $esigRecaptchaToken = esigpost('esig_recaptcha_validation_token');
            if (empty($esigRecaptchaToken)) {
                wp_die('You are not allowed to sign this agreement.  Bad Request – Captcha validation error.');
            }

            $response = Esign_licenses::wpRemoteRequest(["body" => ["secret" => esigRecaptchaSettings::getSecretKey(),
                            "response" => $esigRecaptchaToken]], "https://www.google.com/recaptcha/api/siteverify");

            $response_data = json_decode(wp_remote_retrieve_body($response));

            $score = $response_data->score;
            $success = $response_data->success;

            if ($success === false) {
                wp_die('You are not allowed to sign this agreement. Captcha validation error.');
            }

            $allowedRecaptchaSore = apply_filters("esig_allowed_recaptcha_score_level", 0.3);

            if ($success === true && $score <= $allowedRecaptchaSore) {
                wp_die(sprintf('You are not allowed to sign this agreement. Captcha validation error score below %0.2f', $allowedRecaptchaSore));
            }

            return true;
        }

        final static function load_scripts($scripts) {

            $documentId = esigRecaptchaSettings::findDocumentId();

            if (!$documentId) {
                return $scripts;
            }

            if (!esigRecaptchaSettings::isRecaptchaEnabled($documentId)) {
                return $scripts;
            }


            $scripts[] = 'esig-google-recaptcha-api';
            $scripts[] = 'esig-recaptcha-execute';

            wp_localize_script('esig-recaptcha-execute', 'esig_recaptcha', [esigRecaptchaSettings::ESIG_GOOGLE_CAPTCHA_SITE_KEY => esigRecaptchaSettings::getsiteKey()]);
            return $scripts;
        }

        final static function load_styles($styles) {

            $documentId = esigRecaptchaSettings::findDocumentId();

            if (!$documentId) {
                return $styles;
            }

            if (!esigRecaptchaSettings::isRecaptchaEnabled($documentId)) {
                return $styles;
            }

            if (esigRecaptchaSettings::isHideBadge()) {
                $styles[] = 'esig-recaptch-style-hide';
            } else {
                $styles[] = 'esig-recaptch-style-show';
            }

            return $styles;
        }

        final static function register_scripts() {
            //&render=". esigRecaptchaSettings::getsiteKey()
            wp_register_script('esig-google-recaptcha-api', "https://www.google.com/recaptcha/api.js?onload=_grecaptcha_callback&render=explicit", array(), "", false);
            wp_register_script('esig-recaptcha-execute', plugins_url('assets/js/esig-recaptcha.js', dirname(__FILE__)), array(), "", false);
            //registering styles 
            wp_register_style('esig-recaptch-style-show', plugins_url('assets/css/esig-recaptcha-show.css', dirname(__FILE__)), array(), "", 'screen');
            wp_register_style('esig-recaptch-style-hide', plugins_url('assets/css/esig-recaptcha-hide.css', dirname(__FILE__)), array(), "", 'screen');
        }

        final static function esig_misc_page_more_acitons($misc_more_actions) {

            $class = (isset($_GET['page']) && $_GET['page'] == 'esign-google-recaptcha') ? 'misc_current' : '';
            $misc_more_actions .= ' | <a class="misc_link ' . $class . '" href="admin.php?page=esign-google-recaptcha">' . __('Google reCAPTCHA', 'esig') . '</a>';
            return $misc_more_actions;
        }

        final static function adminMenu() {
            $esigClass = new Esign_core_load();
            add_submenu_page(null, __('E-mails', 'esig'), __('E-mails', 'esig'), 'read', 'esign-google-recaptcha', array(&$esigClass, 'route'));
        }

    }

    

    
    
endif;