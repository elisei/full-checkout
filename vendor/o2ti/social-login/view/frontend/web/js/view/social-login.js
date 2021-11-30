/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(["ko", "uiComponent"], function (ko, Component) {
    "use strict";

    return Component.extend({
        defaults: {
            template: "O2TI_SocialLogin/social-login"
        },
        isVisible: ko.observable(window.checkoutConfig.socialLogin.enabled),
        isEnabled(provider) {
            if(provider === "facebook"){
                return window.checkoutConfig.socialLogin.providers.facebook;
            }
            if(provider === "google"){
                return window.checkoutConfig.socialLogin.providers.google;
            }
            if(provider === "WindowsLive"){
                return window.checkoutConfig.socialLogin.providers.WindowsLive;
            }
        },
        getRedirectUrl(provider) {
            return window.checkoutConfig.socialLogin.redirectUrl + "provider/" + provider;
        }
    });
});
