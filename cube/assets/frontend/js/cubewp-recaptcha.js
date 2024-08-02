var cubewpCaptchaLoaded = function() {
    (function ($) {
        'use strict';
        $.cubewp_recaptcha = $.cubewp_recaptcha || {};

        var captcha_init = $(".cubewp-form-recaptcha");
        if (captcha_init.length > 0) {
            captcha_init.each(function () {
                var captcha_init_id = $(this).attr("id");
                grecaptcha.render(captcha_init_id, {
                    'sitekey' : cubewp_recaptcha_params.site_key,
                });
            });
        }

    })(jQuery);
};
