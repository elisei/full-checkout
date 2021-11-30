/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        "*": {
            mask: "O2TI_InputMasking/js/mask/jquery.mask",
            inputMaskingAccount: "O2TI_InputMasking/js/mask/jquery.account.mask"
        },
    },
    shim: {
        mask: ["jquery"]
    }
};
