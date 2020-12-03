<?php

class esigRecaptchaSettings {

    const ESIG_GOOGLE_CAPTCHA_SITE_KEY = 'esig_google_captcha_site_key';
    const ESIG_GOOGLE_CAPTCHA_SECRET_KEY = 'esig_google_captcha_secret_key';
    const ESIG_GLOBAL_RECAPTCHA_SETTING = 'esig_global_recaptcha';
    const ESIG_DOC_RECAPTCHA_SETTING = 'esig_document_recaptcha';
    const ESIG_HIDE_RECAPTCHA_BADGE = 'esig_hide_recaptcha_badge';

    public static function saveKeys($siteKey, $secretKey) {
        WP_E_Sig()->setting->set_generic(self::ESIG_GOOGLE_CAPTCHA_SITE_KEY, $siteKey);
        WP_E_Sig()->setting->set_generic(self::ESIG_GOOGLE_CAPTCHA_SECRET_KEY, $secretKey);
    }

    public static function getSecretKey() {
        return WP_E_Sig()->setting->get_generic(self::ESIG_GOOGLE_CAPTCHA_SECRET_KEY);
    }

    public static function getsiteKey() {
        return WP_E_Sig()->setting->get_generic(self::ESIG_GOOGLE_CAPTCHA_SITE_KEY);
    }

    public static function enableGlobalRecaptcha($enabled) {
        WP_E_Sig()->setting->set_generic(self::ESIG_GLOBAL_RECAPTCHA_SETTING, $enabled);
    }
    
    public static function isKeysEnabled(){
        $secretKey = self::getSecretKey();
        $siteKey = self::getsiteKey();
        if(empty($secretKey) || empty($siteKey)){
            return false;
        }
        return true;
    }
    
    public static function enableDocRecaptcha($document_id) {
       WP_E_Sig()->meta->add($document_id, self::ESIG_DOC_RECAPTCHA_SETTING,1);
    }
    
     public static function disableDocRecaptcha($document_id) {
       WP_E_Sig()->meta->add($document_id, self::ESIG_DOC_RECAPTCHA_SETTING,0);
    }
    
    public static function isDocRecaptchaEnabled($documentId){
        $isRecaptcha = WP_E_Sig()->meta->get($documentId, self::ESIG_DOC_RECAPTCHA_SETTING);
        
        if ($isRecaptcha) {
            return true;
        }
        return false;
    }
    
    public static function hideRecaptchaBadge($disabled) {
        WP_E_Sig()->setting->set_generic(self::ESIG_HIDE_RECAPTCHA_BADGE, $disabled);
    }
    
    public static function isHideBadge(){
        $hideBadge =  WP_E_Sig()->setting->get_generic(self::ESIG_HIDE_RECAPTCHA_BADGE);
        if($hideBadge){
            return true ;
        }
        return false;
    }

    public static function getGlobalCaptchaSetting() {
        return WP_E_Sig()->setting->get_generic(self::ESIG_GLOBAL_RECAPTCHA_SETTING);
    }

    public static function isGlobalRecaptchaEnabled() {
        if (self::getGlobalCaptchaSetting()) {
            return true;
        }
        return false;
    }

    public static function isRecaptchaEnabled($documentId) {
        
        if (self::isGlobalRecaptchaEnabled() ) {
            return true;
        }
        
        $isRecaptcha = WP_E_Sig()->meta->get($documentId, self::ESIG_DOC_RECAPTCHA_SETTING);
        
        if ($isRecaptcha) {
            return true;
        }
 
        return false;
    }

    public static function findDocumentId() {

        $invite_hash = esigget('invite');
        // $check_sum= esigget('csum');
        if (empty($invite_hash)) {

            if (isset($_POST) && count($_POST) > 0) {
                return false;
            }
            
            $page_id = get_queried_object_id();
            $sadClass = new esig_sad_document();
            $documentId = $sadClass->get_sad_id($page_id);
            return $documentId;
        } else {

            $invites = WP_E_Sig()->invite->getInvite_by_invite_hash($invite_hash);
            if (!WP_E_Sig()->signature->userHasSignedDocument($invites->user_id, $invites->document_id)) {
                return $invites->document_id;
            }
        }
        return false;
    }

}
