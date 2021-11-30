/**
 * Copyright © 2021 O2TI. All rights reserved.
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

define(["mage/translate"], function ($t) {
	"use strict";
	return function (targetModule) {
		return targetModule.extend({
			defaults: {
			template: "O2TI_ThemeFullCheckout/summary/cart-items"
			},
			isItemsBlockExpanded() {
				return true;
			}
		});
	};
});
