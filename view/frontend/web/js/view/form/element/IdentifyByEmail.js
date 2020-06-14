/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    "jquery",
    "uiComponent",
    "ko",
    "Magento_Customer/js/model/customer",
    "Magento_Customer/js/action/check-email-availability",
    "Magento_Customer/js/action/login",
    "Magento_Checkout/js/model/quote",
    "Magento_Checkout/js/checkout-data",
    "Magento_Checkout/js/model/full-screen-loader",
    "Magento_Checkout/js/model/step-navigator",
    "mage/validation"
], function ($, Component, ko, customer, checkEmailAvailability, loginAction, quote, checkoutData, fullScreenLoader, stepNavigator) {
    "use strict";

    var validatedEmail;

    if (!checkoutData.getValidatedEmailValue() &&
        window.checkoutConfig.validatedEmailValue
    ) {
        checkoutData.setInputFieldEmailValue(window.checkoutConfig.validatedEmailValue);
        checkoutData.setValidatedEmailValue(window.checkoutConfig.validatedEmailValue);
    }

    validatedEmail = checkoutData.getValidatedEmailValue();

    if (validatedEmail && !customer.isLoggedIn()) {
        quote.guestEmail = validatedEmail;
    }

    return Component.extend({
        defaults: {
            template: "O2TI_FullCheckout/form/element/identify-by-email",
            email: checkoutData.getInputFieldEmailValue(),
            emailFocused: false,
            isLoading: false,
            isPasswordVisible: false,
            enableNext: false,
            listens: {
                email: "emailHasChanged",
                emailFocused: "validateEmail"
            },
            ignoreTmpls: {
                email: true
            }
        },
        checkDelay: 500,
        checkRequest: null,
        isEmailCheckComplete: null,
        isCustomerLoggedIn: customer.isLoggedIn,
        forgotPasswordUrl: window.checkoutConfig.forgotPasswordUrl,
        emailCheckTimeout: 0,

        /**
         * Initializes regular properties of instance.
         *
         * @returns {Object} Chainable.
         */
        initConfig: function () {
            this._super();

            
            this.enableNext = this.resolveInitialEnableNextVisibility();
            this.isPasswordVisible = this.resolveInitialPasswordVisibility();

            return this;
        },

        initialize: function() {
            this._super();
            this.checkEmailAvailability();
            this.resolveInitialEnableNextVisibility();
            this.resolveInitialPasswordVisibility();
            return this;
        },
        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(["email", "emailFocused", "isLoading", "isPasswordVisible", "enableNext"]);

            return this;
        },

        /**
         * Callback on changing email property
         */
        emailHasChanged: function () {
            var self = this;

            clearTimeout(this.emailCheckTimeout);

            if (self.validateEmail()) {
                quote.guestEmail = self.email();
                checkoutData.setValidatedEmailValue(self.email());
            }
            this.emailCheckTimeout = setTimeout(function () {
                if (self.validateEmail()) {
                    self.checkEmailAvailability();
                } else {
                    self.isPasswordVisible(false);
                    self.enableNext(true);
                }
            }, self.checkDelay);

            checkoutData.setInputFieldEmailValue(self.email());
        },

        /**
         * Check email existing.
         */
        checkEmailAvailability: function () {
            this.validateRequest();
            this.isEmailCheckComplete = $.Deferred();
            this.isLoading(true);
            this.checkRequest = checkEmailAvailability(this.isEmailCheckComplete, this.email());
            
            $.when(this.isEmailCheckComplete).done(function () {
                this.isPasswordVisible(false);
                this.enableNext(true);
            }.bind(this)).fail(function () {
                this.isPasswordVisible(true);
                this.enableNext(false);
                checkoutData.setCheckedEmailValue(this.email());
            }.bind(this)).always(function () {
                this.isLoading(false);
            }.bind(this));
        },

        /**
         * If request has been sent -> abort it.
         * ReadyStates for request aborting:
         * 1 - The request has been set up
         * 2 - The request has been sent
         * 3 - The request is in process
         */
        validateRequest: function () {
            if (this.checkRequest != null && $.inArray(this.checkRequest.readyState, [1, 2, 3])) {
                this.checkRequest.abort();
                this.checkRequest = null;
            }
        },

        /**
         * Local email validation.
         *
         * @param {Boolean} focused - input focus.
         * @returns {Boolean} - validation result.
         */
        validateEmail: function (focused) {
            var loginFormSelector = "form[data-role=email-with-possible-login]",
                usernameSelector = loginFormSelector + " input[name=username]",
                loginForm = $(loginFormSelector),
                validator,
                valid;

            loginForm.validation();

            if (focused === false && !!this.email()) {
                valid = !!$(usernameSelector).valid();

                if (valid) {
                    $(usernameSelector).removeAttr("aria-invalid aria-describedby");
                }

                return valid;
            }

            validator = loginForm.validate();

            return validator.check(usernameSelector);
        },

        continueOSC: function() {
            var loginFormSelector = "form[data-role=email-with-possible-login]";
            if (this.validateEmail()) {
                stepNavigator.next();
            } else {
                $(this.loginFormSelector + " input[name=username]").focus();
            }
        },
        // 
        
        /**
         * Log in form submitting callback.
         *
         * @param {HTMLElement} loginForm - form element.
         */
        login: function (loginForm) {
            var loginData = {},
                formDataArray = $(loginForm).serializeArray();

            formDataArray.forEach(function (entry) {
                loginData[entry.name] = entry.value;
            });

            if (this.isPasswordVisible() && $(loginForm).validation() && $(loginForm).validation("isValid")) {
                fullScreenLoader.startLoader();
                loginAction(loginData).always(function () {
                    fullScreenLoader.stopLoader();
                });
            }
        },

        resolveInitialEnableNextVisibility: function() {
           
            if (checkoutData.getInputFieldEmailValue() !== "" && checkoutData.getCheckedEmailValue() === "") {
                return true;
            }

            if (checkoutData.getInputFieldEmailValue() !== "" && this.resolveInitialPasswordVisibility() === false) {
                return true;
            }

            return false;
        },
        /**
         * Resolves an initial state of a login form.
         *
         * @returns {Boolean} - initial visibility state.
         */
        resolveInitialPasswordVisibility: function () {
            
            if (checkoutData.getInputFieldEmailValue() !== "" && checkoutData.getCheckedEmailValue() === "") {
                return true;
            }

            if (checkoutData.getInputFieldEmailValue() !== "") {
                return checkoutData.getInputFieldEmailValue() === checkoutData.getCheckedEmailValue();
            }

            return false;
        }
    });
});
