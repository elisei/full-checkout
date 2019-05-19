/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote'
], function ($, customer, quote) {
    'use strict';
    return function (targetModule) {
        targetModule.navigateTo = function (code, scrollToElementId) {
            if (customer.isLoggedIn() && code === 'email') {
                return;
            }
            var sortedItems = targetModule.steps().sort(this.sortItems);
            if (!this.isProcessed(code)) {
                return;
            }
            window.location = window.checkoutConfig.checkoutUrl + '#' + code;
            sortedItems.forEach(function (element) {
                element.isVisible(element.code == code);
            });
        };
        targetModule.handleHash = function () {
            var hashString = window.location.hash.replace('#', ''),
                isRequestedStepVisible;
            if (hashString === '') {
                if (!customer.isLoggedIn()) {
                    targetModule.navigateTo('email');
                }
                targetModule.navigateTo(quote.isVirtual() ? 'payment' : 'shipping');
                return false;
            }
            if ($.inArray(hashString, this.validCodes) === -1) {
                window.location.href = window.checkoutConfig.pageNotFoundUrl;
                return false;
            }
            isRequestedStepVisible = targetModule.steps().sort(this.sortItems).some(function (element) {
                return (element.code == hashString || element.alias == hashString) && element.isVisible();
            });
            if (isRequestedStepVisible) {
                return false;
            }
            targetModule.steps().sort(this.sortItems).forEach(function (element) {
                if (element.code == hashString || element.alias == hashString) {
                    element.navigate(element);
                } else {
                    element.isVisible(false);
                }
            });
            return false;
        };
        return targetModule;
    };
});