/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

define([
	"Magento_Ui/js/view/messages",
	"O2TI_CheckoutIdentificationStep/js/model/create-account-messages"
], function (Component, messageContainer) {
	"use strict";

	return Component.extend({
		/** @inheritdoc */
		initialize: function (config) {
			return this._super(config, messageContainer);
		}
	});
});
