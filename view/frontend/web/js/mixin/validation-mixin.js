define([
    'jquery',
    'Magento_Ui/js/lib/validation/utils',
], function($, utils) {
    'use strict';
    return function(validator) {
        validator.addRule(
            'telephone-br-rule',
            function (value) {
               if(value.length == 13){
                
                return value.match(/^([()])([0-9]){2}([)])([0-9]){5}([-])([0-9]){3}$/);
               }
              return value.length == 14 && value.match(/^([()])([0-9]){2}([)])([0-9]){4,5}([-])([0-9]){4}$/);
               
            },
            $.mage.__('Insira um telefone válido, pode ser fixo ou celular.')
        );
        validator.addRule(
            'vatid-br-rule',
            function (value) {
               if(value.length == 18){
                let CNPJ = value.replace(/[^\d]+/g,'');
                if(CNPJ == '') return false;
                if (CNPJ.length != 14)
                    return false;
                if (CNPJ == "00000000000000" || 
                    CNPJ == "11111111111111" || 
                    CNPJ == "22222222222222" || 
                    CNPJ == "33333333333333" || 
                    CNPJ == "44444444444444" || 
                    CNPJ == "55555555555555" || 
                    CNPJ == "66666666666666" || 
                    CNPJ == "77777777777777" || 
                    CNPJ == "88888888888888" || 
                    CNPJ == "99999999999999")
                    return false;
                let tamanho = CNPJ.length - 2
                let numeros = CNPJ.substring(0,tamanho);
                let digitos = CNPJ.substring(tamanho);
                let soma = 0;
                let pos = tamanho - 7;
                let i;
                let resultado;
                for (i = tamanho; i >= 1; i--) {
                  soma += numeros.charAt(tamanho - i) * pos--;
                  if (pos < 2)
                        pos = 9;
                }
                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(0))
                    return false;
                     
                tamanho = tamanho + 1;
                numeros = CNPJ.substring(0,tamanho);
                soma = 0;
                pos = tamanho - 7;
                for (i = tamanho; i >= 1; i--) {
                  soma += numeros.charAt(tamanho - i) * pos--;
                  if (pos < 2)
                        pos = 9;
                }
                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(1))
                      return false;
                return true;
                // value.match(/^([0-9]){2}([.])([0-9]){3}([.])([0-9]){3}([/])([0-9]){4}([-])([0-9]){2}$/);
               }
               if(value.length == 14){
                let cpf = value.replace(/[^\d]+/g,'');    
                if(cpf == '') return false;
                if (cpf.length != 11 || 
                    cpf == "00000000000" || 
                    cpf == "11111111111" || 
                    cpf == "22222222222" || 
                    cpf == "33333333333" || 
                    cpf == "44444444444" || 
                    cpf == "55555555555" || 
                    cpf == "66666666666" || 
                    cpf == "77777777777" || 
                    cpf == "88888888888" || 
                    cpf == "99999999999")
                        return false;    
                let add = 0;
                let i;
                let rev; 
                for (i=0; i < 9; i ++)      
                    add += parseInt(cpf.charAt(i)) * (10 - i);  
                    rev = 11 - (add % 11);  
                    if (rev == 10 || rev == 11)     
                        rev = 0;    
                    if (rev != parseInt(cpf.charAt(9)))     
                        return false;       
                // Valida 2o digito 
                add = 0;    
                for (i = 0; i < 10; i ++)       
                    add += parseInt(cpf.charAt(i)) * (11 - i);  
                rev = 11 - (add % 11);  
                if (rev == 10 || rev == 11) 
                    rev = 0;    
                if (rev != parseInt(cpf.charAt(10)))
                    return false;       
                return true;  
               }
            },
            $.mage.__('Insira um documento fiscal válido, pode ser CPF ou CNPJ')
        );
        return validator;
    }
});