/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
 define([
    "uiRegistry",
    "Magento_Ui/js/form/element/abstract",
    "jquery",
    "mask"
], function (registry, abstract, $, mask) {
    "use strict";
    return abstract.extend({
        initialize() {
            this._super();          
            $("#"+this.uid).mask(this.maskToCpfCnpjMaskBehavior());
            return this;
        },
        maskToCpfCnpjMaskBehavior() {
            var  lengthDocument = this.value().replace(/\D/g, "").length;
            if(registry.get(this.parentName + "." + "company")){
                 if(lengthDocument <= 11){
                    registry.get(this.parentName + "." + "company").visible(false);
                } else {
                    registry.get(this.parentName + "." + "company").visible(true);
                }
            }
            return lengthDocument  <= 11 ? "000.000.000-009" : "00.000.000/0000-00";
        },
        onUpdate() {
            var validate = this.validate();
            this.bubble("update", this.hasChanged());
            $("#"+this.uid).mask(this.CpfCnpjMaskBehavior());
        }
    });
});