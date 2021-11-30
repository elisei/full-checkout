/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */
define([
	"jquery",
	"mage/storage",
	"Magento_Ui/js/model/messageList",
	"Magento_Customer/js/customer-data",
	"mage/translate"
], function ($, storage, globalMessageList, customerData, $t) {
	"use strict";

	var callbacks = [],

		/**
		 * @param {Object} messageContainer
		 */
		action = function (messageContainer) {
			messageContainer = messageContainer || globalMessageList;

			return storage.get(
				'customer/ajax/logout',
				false
			).done(function (response) {
				if (response.errors) {
					messageContainer.addErrorMessage(response);
					callbacks.forEach(function (callback) {
						callback();
					});
				} else {
					location.reload();
				}
			}).fail(function () {
				messageContainer.addErrorMessage({
					'message': $t('Unable to leave. Please try again later')
				});
				callbacks.forEach(function (callback) {
					callback();
				});
			});
		};

	/**
	 * @param {Function} callback
	 */
	action.registerLogoutCallback = function (callback) {
		callbacks.push(callback);
	};

	return action;
});
