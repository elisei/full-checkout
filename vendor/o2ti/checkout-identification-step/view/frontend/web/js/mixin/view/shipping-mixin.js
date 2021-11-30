/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */
/*global define*/
define([
	"jquery",
	"Magento_Customer/js/model/customer",
	"Magento_Checkout/js/model/quote",
	"Magento_Checkout/js/checkout-data",
	"Magento_Checkout/js/model/step-navigator",
], function ($, customer, quote, checkoutData, stepNavigator) {
	"use strict";

	return function (Component) {
		return Component.extend({
			/**
			 * Set shipping information handler
			 */
			validateShippingInformation: function () {
				var result = this._super();
				if(!customer.isLoggedIn()){
					if(window.checkoutConfig.identificationConfig !== false){
						var loginFormSelector = "form[data-role=email-with-possible-login]",
							usernameSelector = loginFormSelector + " input[name=username]";
						if (!$(usernameSelector).val()) {
							stepNavigator.setHash("identification");
						}
					}
					if(!window.checkoutConfig.identificationConfig.isContiuneAsGuest){
						stepNavigator.setHash("identification");
					}
				}
				return result;
			},

			/**
			 * Navigator change hash handler.
			 *
			 * @param {Object} step - navigation step
			 */
			navigate: function (step) {
				var result = this._super(step);
				if(!customer.isLoggedIn()){
					if(window.checkoutConfig.identificationConfig !== false){
						if(!window.checkoutConfig.identificationConfig.isContiuneAsGuest){
							stepNavigator.setHash("identification");
							return false;
						}
					}
				}
				return result;
			},
		});
	}
});
