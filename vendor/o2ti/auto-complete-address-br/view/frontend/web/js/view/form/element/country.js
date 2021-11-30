/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, registry, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            imports: {
                update: '${ $.parentName }.website_id:value'
            }
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super();
            self = this._super();

            if(this.options().length === 1){
                this.setVisible(false);
            }
            
            _.filter(this.options(), function (item) {
                if(_.contains(item, item['is_default'])) {
                    if(item['value'] === 'BR') {
                        self.setVisible(false);
                    }
                }
            });

            return this;
        },

        /**
         * Filters 'initialOptions' property by 'field' and 'value' passed,
         * calls 'setOptions' passing the result to it
         *
         * @param {*} value
         * @param {String} field
         */
        filter: function (value, field) {
            var result, defaultCountry, defaultValue;

            if (!field) { //validate field, if we are on update
                field = this.filterBy.field;
            }

            this._super(value, field);
            result = _.filter(this.initialOptions, function (item) {

                if (item[field]) {
                    return ~item[field].indexOf(value);
                }

                return false;
            });
            this.setOptions(result);
            this.reset();

            if (!this.value()) {
                defaultCountry = _.filter(result, function (item) {
                    return item['is_default'] && _.contains(item['is_default'], value);
                });

                if (defaultCountry.length) {
                    defaultValue = defaultCountry.shift();
                    this.value(defaultValue.value);
                }
            }
        }
    });
});

