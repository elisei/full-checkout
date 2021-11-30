/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

define([
    "Magento_Ui/js/form/element/abstract",
    "Magento_Checkout/js/model/default-post-code-resolver"
], function (
    Abstract,
    defaultPostCodeResolver
) {
    "use strict";

    return Abstract.extend({
        defaults: {
            skipValidation: false,
            imports: {
                update: '${ $.parentName.replace("street","postcode") }:value'
            }
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super();
            this.setVisible(false);

            return this;
        },

        /**
         * Callback change visible
         * 
         * @param {String} field
         */
        update: function (value) {
            if (value) {
                if (value.replace(/[^\d]/g, "").length === 8) {
                    return this.setVisible(true);
                }
            }
            this.setVisible(false);
        }
    });
});