/**
 * Copyright © 2021 O2TI. All rights reserved.
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

define(["Magento_Checkout/js/model/step-navigator"], function (stepNavigator) {
	"use strict";
	return function (targetModule) {
		return targetModule.extend({
			isFullMode() {
				if (stepNavigator.isProcessed("shipping")) {
					if (!this.getTotals()) {
						return false;
					}
				}
				return true;
			},
		});
	};
});
