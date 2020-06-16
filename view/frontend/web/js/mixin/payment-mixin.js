/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([], function () {
    "use strict";
    return function (targetModule) {
        return targetModule.extend({
            defaults: {
                template: "O2TI_FullCheckout/payment"
            },
        });
    }
});