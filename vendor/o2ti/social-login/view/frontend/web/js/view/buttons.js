/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(["ko", "uiComponent"], function (ko, Component) {
    "use strict";

    return Component.extend({
        isVisible: ko.observable(true),
        initialize() {
            this._super();
            isVisible: ko.observable(this.data.socialLogin.enabled);
        },
        isEnabled(provider) {
            if (provider === "facebook") {
                return this.data.socialLogin.providers.facebook;
            }
            if (provider === "google") {
                return this.data.socialLogin.providers.google;
            }
            if (provider === "WindowsLive") {
                return this.data.socialLogin.providers.WindowsLive;
            }
        },
        getRedirectUrl(provider) {
            return this.data.socialLogin.redirectUrl + "provider/" + provider;
        },
    });
});
