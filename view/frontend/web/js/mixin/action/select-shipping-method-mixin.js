/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    "underscore",
    "jquery",
    "uiRegistry",
    "Magento_Checkout/js/model/quote",
    "Magento_Checkout/js/model/resource-url-manager",
    "mage/storage"
], function (_, $, registry, quote, resourceUrlManager, storage) {
    "use strict";
    return function (targetModule) {
        return function (selectShippingMethod) {
            targetModule(selectShippingMethod); 
            var shippingAddress = registry.get("checkout.steps.shipping-step.shippingAddress");
            if (typeof shippingAddress === "undefined" || shippingAddress.rates().length <= 1) {
                return;
            }
            if(selectShippingMethod === null){
                return;
            }
            var setClassselectShippingMethod = $(".label_carrier_" + selectShippingMethod["method_code"] + "_" + selectShippingMethod["carrier_code"]).addClass("selected-shipping");
            var payload = {
                addressInformation: {
                    "shipping_address": quote.shippingAddress(),
                    "shipping_method_code": selectShippingMethod["method_code"],
                    "shipping_carrier_code": selectShippingMethod["carrier_code"]
                }
            };
            return storage.post(
                resourceUrlManager.getUrlForSetShippingInformation(quote),
                JSON.stringify(payload)
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                  
                }
            );
        };
    }
});