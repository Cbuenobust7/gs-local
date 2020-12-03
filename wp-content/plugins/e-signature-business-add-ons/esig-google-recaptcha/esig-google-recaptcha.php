<?php
/**
 * @package   	      WP E-Signature - Google reCaptcha
 * @contributors      Kevin Michael Gray (Approve Me), Abu Shoaib (Approve Me)
 * @wordpress-plugin
 * Name:       WP E-Signature - Google reCaptcha
 * Plugin URI:        https://www.approveme.com/wp-digital-e-signature
 * Description:       This add-on automatically validate google reCaptcha for e-signature contracts submissions.
 * mini-description:  connect with your Active Campaign CRM
 * Version:           1.5.6.5
 * Author:            Approve Me
 * AuthorURI:        https://approveme.com/
 * Documentation:   http://aprv.me/1NE59p3
 * License/TermsandConditions: https://www.approveme.com/terms-conditions/
 * PrivacyPolicy: https://www.approveme.com/privacy-policy/
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

 // esig google captcah plugin path
            if (!defined('ESIGN_GOOGLE_CAPTCHA_PATH'))
                   define('ESIGN_GOOGLE_CAPTCHA_PATH', dirname(__FILE__));
            
            // esig asset directory url
            if (!defined('ESIGN_RECAPTCHA_ASSET_URI'))
                  define('ESIGN_RECAPTCHA_ASSET_URI', plugins_url('assets', __FILE__));

require_once 'includes/class-google-recaptcha-settting.php';            
require_once 'controllers/recaptchaController.php';
require_once 'includes/class-google-recaptcha.php';
ESIG_GOOGLE_CAPTCHA::Init();
