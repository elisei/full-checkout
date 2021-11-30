/**
 * Copyright © 2021 O2TI. All rights reserved.
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

define(["Magento_Checkout/js/model/step-navigator"], function (stepNavigator) {
	"use strict";
	return function (targetModule) {
		return targetModule.extend({
			isVisibleOrderDetails() {
				if(stepNavigator.isProcessed('identification')) {
					return true;
				}
				if(stepNavigator.isProcessed('shipping')) {
					return true;
				}
				return false;
			},
		});
	};
});
