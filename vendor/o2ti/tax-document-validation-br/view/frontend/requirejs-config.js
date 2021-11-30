/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */
var config = {
    
    config: {
        mixins: {
            "Magento_Ui/js/lib/validation/validator": {
                "O2TI_TaxDocumentValidationBr/js/mixin/checkout-validation-mixin": true
            },
            "mage/validation": {
                "O2TI_TaxDocumentValidationBr/js/mixin/customer-validation-mixin": true
            }
        }
    }
};
