/**
	* Copyright © O2TI. All rights reserved.
	* @author    Bruno Elisei <brunoelisei@o2ti.com>
	* See COPYING.txt for license details.
	*/
/**
 * @api
 */
define([
	"jquery",
	"underscore",
	"mageUtils",
	"uiRegistry",
	"Magento_Ui/js/form/element/abstract",
	"uiLayout",
	"Magento_Customer/js/zxcvbn",
	"mage/translate",
	"mage/validation"
], function ($, _, utils, registry, Abstract, layout, zxcvbn, $t) {
	'use strict';

	return Abstract.extend({
		defaults: {
			visible: true,
			label: '',
			error: '',
			uid: utils.uniqueid(),
			uAdditionalid: utils.uniqueid(),
			disabled: false,
			links: {
				value: '${ $.provider }:${ $.dataScope }'
			},
			autocomplete: '',
			placeholder: '',
			emailSelector: 'input[type="email"]',
			isViewCreatePassword: false,
			passwordInputType: 'password',
			textInputType: 'text'
			
		},

		/**
		 * Extends instance with defaults
		 */
		initialize: function () {
			this._super();
			this._super().observe([
				"isViewCreatePassword"
			]);
			return this;
		},

		/**
		 * Calls 'initObservable' of parent
		 *
		 * @returns {Object} Chainable.
		 */
		initObservable: function () {
			this._super()
					.observe('disabled visible value isViewCreatePassword');
			this.isViewCreatePassword.subscribe(function (isChecked) {
				console.log("change");
				this._showPassword(isChecked);
			}.bind(this));
			return this;
		},

		/**
		 * Show/Hide password
		 * @private
		 */
		_showPassword(isChecked) {
			var currentFieldType = $("#"+this.uid).attr('type');
			$("#"+this.uid).attr('type',this.passwordInputType);
			if(currentFieldType === 'password'){
				$("#"+this.uid).attr('type',this.textInputType);
			}
			$("#create-password-show-password").toggleClass('_view');
		},

		/**
		 * Callback that fires when 'value' property is updated.
		 */
		onUpdate: function () {
			this.bubble('update', this.hasChanged());
			this._calculateStrength();
			this.validate();
		},

		/**
		 * Calculate password strength
		 * @private
		 */
		_calculateStrength: function () {
			var password = this.value(),
				isEmpty = password.length === 0,
				zxcvbnScore,
				displayScore,
				isValid,
				verication;
			if (isEmpty) {
				displayScore = 0;
			} else {
				isValid = $.validator.validateSingleElement($(this.uid));
				zxcvbnScore = zxcvbn(password).score;
				displayScore = isValid && zxcvbnScore > 0 ? zxcvbnScore : 1;
			}
			verication = this._displayStrength(displayScore);
			return verication;
		},

		/**
		 * Strength Class
		 */
		_strengthClass: function() {
			return this._calculateStrength().className;
		},

		/**
		 * Strength Label
		 */
		_strengthLabel: function() {
			var verification = this._calculateStrength();
			return this._calculateStrength().strengthLabel;
		},

		/**
		 * Display strength
		 * @param {Number} displayScore
		 * @private
		 */
		_displayStrength: function (displayScore) {
			var strengthLabel = '',
				className,
				data = {};

			switch (displayScore) {
				case 0:
					strengthLabel = $t('No Password');
					className = 'password-none';
					break;

				case 1:
					strengthLabel = $t('Weak');
					className = 'password-weak';
					break;

				case 2:
					strengthLabel = $t('Medium');
					className = 'password-medium';
					break;

				case 3:
					strengthLabel = $t('Strong');
					className = 'password-strong';
					break;

				case 4:
					strengthLabel = $t('Very Strong');
					className = 'password-very-strong';
					break;
			}
			data = {'className': className, 'strengthLabel': strengthLabel};
			return data;
		},


		/**
		 * Has service
		 *
		 * @returns {Boolean} false.
		 */
		hasService: function () {
			return false;
		},

		/**
		 * Has addons
		 *
		 * @returns {Boolean} false.
		 */
		hasAddons: function () {
			return false;
		},

		
	});
});
