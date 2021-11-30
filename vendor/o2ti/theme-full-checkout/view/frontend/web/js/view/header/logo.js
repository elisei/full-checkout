/**
 * Copyright © 2021 O2TI. All rights reserved.
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

define([
	"jquery",
	"ko",
	"uiComponent",
	"underscore",
], function (
	$,
	ko,
	Component,
	_,
) {
	"use strict";
	return Component.extend({
		defaults: {
			template: "O2TI_ThemeFullCheckout/header/logo",
		},
		getLogoSrc() {
			return window.checkoutConfig.logo_src;
		},
		getLogoWidth() {
			return window.checkoutConfig.logo_width;
		},
		getLogoHeight() {
			return window.checkoutConfig.logo_height;
		},
		getLogoAlt() {
			return window.checkoutConfig.logo_alt;
		},
		getLogoUrl() {
			return window.checkoutConfig.cartUrl;
		}
	});
});
