/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
var config = {
  map: {
    "*": {
      mask: "O2TI_FullCheckout/js/mask/jquery.mask",
      accountMask: "O2TI_FullCheckout/js/mask/jquery.account.mask",
    },
  },
  shim: {
    mask: ["jquery"],
  },
  config: {
    mixins: {
      "Magento_Ui/js/form/element/abstract": {
        "O2TI_FullCheckout/js/mixin/abstract-mixin": true,
      },
      "Magento_Ui/js/lib/validation/validator": {
        "O2TI_FullCheckout/js/mixin/validation-mixin": true,
      },
      "Magento_Checkout/js/model/step-navigator": {
        "O2TI_FullCheckout/js/mixin/step-navigator-mixin": true,
      },
      "Magento_Checkout/js/view/progress-bar": {
        "O2TI_FullCheckout/js/mixin/progress-bar-mixin": true,
      },
      "Magento_Checkout/js/view/shipping": {
        "O2TI_FullCheckout/js/mixin/shipping-mixin": true,
      },
      "Magento_Checkout/js/view/payment": {
        "O2TI_FullCheckout/js/mixin/payment-mixin": true,
      },
      "Magento_Checkout/js/view/summary/shipping": {
        "O2TI_FullCheckout/js/mixin/summary-shipping-mixin": true,
      },
      "Magento_Checkout/js/view/summary/abstract-total": {
        "O2TI_FullCheckout/js/mixin/abstract-total-mixin": true,
      },
      "Magento_Checkout/js/action/select-shipping-method": {
        "O2TI_FullCheckout/js/mixin/action/select-shipping-method-mixin": true,
      },
    },
  },
};
