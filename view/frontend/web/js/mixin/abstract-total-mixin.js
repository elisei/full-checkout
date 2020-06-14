/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    "Magento_Checkout/js/model/step-navigator"
], function (stepNavigator) {
    "use strict";
    return function (targetModule) {
        return targetModule.extend({
            isFullMode() {
                if(stepNavigator.isProcessed("shipping")){
                    if (!this.getTotals()) {
                        return false;
                    }
                }
                return true; 
            }
        });
    };
});