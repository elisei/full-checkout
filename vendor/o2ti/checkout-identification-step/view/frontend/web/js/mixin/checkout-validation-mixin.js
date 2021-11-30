/**
	* Copyright © O2TI. All rights reserved.
	* @author    Bruno Elisei <brunoelisei@o2ti.com>
	* See COPYING.txt for license details.
	*/
define([
	'jquery',
	'moment',
	'mageUtils',
	'jquery-ui-modules/widget',
	'jquery/validate',
	'mage/translate'
], function ($, moment, utils) {
	'use strict';
	
	return function (validator) {

		/**
		 * Add Validate password
		 */
		validator.addRule(
			 "validate-custom-customer-password",
			function (v, element, params) {
				var validator = this,
				counter = 0,
				passwordMinLength = params.length,
				passwordMinCharacterSets = params.characterClassesNumber,
				pass = $.trim(v),
				result = pass.length >= passwordMinLength;
				if (result === false) {
					this.passwordErrorMessage = $.mage.__('Minimum length of this field must be equal or greater than %1 symbols. Leading and trailing spaces will be ignored.').replace('%1', passwordMinLength); //eslint-disable-line max-len
					return false;
				}

				if (pass.match(/\d+/)) {
					counter++;
				}

				if (pass.match(/[a-z]+/)) {
					counter++;
				}

				if (pass.match(/[A-Z]+/)) {
					counter++;
				}

				if (pass.match(/[^a-zA-Z0-9]+/)) {
					counter++;
				}

				if (counter < passwordMinCharacterSets) {
					this.passwordErrorMessage = $.mage.__('Minimum of different classes of characters in password is %1. Classes of characters: Lower Case, Upper Case, Digits, Special Characters.').replace('%1', passwordMinCharacterSets); //eslint-disable-line max-len
					return false;
				}
				
				return true;
			},
			$.mage.__('Minimum of different classes of characters in password. Classes of characters: Lower Case, Upper Case, Digits, Special Characters.') //eslint-disable-line max-len
		);
		
		/**
		 * Add Validate Dob
		 */
		validator.addRule(
			"validate-dob",
			function (value, element, params) {
				var dateFormata = utils.convertToMomentFormat(params.dateFormat);
				if (value === '') {
					return true;
				}
				return moment(value, dateFormata).isBefore(moment());
			},
			$.mage.__('The Date of Birth should not be greater than today.')
		);
		return validator;
	};
});