/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
 define([
    "uiRegistry",
    "Magento_Ui/js/form/element/abstract",
    "jquery",
    "mask",
    "mage/url",
    "Magento_Checkout/js/model/shipping-service",
    "Magento_Checkout/js/model/quote",
    "Magento_Checkout/js/model/resource-url-manager",
    "Magento_Checkout/js/model/shipping-rate-registry",
    "Magento_Checkout/js/model/error-processor",
    "Magento_Checkout/js/model/step-navigator",
    "mage/storage",
], function (registry, abstract, $, mask, url, shippingService, quote, resourceUrlManager, rateRegistry, errorProcessor, stepNavigator,storage) {
    "use strict";
    return abstract.extend({

        initialize() {
            this._super();
            var element = this;
            this.toggleFields(element);
            var postcodeMask = this.mask;
            $("#"+this.uid).mask(postcodeMask);
            return this;
        },
        toggleFields(element){
            if(this.value()){
                var validate = this.validate().valid;
                if(validate === true && this.value().length === 9){
                    if(registry.get(element.parentName + "." + "street.0")){
                        registry.get(element.parentName + "." + "street.0").visible(true);
                        registry.get(element.parentName + "." + "street.1").visible(true);
                        registry.get(element.parentName + "." + "street.2").visible(true);
                        registry.get(element.parentName + "." + "street.3").visible(true);
                    }
                    if(registry.get(element.parentName + "." + "city")){
                        registry.get(element.parentName + "." + "city").visible(true);
                    }
                    if(registry.get(element.parentName + "." + "region_id")){
                                registry.get(element.parentName + "." + "region_id").visible(true);
                    }
                }
            } else {
                if(registry.get(element.parentName + "." + "street.0")){
                    registry.get(element.parentName + "." + "street.0").visible(false);
                    registry.get(element.parentName + "." + "street.1").visible(false);
                    registry.get(element.parentName + "." + "street.2").visible(false);
                    registry.get(element.parentName + "." + "street.3").visible(false);
                }
                if(registry.get(element.parentName + "." + "city")){
                    registry.get(element.parentName + "." + "city").visible(false);
                }
                if(registry.get(element.parentName + "." + "region_id")){
                    registry.get(element.parentName + "." + "region_id").visible(false);
                }
            }
            return this;
        },
        onUpdate() {
         
            var element = this;
            this.toggleFields(element);
            if(this.value() && this.value().length === 9){
                var validate = this.validate();
                if(validate.valid == true){
                    var cep = this.value();
                    var formKey = $.cookie("form_key");
                    var getaddress = url.build("full_checkout/postcode/address/zipcode/"+cep+"/form_key/"+formKey);
                    $.ajax({
                        url: getaddress,
                        dataType: "json",
                        timeout: 4000
                    }).done(function (data) {
                        if(data.success){
                            if(registry.get(element.parentName + "." + "street.0")){
                                registry.get(element.parentName + "." + "street.0").value(data.street);
                            }
                            if(registry.get(element.parentName + "." + "street.2")){
                                registry.get(element.parentName + "." + "street.2").value(data.neighborhood);
                            }
                           
                            if(registry.get(element.parentName + "." + "city")){
                                registry.get(element.parentName + "." + "city").value(data.city);
                            }
                            if(registry.get(element.parentName + "." + "region_id")){
                                registry.get(element.parentName + "." + "region_id").value(data.uf);
                            }
                            if(registry.get(element.parentName + "." + "country_id")){
                                registry.get(element.parentName + "." + "country_id").value("BR");
                            }
                            var number = registry.get(element.parentName + "." + "street.1").uid;
                            $("#"+number).focus();
                           

                        }
                    }).always(function () {
                       
                        var address = quote.shippingAddress(); 
                        var serviceUrl, payload;
                       
                        serviceUrl = resourceUrlManager.getUrlForEstimationShippingMethodsForNewAddress(quote);
                        payload = JSON.stringify({
                                address: {
                                    "country_id": registry.get(element.parentName + "." + "country_id").value(),
                                    "region_id": registry.get(element.parentName + "." + "region_id").value(),
                                    "city": registry.get(element.parentName + "." + "city").value(),
                                    "postcode": cep,
                                }
                            }
                        );
                        shippingService.isLoading(true);
                        storage.post(
                            serviceUrl, payload, false
                        ).done(function (result) {
                            shippingService.setShippingRates(result);
                            shippingService.isLoading(false);
                        }).fail(function (response) {
                            shippingService.setShippingRates([]);
                            errorProcessor.process(response);
                        }).always(function () {
                            shippingService.isLoading(false);
                        });
                    });
                }
            }  
        }
    });
});