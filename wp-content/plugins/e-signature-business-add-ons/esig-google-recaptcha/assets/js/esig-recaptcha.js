/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



(function ($) {


    $('.signature-wrapper-displayonly').click(function (e) {

        e.preventDefault();

        grecaptcha.ready(function () {
            grecaptcha.execute(_grecaptcha_callback(), {action: 'submit'}).then(function (token) {
                //<intput type="text" name="esig_recaptcha_validation_token" id="esig_recaptcha_validation_token" 
                $("#sign-form .signatures").append("<input type='hidden' name='esig_recaptcha_validation_token' id='esig_recaptcha_validation_token' value='" + token + "'>");

                // Add your logic to submit to your backend server here.
            });
        });

    });


})(jQuery);


window._grecaptcha_callback = () => {

    var clientId = window.grecaptcha.render({
        sitekey: esig_recaptcha.esig_google_captcha_site_key,
        badge: 'bottomleft',
    });

    return clientId;

}
