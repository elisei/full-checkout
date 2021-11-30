/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

define([
	"uiRegistry",
	"Magento_Ui/js/form/element/abstract",
	"jquery",
	"mask",
], function (registry, abstract, $, mask) {
	"use strict";

	return abstract.extend({

		/**
         * {@inheritdoc}
         */
		initialize() {
			this._super();
			if(this.maskEnable){
				let typeMask = this.mask;
				let useClearIfNotMatch = this.maskClearIfNotMatch;
				$("#" + this.uid).mask(typeMask,  { clearIfNotMatch: useClearIfNotMatch });
			}
			return this;
		},

		/**
         * On Update
         */
		onUpdate() {
			var validate = this.validate();
			this.bubble("update", this.hasChanged());
			if(this.maskEnable){
				let typeMask = this.mask;
				let useClearIfNotMatch = this.maskClearIfNotMatch;
				$("#" + this.uid).mask(typeMask,  { clearIfNotMatch: useClearIfNotMatch });
			}
		},
	});
});
