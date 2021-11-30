/**
 * Copyright © 2021 O2TI. All rights reserved.
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

define([
	"underscore",
	"jquery",
	"uiRegistry",
	"Magento_Checkout/js/model/quote",
	"Magento_Checkout/js/model/resource-url-manager",
	"mage/storage",
], function (_, $, registry, quote, resourceUrlManager, storage) {
	"use strict";
	return function (targetModule) {
		return function (selectShippingMethod) {
			targetModule(selectShippingMethod);
			var shippingAddress = registry.get(
				"checkout.steps.shipping-step.shippingAddress"
			);
			if (
				typeof shippingAddress === "undefined" ||
				shippingAddress.rates().length <= 1
			) {
				return;
			}
			if (selectShippingMethod === null) {
				return;
			}
			var payload = {
				addressInformation: {
					shipping_address: quote.shippingAddress(),
					shipping_method_code: selectShippingMethod["method_code"],
					shipping_carrier_code: selectShippingMethod["carrier_code"],
				},
			};
			return storage
				.post(
					resourceUrlManager.getUrlForSetShippingInformation(quote),
					JSON.stringify(payload)
				)
				.done(function (response) {
					quote.setTotals(response.totals);
				});
		};
	};
});
