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
        defaults: {
            elementTmpl: "O2TI_FullCheckout/form/element/number"
        },
        initialize() {
            this._super();
            var telephoneMask = this.mask;
            $("#"+this.uid).mask(telephoneMask);
            return this;
        },
        onUpdate() {
            var validate = this.validate();
            this.bubble("update", this.hasChanged());
            var telephoneMask = this.mask;
            $("#"+this.uid).mask(telephoneMask);
        }
    });
});