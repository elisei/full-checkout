/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
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
    "mage/translate"
], function ($, ko, Component, _, stepNavigator, customerData, customer, checkoutData, $t) {
    "use strict";
    return Component.extend({
        defaults: {
            template: "O2TI_FullCheckout/identify"
        },
        identifierSelector: "form[data-role=email-with-possible-login]",

        isVisible: ko.observable(!customer.isLoggedIn()),

        initialize() {
            this._super();
            if(!customer.isLoggedIn()) {
                stepNavigator.registerStep("identify", null, $t("Identify"), this.isVisible, _.bind(this.navigate, this), this.sortOrder);
            }
            return this;
        },

        /**
         * Navigator change hash handler.
         *
         * @param {Object} step - navigation step
         */
        navigate(step) {
            if (customer.isLoggedIn()) {
                this.navigateToNextStep();
            } else {
                this.isVisible(true);
            }
        },


        validateEmail() {
            var emailValidationResult = customer.isLoggedIn();
            if (!customer.isLoggedIn()) {
                $(this.identifierSelector).validation();
                emailValidationResult = Boolean($(this.identifierSelector + " input[name=username]").valid());
            }
            return emailValidationResult;
        },

        navigateToNextStep() {
            if (this.validateEmail()) {
                stepNavigator.next();
            } else {
                $(this.identifierSelector + " input[name=username]").focus();
            }
        }
    });
});