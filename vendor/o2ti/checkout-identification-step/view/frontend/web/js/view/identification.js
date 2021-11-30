/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */
define([
	"jquery",
	"ko",
	"uiComponent",
	"underscore",
	"Magento_Checkout/js/model/step-navigator",
	"Magento_Customer/js/customer-data",
	"Magento_Customer/js/model/customer",
	"Magento_Checkout/js/checkout-data",
	"mage/translate",
], function (
	$,
	ko,
	Component,
	_,
	stepNavigator,
	customerData,
	customer,
	checkoutData,
	$t
) {
	"use strict";
	return Component.extend({
		defaults: {
			template: "O2TI_CheckoutIdentificationStep/identification",
		},
		identifierSelector: "form[data-role=email-with-possible-login]",
		isVisible: ko.observable(!customer.isLoggedIn()),
		isVisibleLogged: ko.observable(customer.isLoggedIn()),

		/**
		 * Extends instance with defaults
		 * 
		 *  @returns {Object} Chainable.
		 */
		initialize() {
			this._super();
			stepNavigator.registerStep(
				"identification",
				null,
				$t("Identification"),
				this.isVisible, _.bind(this.navigate, this),
				this.sortOrder
			);
			return this;
		},

		/**
		 * Navigator change hash handler.
		 *
		 * @param {Object} step - navigation step
		 */
		navigate(step) {
			if (customer.isLoggedIn()) {
				this.isVisible(false);
				stepNavigator.setHash('identification');
				this.navigateToNextStep();
			} else {
				this.isVisible(true);
			}
		},


		/**
		 * Is Validate Email
		 * 
		 *  @returns {bool} emailValidationResult.
		 */
		validateEmail() {
			var emailValidationResult = customer.isLoggedIn();
			if (!customer.isLoggedIn()) {
			$(this.identifierSelector).validation();
			emailValidationResult = Boolean(
				$(this.identifierSelector + " input[name=username]").valid()
			);
			}
			return emailValidationResult;
		},

		/**
		 * Navigate To Nex Step
		 */
		navigateToNextStep() {
			if (this.validateEmail()) {
				stepNavigator.next();
			} else {
				$(this.identifierSelector + " input[name=username]").focus();
			}
		},
	});
});
