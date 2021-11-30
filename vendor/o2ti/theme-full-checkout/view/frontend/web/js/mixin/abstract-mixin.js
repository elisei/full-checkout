/**
 * Copyright © 2021 O2TI. All rights reserved.
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

define([], function () {
	"use strict";
	var mixin = {
			getTemplate() {
				var template = this._super();
				if (template === "ui/form/field" || template === "ui/form/group") {
					template = "O2TI_ThemeFullCheckout/form/field";
				}
				return template;
			},
		};
	return function (target) {
		return target.extend(mixin);
	};
});
