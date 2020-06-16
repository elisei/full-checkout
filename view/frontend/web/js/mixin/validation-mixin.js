define([
    "jquery",
    "Magento_Ui/js/lib/validation/utils",
], function($, utils) {
    "use strict";

    function invalidosComunsCNPJ(value) {
        if (value === "00000000000000" || value === "11111111111111" || value === "22222222222222" || value === "33333333333333" || value === "44444444444444" || value === "55555555555555" || value === "66666666666666" || value === "77777777777777" || value === "88888888888888" || value === "99999999999999") {
            return true;
        }
        return false
    }

    function invalidosComunsCPF(value) {
        if (value === "00000000000" || value === "11111111111" || value === "22222222222" || value === "33333333333" || value === "44444444444" || value === "55555555555" || value === "66666666666" || value === "77777777777" || value === "88888888888" || value === "99999999999") {
            return true;
        }
        return false
    }

    function validateCPF(val) {
        let cpf = val.replace(/[^\d]+/g, "");

        let v1 = 0;
        let v2 = 0;
        let aux = false;

        if (cpf.length !== 11) {
            return false;
        }

        if (invalidosComunsCPF(cpf)) {
            return false;
        }

        for (let i = 1; cpf.length > i; i++) {
            if (cpf[i - 1] != cpf[i]) {
                aux = true;
            }
        }

        if (aux == false) {
            return false;
        }

        for (let k = 0, p = 10;
            (cpf.length - 2) > k; k++, p--) {
            v1 += cpf[k] * p;
        }

        v1 = ((v1 * 10) % 11);

        if (v1 == 10) {
            v1 = 0;
        }

        if (v1 != cpf[9]) {
            return false;
        }

        for (let j = 0, p = 11;
            (cpf.length - 1) > j; j++, p--) {
            v2 += cpf[j] * p;
        }

        v2 = ((v2 * 10) % 11);

        if (v2 == 10) {
            v2 = 0;
        }

        if (v2 != cpf[10]) {
            return false;
        } else {
            return true;
        }

        return true;
    }

    function validateCNPJ(value) {
        let cnpj = value.replace(/[^\d]+/g, "");

        if (cnpj.length !== 14) {
            return false;
        }

        if (invalidosComunsCNPJ(cnpj)) {
            return false;
        }

        let v1 = 0;
        let v2 = 0;
        let aux = false;

        for (let i = 1; cnpj.length > i; i++) {
            if (cnpj[i - 1] != cnpj[i]) {
                aux = true;
            }
        }

        if (aux == false) {
            return false;
        }

        for (let k = 0, p1 = 5, p2 = 13;
            (cnpj.length - 2) > k; k++, p1--, p2--) {
            if (p1 >= 2) {
                v1 += cnpj[k] * p1;
            } else {
                v1 += cnpj[k] * p2;
            }
        }

        v1 = (v1 % 11);

        if (v1 < 2) {
            v1 = 0;
        } else {
            v1 = (11 - v1);
        }

        if (v1 != cnpj[12]) {
            return false;
        }

        for (let j = 0, p1 = 6, p2 = 14;
            (cnpj.length - 1) > j; j++, p1--, p2--) {
            if (p1 >= 2) {
                v2 += cnpj[j] * p1;
            } else {
                v2 += cnpj[j] * p2;
            }
        }

        v2 = (v2 % 11);

        if (v2 < 2) {
            v2 = 0;
        } else {
            v2 = (11 - v2);
        }

        if (v2 != cnpj[13]) {
            return false;
        } else {
            return true;
        }

        return true;
    }

    return function(validator) {

        validator.addRule(
            "telephone-br-rule",
            function(value) {
                if (value.length === 13) {
                    return value.match(/^([()])([0-9]){2}([)])([0-9]){5}([-])([0-9]){3}$/);
                }
                return value.length === 14 && value.match(/^([()])([0-9]){2}([)])([0-9]){4,5}([-])([0-9]){4}$/);

            },
            $.mage.__("Insira um telefone válido, pode ser fixo ou celular.")
        );
        validator.addRule(
            "vatid-br-rule",
            function(value) {
                if (value.length === 18) {
                    return validateCNPJ(value);
                }
                if (value.length === 14) {
                    return validateCPF(value);
                }
            },
            $.mage.__("Insira um documento fiscal válido, pode ser CPF ou CNPJ")
        );


        return validator;
    }
});