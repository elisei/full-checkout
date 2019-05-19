/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
 define([
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
    'jquery',
    'mask'
], function (registry, abstract, jQuery, mask) {
    'use strict';
    return abstract.extend({
        initialize: function () {
            this._super();
            jQuery('#'+this.uid).mask(this.mask);
            return this;
        },
        onUpdate: function () {
            var validate = this.validate();
            this.bubble('update', this.hasChanged());
            jQuery('#'+this.uid).mask(this.mask);
        }
    });
});