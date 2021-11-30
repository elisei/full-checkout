/**
	* Copyright © O2TI. All rights reserved.
	* @author    Bruno Elisei <brunoelisei@o2ti.com>
	* See COPYING.txt for license details.
	*/
define([
	"jquery",
	"uiComponent",
	"ko",
	"Magento_Customer/js/model/customer",
	"Magento_Customer/js/action/check-email-availability",
	"Magento_Customer/js/action/login",
	"O2TI_CheckoutIdentificationStep/js/action/logout",
	"Magento_Checkout/js/model/quote",
	"Magento_Checkout/js/checkout-data",
	"Magento_Checkout/js/model/full-screen-loader",
	"Magento_Checkout/js/model/step-navigator",
	"Magento_Customer/js/customer-data",
	"Magento_Ui/js/modal/confirm",
	"mage/translate",
	"mage/validation",
], function (
	$,
	Component,
	ko,
	customer,
	checkEmailAvailability,
	loginAction,
	logoutAction,
	quote,
	checkoutData,
	fullScreenLoader,
	stepNavigator,
	customerData,
	confirmation,
	$t
) {
	"use strict";

	var validatedEmail;

	if (
		!checkoutData.getValidatedEmailValue() &&
		window.checkoutConfig.validatedEmailValue
	) {
		checkoutData.setInputFieldEmailValue(
			window.checkoutConfig.validatedEmailValue
		);
		checkoutData.setValidatedEmailValue(
			window.checkoutConfig.validatedEmailValue
		);
	}

	validatedEmail = checkoutData.getValidatedEmailValue();

	if (validatedEmail && !customer.isLoggedIn()) {
		quote.guestEmail = validatedEmail;
	}

	return Component.extend({
		passwordSelector: '#customer-password',
		passwordInputType: 'password',
		textInputType: 'text',

		defaults: {
			template: "O2TI_CheckoutIdentificationStep/form/step/identification",
			email: checkoutData.getInputFieldEmailValue(),
			emailFocused: false,
			isLoading: false,
			isPasswordVisible: false,
			listens: {
				email: "emailHasChanged",
				emailFocused: "validateEmail",
			},
			ignoreTmpls: {
				email: true,
			}
		},
		checkDelay: 500,
		checkRequest: null,
		isEmailCheckComplete: null,
		isCustomerLoggedIn: ko.observable(customer.isLoggedIn()),
		forgotPasswordUrl: window.checkoutConfig.forgotPasswordUrl,
		emailCheckTimeout: 0,
		resolveTypeIdentification: true,
		enableNext: true,

		/**
		 * Initializes regular properties of instance.
		 *
		 * @returns {Object} Chainable.
		 */
		initConfig() {
			this._super();
			this.enableNext = true;
			this.isPasswordVisible = this.resolveInitialPasswordVisibility();
			return this;
		},

		/**
		 * Extends instance with defaults
		 * 
		 * @returns {Object} Chainable.
		 */
		initialize() {
			this._super();
			this.checkEmailAvailability();
			this.enableNext(true);
			this.resolveInitialEnableNextVisibility();
			this.resolveInitialPasswordVisibility();
			return this;
		},

		/**
		 * Initializes observable properties of instance
		 *
		 * @returns {Object} Chainable.
		 */
		initObservable() {
			this._super().observe([
				"email",
				"emailFocused",
				"isLoading",
				"isPasswordVisible",
				"enableNext",
				"isViewPassword",
				"resolveTypeIdentification"
			]);

			this.isViewPassword.subscribe(function (isChecked) {
				this._showPassword(isChecked);
			}.bind(this));
			return this;
		},

		/**
		 * Callback on changing email property
		 */
		emailHasChanged() {
			var self = this;
			self.resolveTypeIdentification(null);
			self.enableNext(true);
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
				}
			}, self.checkDelay);
			checkoutData.setInputFieldEmailValue(self.email());
		},

		/**
		 * Check email existing.
		 */
		checkEmailAvailability() {
			this.validateRequest();
			this.isEmailCheckComplete = $.Deferred();
			this.isLoading(true);
			this.checkRequest = checkEmailAvailability(
				this.isEmailCheckComplete,
				this.email()
			);

			$.when(this.isEmailCheckComplete)
				.done(
					function () {
						this.resolveTypeIdentification('new-customer');
						this.isPasswordVisible(false);
						this.enableNext(this.isContiuneAsGuest());
					}.bind(this)
				)
				.fail(
					function () {
						this.resolveTypeIdentification('customer');
						this.isPasswordVisible(true);
						this.enableNext(this.isContiuneAsGuest());
						checkoutData.setCheckedEmailValue(this.email());
					}.bind(this)
				)
				.always(
					function () {
						this.isLoading(false);
					}.bind(this)
				);
		},

		/**
		 * If request has been sent -> abort it.
		 * ReadyStates for request aborting:
		 * 1 - The request has been set up
		 * 2 - The request has been sent
		 * 3 - The request is in process
		 */
		validateRequest() {
			if (
				this.checkRequest !== null &&
				$.inArray(this.checkRequest.readyState, [1, 2, 3])
			) {
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
		validateEmail(focused) {
			var loginFormSelector = "form[data-role=email-with-possible-login]",
				usernameSelector = loginFormSelector + " input[name=username]",
				loginForm = $(loginFormSelector),
				validator,
				valid;

			loginForm.validation();

			if (focused === false && !!this.email()) {
					valid = !!$(usernameSelector).valid();
					if (valid) {
							$(usernameSelector).removeAttr('aria-invalid aria-describedby');
					}

					return valid;
			}

			if (focused === true) {
					valid = !!$(usernameSelector).valid();

					if (valid) {
							$(usernameSelector).removeAttr('aria-invalid aria-describedby');
					}

					return valid;
			}

			if (loginForm.is(':visible')) {
					validator = loginForm.validate();

					return validator.check(usernameSelector);
			}

			return true;
		},

		/**
		 * Log in form submitting callback.
		 *
		 * @param {HTMLElement} loginForm - form element.
		 */
		login(loginForm) {
				var loginData = {},
						formDataArray = $(loginForm).serializeArray();

				formDataArray.forEach(function (entry) {
						loginData[entry.name] = entry.value;
				});

				if (this.isPasswordVisible() && $(loginForm).validation() && $(loginForm).validation('isValid')) {
						fullScreenLoader.startLoader();
						loginAction(loginData).always(function () {
								fullScreenLoader.stopLoader();
						});
				}
		},

		/**
		 * Text for Form Create Account
		 * 
		 * @returns string;
		 */
		textLoginAccount(){
			if(window.checkoutConfig.identificationConfig.isContiuneAsGuest){
				return $t('You already have an account with us. Sign in or continue as guest');
			}
			return $t('You already have an account with us. Please sign in');
		},

		/**
		 * Next Step Checkout if is Validate
		 */
		continueOSC() {
			var loginFormSelector = "form[data-role=email-with-possible-login]",
			usernameSelector = loginFormSelector + " input[name=username]";
			if (this.validateEmail($(usernameSelector).length)) {
				stepNavigator.next();
			} else {
				$(usernameSelector).focus();
			}
		},

		/**
		 * Logout Action
		 */
		logout(logoutForm) {
			var logoutData = {},
				textAlert;
			
			textAlert = $t('We will keep your items and you can use another email to complete your purchase.');

			if(!this.isCleanOnLogout) {
				textAlert = $t('When exiting your account your cart will be deleted.');
			}

			confirmation({
				title: $t('Do you want to log out?'),
				content: textAlert,
				actions: {

					confirm: function () {
						fullScreenLoader.startLoader();
						logoutAction(logoutForm).always(function () {
								fullScreenLoader.stopLoader();
						});
					},

					cancel: function () {
						return false;
					}
				}
			});
			
		},

		/**
		 * Get Customer Data
		 * 
		 * @returns {Object} Chainable.
		 */
		getCustomerData(){
			if(customer.isLoggedIn()) {
				return customerData.get('customer')();
			}
		},

		/**
		 * Get Customer Data FirstName
		 * 
		 * @returns {string} firstname.
		 */
		getCustomerFirstName(){
			if(customer.isLoggedIn()) {
				return customerData.get('customer')().firstname;
			}
		},

		/**
		 * Get Customer Data Email
		 * 
		 * @returns {string} email.
		 */
		getCustomerEmail(){
			if(customer.isLoggedIn()) {
				return customerData.get('customer')().email;
			}
		},

		/**
		 * Get Text to Continue OSC
		 * 
		 * @return {string} text
		 */
		continueOSCText() {
			console.log(this.resolveTypeIdentification());
			if(this.resolveTypeIdentification() === 'customer' || this.resolveTypeIdentification() === 'new-customer') {
				return $t('Continue as guest');
			}
			return $t('Next');
		},

		/**
		 * Is Logout Visible
		 * 
		 * @returns {bool} isLogoutVisible.
		 */
		isLogoutVisible(){
			return window.checkoutConfig.identificationConfig.isLogoutVisible;
		},

		/**
		 * Is Clean On Logout
		 * 
		 * @returns {bool} isCleanOnLogout.
		 */
		isCleanOnLogout(){
			return window.checkoutConfig.identificationConfig.isCleanOnLogout;
		},

		/**
		 * Is Continue As Guest
		 * 
		 * @returns {bool} isContiuneAsGuest.
		 */
		isContiuneAsGuest(){
			return window.checkoutConfig.identificationConfig.isContiuneAsGuest;
		},

		/**
		 * Is Remember Me Checkbox Visible
		 * 
		 * @returns {bool} isRememberMeCheckboxVisible.
		 */
		isRememberMeCheckboxVisible(){
			return window.checkoutConfig.persistenceConfig.isRememberMeCheckboxVisible;
		},

		/**
		 * Is Remember Me Checkbox Checked
		 * 
		 * @returns {bool} isRememberMeCheckboxChecked.
		 */
		isRememberMeCheckboxChecked(){
			if(window.checkoutConfig.persistenceConfig.isRememberMeCheckboxChecked){
				return 'checked';
			}
		},

		/**
		 * Resolver if is New Customer
		 * 
		 * @returns bool
		 */
		isNewCustomer() {
			if(this.resolveTypeIdentification() === 'new-customer' && checkoutData.getInputFieldEmailValue() !== "") {
				return true;
			}
		},

		/**
		 * Resolver if is Customer
		 * 
		 * @returns bool
		 */
		isCustomerNotLogged() {
			if(this.resolveTypeIdentification()  === 'customer' && checkoutData.getInputFieldEmailValue() !== "") {
				return true;
			}
		},

		/**
		 * Show/Hide password
		 * @private
		 */
		_showPassword(isChecked) {
			var currentFieldType = $(this.passwordSelector).attr('type');
			$(this.passwordSelector).attr('type',this.passwordInputType);
			if(currentFieldType === 'password'){
				$(this.passwordSelector).attr('type',this.textInputType);
			}
			$("#show-password").toggleClass('_view');
		},

		enableNextVisibility(){
			if(checkoutData.getInputFieldEmailValue() === ''){
				return true;	
			}
			return this.enableNext();
		},

		/**
		 * Resolves an initial state of a buton Next.
		 *
		 * @returns {Boolean} - initial visibility state.
		 */
		resolveInitialEnableNextVisibility() {
			if (
				checkoutData.getInputFieldEmailValue() !== "" &&
				checkoutData.getCheckedEmailValue() === ""
			) {
				return true;
			}

			if (
				checkoutData.getInputFieldEmailValue() !== "" &&
				this.resolveInitialPasswordVisibility() === false
			) {
				return true;
			}

			return false;
		},

		/**
		 * Resolves an initial state of a login form.
		 *
		 * @returns {Boolean} - initial visibility state.
		 */
		resolveInitialPasswordVisibility() {
			if (
				checkoutData.getInputFieldEmailValue() !== "" &&
				checkoutData.getCheckedEmailValue() === ""
			) {
				return true;
			}

			if (checkoutData.getInputFieldEmailValue() !== "") {
				return (
					checkoutData.getInputFieldEmailValue() ===
					checkoutData.getCheckedEmailValue()
				);
			}

			return false;
		},
	});
});
