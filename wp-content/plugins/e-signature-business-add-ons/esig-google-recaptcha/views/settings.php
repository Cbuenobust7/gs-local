<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>

<?php 

include($this->rootDir . ESIG_DS . 'partials/_tab-nav.php'); 

// To default a var, add it to an array
	$vars = array(
		'other_form_element', // will default $data['other_form_element']
		'pdf_options', 
		'active_campaign_options'
	);
	$this->default_vals($data, $vars);
?>

<div class="esign-main-tab">

 <a class="misc_link " href="admin.php?page=esign-misc-general"><?php _e('General Option', 'esig'); ?></a> 
 
 | <a class="misc_link" href="admin.php?page=esign-mails-general"><?php _e('White Label Option', 'esig'); ?></a> 
  
 
 <?php echo $data['customizztion_more_links']; ?>

</div>	
<h3>Google reCAPTCHA keys</h3>

 <?php echo esigget('message'); ?>
<form name="settings_form" class="settings-form" method="post" action="<?php echo $data['post_action']; ?>">	
<table class="form-table">
	<tbody>
            
                <tr>
                    <td>
                        <?php _e("Register your domain name with Google reCaptcha v3 service and add the keys to the fields below.","esig") ; ?>
                        <a href="https://www.google.com/recaptcha/admin#list" target="_blank"> <?php _e("Get your API Keys","esig") ; ?> </a>
                    </td>
                </tr>
                
                <tr>
                    <td>
                      <?php  _e("Need Help?","esig"); ?> <a href="https://www.approveme.com/" target="_blank"> Read the Instruction </a>
                    </td>
                </tr>
		
		<tr>
                    <td>
                        <label style="font-weight: bold !important;"><?php _e('Site Key', 'esig' ); ?> </label>
                        <input type="text" name="esig_recaptcha_site_key" class="regular-text" id="esig_recaptcha_site_key" value="<?php echo esigRecaptchaSettings::getsiteKey(); ?>">
                    </td>
                </tr>
                
		<tr>
                    <td>
                        <label style="font-weight: bold !important;"><?php _e('Secret Key', 'esig' ); ?> </label>
                        <input type="text" name="esig_recaptcha_secret_key" class="regular-text" id="esig_recaptcha_secret_key" value="<?php echo esigRecaptchaSettings::getSecretKey(); ?>">
                    </td>
                </tr>
                
                <?php 
                $disabled = (esigRecaptchaSettings::isKeysEnabled())? "" : "Disabled";
                $disableStyle = (esigRecaptchaSettings::isKeysEnabled())? "" : 'style="opacity: 0.7;"';
                ?>
                
                <tr>
                    <td <?php echo $disableStyle; ?>>
                        <label for="">
                            <input name="esig_recaptcha_global" <?php echo $disabled; ?> id="esig_recaptcha_global" type="checkbox" value="1" <?php if(esigRecaptchaSettings::isGlobalRecaptchaEnabled()) echo "checked"; ?>> <?php _e('Enable reCaptcha for all documents signing page.', 'esig' ); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <td <?php echo $disableStyle; ?>>
                        <label for="">
                            <input name="esig_recaptcha_badge" <?php echo $disabled; ?> id="esig_recaptcha_badge" type="checkbox" value="1" <?php if(esigRecaptchaSettings::isHideBadge()) echo "checked"; ?>> <?php _e('Hide reCaptcha badge on documents signing page.', 'esig' ); ?>
                        </label>
                    </td>
                </tr>
		 
	</tbody>
</table>
		

	<p>
		<input type="submit" name="esig-recaptcha-submit" class="button-appme button" value="Save Settings" />
	</p>
</form>
