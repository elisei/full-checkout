define([
    "jquery",
    "mage/url",
    "mask"
], function ($, url, mask) {
    "use strict";
    return function (config) {
        function setCpfCnpjMaskBehavior(vat) {
           var  lengthDocument = vat.replace(/\D/g, "").length;
            if($("#company").length){
                 if(lengthDocument <= 11){
                    $(".field.company").hide();
                } else {
                    $(".field.company").show();
                }
            }
            return lengthDocument  <= 11 ? "000.000.000-009" : "00.000.000/0000-00";
        }
        function completeAddressBr(cep) {
                var formKey = jQuery.cookie("form_key");
                var getaddress = url.build("full_checkout/postcode/address/zipcode/"+cep+"/form_key/"+formKey);
                jQuery.ajax({
                    url: getaddress,
                    dataType: "json",
                    timeout: 4000
                }).done(function (data) {
                    if(data.success){
                        if($("#street_1")){
                            $("#street_1").val(data.street);
                        }
                        if($("#street_3")){
                            $("#street_3").val(data.neighborhood);
                        }
                       
                        if($("#city")){
                            $("#city").val(data.city);
                        }
                        if($("#region_id")){
                            $("#region_id").val(data.uf);
                        }
                        if($("#country_id")){
                            $("#country_id").val("BR");
                        }
                        var number = $("#street.1").uid;
                        jQuery("#"+number).focus();
                    }
                });
        }
        $(function () {
            if($(config.maskRegion).selector === $("#country").val()){
                $("#zip").mask("00000-000", {
                                                onComplete(cep) {
                                                    completeAddressBr(cep);
                                                }
                                            });
                $("#telephone").mask("(00)00000-0000", {clearIfNotMatch: true}).addClass("telephone-br-rule");
                var vat = $("#vat_id");
                vat.addClass("vatid-br-rule");
                vat.on("change keyup paste",function(){
                    var typeMaskVat = setCpfCnpjMaskBehavior(vat.val());
                    vat.mask(typeMaskVat, {clearIfNotMatch: true});
                });
            }
        });
    };
});