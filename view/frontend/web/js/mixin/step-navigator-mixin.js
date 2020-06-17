/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
  "jquery",
  "Magento_Customer/js/model/customer",
  "Magento_Checkout/js/model/quote",
], function ($, customer, quote) {
  "use strict";
  return function (targetModule) {
    targetModule.navigateTo = function (code, scrollToElementId) {
      var sortedItems = targetModule.steps().sort(targetModule.sortItems),
        bodyElem = $("body");

      scrollToElementId = scrollToElementId || null;

      if (!targetModule.isProcessed(code)) {
        return;
      }
      sortedItems.forEach(function (element) {
        if (element.code === code) {
          //eslint-disable-line eqeqeq
          element.isVisible(true);
          bodyElem.animate(
            {
              scrollTop: $("#" + code).offset().top,
            },
            0,
            function () {
              window.location = window.checkoutConfig.checkoutUrl + "#" + code;
            }
          );

          if (scrollToElementId && $("#" + scrollToElementId).length) {
            bodyElem.animate(
              {
                scrollTop: $("#" + scrollToElementId).offset().top,
              },
              0
            );
          }
        } else {
          element.isVisible(false);
        }
      });
    };

    targetModule.handleHash = function () {
      var hashString = window.location.hash.replace("#", ""),
        isRequestedStepVisible;

      if (hashString === "_=_") {
        window.location.href = window.checkoutConfig.checkoutUrl;
        return false;
      }
      if (hashString === "") {
        return false;
      }

      if ($.inArray(hashString, this.validCodes) === -1) {
        window.location.href = window.checkoutConfig.checkoutUrl;

        return false;
      }

      isRequestedStepVisible = targetModule.steps
        .sort(this.sortItems)
        .some(function (element) {
          return (
            (element.code === hashString || element.alias === hashString) &&
            element.isVisible()
          ); //eslint-disable-line
        });

      //if requested step is visible, then we don"t need to load step data from server
      if (isRequestedStepVisible) {
        return false;
      }

      targetModule
        .steps()
        .sort(this.sortItems)
        .forEach(function (element) {
          if (element.code === hashString || element.alias === hashString) {
            //eslint-disable-line eqeqeq
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
