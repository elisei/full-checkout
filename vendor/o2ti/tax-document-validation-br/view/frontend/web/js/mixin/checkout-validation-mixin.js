/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

define(["jquery", "Magento_Ui/js/lib/validation/utils"], function ($, utils) {
	"use strict";

	return function (validator) {

		/**
		 * Invalidate Common CNPJ
		 */
		var invalidosComunsCNPJ = function (value) {
			if (
				value === "00000000000000" ||
				value === "11111111111111" ||
				value === "22222222222222" ||
				value === "33333333333333" ||
				value === "44444444444444" ||
				value === "55555555555555" ||
				value === "66666666666666" ||
				value === "77777777777777" ||
				value === "88888888888888" ||
				value === "99999999999999"
			) {
				return true;
			}
			return false;
		};

		/**
		 * Invalidate Common CNPJ
		 */
		var invalidosComunsCPF = function (value) {
			if (
				value === "00000000000" ||
				value === "11111111111" ||
				value === "22222222222" ||
				value === "33333333333" ||
				value === "44444444444" ||
				value === "55555555555" ||
				value === "66666666666" ||
				value === "77777777777" ||
				value === "88888888888" ||
				value === "99999999999"
			) {
				return true;
			}
			return false;
		};

		/**
		 * Validate CPF
		 */
		var validateCPF = function (value) {
			let cpf = value.replace(/[^\d]+/g, "");

			if (cpf.length !== 11) {
				return false;
			}

			if (invalidosComunsCPF(cpf)) {
				return false;
			}

			let add = 0;
			let i;
			let j;
			let rev;
			for (i = 0; i < 9; i++) {
				add += parseInt(cpf.charAt(i), 10) * (10 - i);
			}

			rev = 11 - (add % 11);
			if (rev === 10 || rev === 11) {
				rev = 0;
			}
			if (rev !== parseInt(cpf.charAt(9), 10)) {
				return false;
			}

			add = 0;
			for (j = 0; j < 10; j++) {
				add += parseInt(cpf.charAt(j), 10) * (11 - j);
			}

			rev = 11 - (add % 11);

			if (rev === 10 || rev === 11) {
				rev = 0;
			}

			if (rev !== parseInt(cpf.charAt(10), 10)) {
				return false;
			}

			return true;
		};

		/**
		 * Validate CNPJ
		 */
		var validateCNPJ = function (value) {
			let cnpj = value.replace(/[^\d]+/g, "");

			if (cnpj.length !== 14) {
				return false;
			}

			if (invalidosComunsCNPJ(cnpj)) {
				return false;
			}

			let tamanho = cnpj.length - 2;
			let numeros = cnpj.substring(0, tamanho);
			let digitos = cnpj.substring(tamanho);
			let soma = 0;
			let pos = tamanho - 7;
			let i;
			let j;
			let resultado;
			for (i = tamanho; i >= 1; i--) {
				soma += numeros.charAt(tamanho - i) * pos--;
				if (pos < 2) {
					pos = 9;
				}
			}
			resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);

			if (resultado !== parseInt(digitos.charAt(0), 10)) {
				return false;
			}

			tamanho = tamanho + 1;
			numeros = cnpj.substring(0, tamanho);
			soma = 0;
			pos = tamanho - 7;
			for (j = tamanho; j >= 1; j--) {
				soma += numeros.charAt(tamanho - j) * pos--;
				if (pos < 2) {
					pos = 9;
				}
			}
			resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);

			if (resultado !== parseInt(digitos.charAt(1), 10)) {
				return false;
			}

			return true;
		};
		
		/**
		 * Add Validation CPF/CNPJ
		 */
		validator.addRule(
			"vatid-br-rule-cpf-or-cnpj",
				function (value) {
					if (value.replace(/[^\d]+/g, "").length === 14) {
						return validateCNPJ(value);
					}
					if (value.replace(/[^\d]+/g, "").length === 11) {
						return validateCPF(value);
					}
				},
				$.mage.__('Please provide a valid tax document (CPF/CNPJ)')
		);

		/**
		 * Add Validation CPF
		 */
		validator.addRule(
			 "vatid-br-rule-only-cpf",
				function (value) {
					if (value.replace(/[^\d]+/g, "").length === 11) {
						return validateCPF(value);
					}
				},
				$.mage.__("Please provide a valid tax document (CPF)")
		);

		/**
		 * Add Validation CNPJ
		 */
		validator.addRule(
			 "vatid-br-rule-only-cnpj",
				function (value) {
					if (value.replace(/[^\d]+/g, "").length === 14) {
						return validateCNPJ(value);
					}
				},
				$.mage.__("Please provide a valid tax document (CNPJ)")
		);

		return validator;
	};
});
