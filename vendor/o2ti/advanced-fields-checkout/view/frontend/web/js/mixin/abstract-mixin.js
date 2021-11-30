/**
 * Copyright © 2021 O2TI. All rights reserved.
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */
define([], function () {
	"use strict";
	return function (Component) {
		return Component.extend({
			 defaults: {
				autocomplete: '',
				elementTmpl: 'O2TI_AdvancedFieldsCheckout/form/element/input',
				placeholder: ''
			 }
		});
	}
});
