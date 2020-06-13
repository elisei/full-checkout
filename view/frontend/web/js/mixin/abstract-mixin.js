define(function () {
    'use strict';

    var mixin = {
        getTemplate: function () {
            var template = this._super();
            if(template == 'ui/form/field' || template == 'ui/group/group'){
                template = 'O2TI_FullCheckout/form/field';
            }
            return template;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});