/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

define([
    "jquery",
    "jquery-ui-modules/widget",
    "mask"
], function ($) {
    "use strict";
   
    $.widget('mage.inputMaskingAccount', {

        /**
         * {@inheritdoc}
         */
        _create: function () {
            this._super();
            if(this.options.enable) {
                this._createMask();
            }
        },

        /**
         * Create Mask
         */
        _createMask: function() {
            var self = this;
            let input = self.element;
            let typeMask = self.options.typeMask;
            input.on("change keyup paste", function () {
              if(typeMask !== "self._setCpfCnpjMaskBehavior()"){
                input.mask(typeMask, { clearIfNotMatch: self.options.cleanIfNotMatch });
              } else {
                input.mask(self._setCpfCnpjMaskBehavior(), { clearIfNotMatch: self.options.cleanIfNotMatch });
              }
            });
        },

        /**
         * CPF or CNPK Mask Behavior
         */
        _setCpfCnpjMaskBehavior() {
          var self = this;
          let input = self.element;
          let lengthDocument = $(input).val().replace(/\D/g, "").length;
          return lengthDocument <= 11 ? "000.000.000-009" : "00.000.000/0000-00";
        },
    });

    return $.mage.inputMaskingAccount;
});
