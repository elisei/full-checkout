/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([], function () {
    "use strict";    
    return function (targetModule) {
        return targetModule.extend({
            defaults: {
                template: "O2TI_FullCheckout/shipping",
                shippingFormTemplate: "Magento_Checkout/shipping-address/form",
                shippingMethodListTemplate: "O2TI_FullCheckout/shipping-address/shipping-method-list",
                shippingMethodItemTemplate: "O2TI_FullCheckout/shipping-address/shipping-method-item"
            },
        });
    }
});