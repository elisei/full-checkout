/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */
 var config = {
    config: {
        mixins: {
            "Magento_Checkout/js/view/shipping": {
                "O2TI_CheckoutIdentificationStep/js/mixin/view/shipping-mixin": true
            },
            "Magento_Ui/js/lib/validation/validator": {
                "O2TI_CheckoutIdentificationStep/js/mixin/checkout-validation-mixin": true
            }
        }
    }
};