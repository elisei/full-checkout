/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */
define([
	"jquery",
	"uiComponent",
	"Magento_Checkout/js/model/quote",
	"Magento_Checkout/js/model/step-navigator",
	"Magento_Checkout/js/model/sidebar",
	"Magento_Customer/js/model/customer",
	"Magento_Checkout/js/checkout-data",
	"Magento_Customer/js/customer-data",
	"mage/translate"
], function ($, Component, quote, stepNavigator, sidebarModel, customer, checkoutData, customerData, $t) {
	"use strict";

	return Component.extend({
		defaults: {
			template: "O2TI_CheckoutIdentificationStep/identification-information"
		},

		/**
		 * @return {Boolean}
		 */
		isVisible: function () {
			return stepNavigator.isProcessed("identification");
		},

		/**
		 * @return {String}
		 */
		getTypeIdentify: function () {
			if(!customer.isLoggedIn()){
				return $t("Checkout as a guest using email");   
			}
			return $t('You are logged in with the email');
		},

		/**
		 * @return {String}
		 */
		getEmail: function () {
			if(!customer.isLoggedIn()){
				return quote.guestEmail;   
			}
			return customerData.get('customer')().email;
		},

		/**
		 * Back step.
		 */
		back: function () {
			sidebarModel.hide();
			stepNavigator.navigateTo("identification");
		},
	});
});
