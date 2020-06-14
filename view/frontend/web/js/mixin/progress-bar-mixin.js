/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([], function () {
    "use strict";
    return function (target) {
        return target.extend({
            defaults: {
                template: "O2TI_FullCheckout/progress-bar",
                visible: true
            }
        });
    }
});