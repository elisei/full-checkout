/**
 * Copyright © 2021 O2TI. All rights reserved.
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

var config = {
    config: {
        mixins: {
            "Magento_Ui/js/form/element/abstract": {
                "O2TI_ThemeFullCheckout/js/mixin/abstract-mixin": true
            },
            "Magento_Checkout/js/action/select-shipping-method": {
                "O2TI_ThemeFullCheckout/js/mixin/action/select-shipping-method-mixin": true
            },
            "Magento_Checkout/js/view/progress-bar": {
                "O2TI_ThemeFullCheckout/js/mixin/progress-bar-mixin": true
            },
            "Magento_Checkout/js/view/sidebar": {
                "O2TI_ThemeFullCheckout/js/mixin/sidebar-mixin": true
            },
            "Magento_Checkout/js/view/shipping": {
                "O2TI_ThemeFullCheckout/js/mixin/shipping-mixin": true
            },
            "Magento_Checkout/js/view/payment": {
                "O2TI_ThemeFullCheckout/js/mixin/payment-mixin": true
            },
            "Magento_Checkout/js/view/payment/list": {
                "O2TI_ThemeFullCheckout/js/mixin/payment/list-mixin": true
            },
            "Magento_Checkout/js/view/shipping-information": {
                "O2TI_ThemeFullCheckout/js/mixin/shipping-information-mixin": true
            },
            "Magento_Checkout/js/view/billing-address": {
                "O2TI_ThemeFullCheckout/js/mixin/billing-address-mixin": true
            },
            "Magento_Checkout/js/view/summary/shipping": {
                "O2TI_ThemeFullCheckout/js/mixin/summary-shipping-mixin": true
            },
            "Magento_Checkout/js/view/summary/abstract-total": {
                "O2TI_ThemeFullCheckout/js/mixin/abstract-total-mixin": true
            },
            "Magento_Checkout/js/view/summary/item/details": {
                "O2TI_ThemeFullCheckout/js/mixin/summary/item/details-mixin": true
            },
            "Magento_Checkout/js/view/summary/item/details/thumbnail": {
                "O2TI_ThemeFullCheckout/js/mixin/summary/item/details/thumbnail-mixin": true
            },
            "Magento_Checkout/js/view/summary/cart-items": {
                "O2TI_ThemeFullCheckout/js/mixin/summary/cart-items-mixin": true
            },
            "Magento_SalesRule/js/view/payment/discount": {
                "O2TI_ThemeFullCheckout/js/mixin/sales-rule/payment/discount-mixin": true
            }
        }
    }
};
