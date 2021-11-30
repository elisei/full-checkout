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
			let self = this;
			if(this.maskEnable){
				let typeMask = self.mask;
				let useClearIfNotMatch = self.maskClearIfNotMatch;
				
				if(registry.get(this.parentName + "." + "company")){
					registry.get(this.parentName + "." + "company").visible(false);
				}
				
				if(typeMask !== "self._setCpfCnpjMaskBehavior()"){
					$("#" + this.uid).mask(typeMask, { clearIfNotMatch: useClearIfNotMatch });
				} else {
					$("#" + this.uid).mask(self._setCpfCnpjMaskBehavior(), { clearIfNotMatch: useClearIfNotMatch });
				}
			}
			return this;
		},

		/**
         * CPF or CNPK Mask Behavior
         */
		_setCpfCnpjMaskBehavior() {
			var lengthDocument = this.value().replace(/\D/g, "").length;
			if (registry.get(this.parentName + "." + "company")) {
				if (lengthDocument <= 11) {
					registry.get(this.parentName + "." + "company").visible(false);
				} else {
					registry.get(this.parentName + "." + "company").visible(true);
				}
			}
			return lengthDocument <= 11 ? "000.000.000-0099" : "00.000.000/0000-00";
		},

		/**
         * On Update
         */
		onUpdate() {
			let self = this;
			var validate = this.validate();
			this.bubble("update", this.hasChanged());
			if(this.maskEnable){
				let typeMask = self.mask;
				let useClearIfNotMatch = self.maskClearIfNotMatch;
				if(typeMask !== "self._setCpfCnpjMaskBehavior()"){
					$("#" + this.uid).mask(typeMask, { clearIfNotMatch: useClearIfNotMatch });
				} else {
					$("#" + this.uid).mask(self._setCpfCnpjMaskBehavior(), { clearIfNotMatch: useClearIfNotMatch });
				}
			}
		},
	});
});
