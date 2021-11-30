/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */
/**
 * @api
 */
define(
	[
		"jquery",
		"mage/storage",
		"Magento_Checkout/js/model/error-processor",
		"Magento_Checkout/js/model/full-screen-loader",
		"Magento_Customer/js/customer-data",
		"Magento_Customer/js/model/customer",
		"mage/url",
		"Magento_Ui/js/model/messageList",
	],
	function ($, storage, errorProcessor, fullScreenLoader, customerData, customer, urlBuilder, globalMessageList) {
		"use strict";

		return function (payload, messageContainer) {
			messageContainer = messageContainer || globalMessageList;
			
			var serviceUrl,
				headers = {};

			if (!customer.isLoggedIn()) {
				var formKey = $.cookie("form_key")
				serviceUrl = urlBuilder.build("checkout/customer/accountcreatepost/form_key/" + formKey + "/");
			} else {
				return false;
			}

			fullScreenLoader.startLoader();

			return storage.post(
				serviceUrl, payload, false, 'application/x-www-form-urlencoded', headers
			).fail(
				function (response) {
					errorProcessor.process(response, messageContainer);
				}
			).done(
				function (response) {
					if (response.errors) {
						messageContainer.addErrorMessage(response);
					} else {
						location.reload();
					}
				}
			).always(
				function () {
					fullScreenLoader.stopLoader();
				}
			);
		};
	}
);
