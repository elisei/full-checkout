/**
 * Copyright © 2021 O2TI. All rights reserved.
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

define([], function () {
	"use strict";
	return function (targetModule) {
		return targetModule.extend({
			defaults: {
				template: "O2TI_ThemeFullCheckout/summary/item/details/thumbnail"
			},
		});
	};
});