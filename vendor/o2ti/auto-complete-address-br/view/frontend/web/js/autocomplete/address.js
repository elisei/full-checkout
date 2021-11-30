/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

define([
	"uiRegistry",
	"jquery",
	"mage/url",
	"jquery-ui-modules/widget"
], function (registry, $, url) {
	"use strict";
   
	$.widget('mage.autoCompleteAddressBr', {
		_create(){
			this._super();
			if(this.options.enabled) {
				this._getChangeInPostcode();
			}
		},
		_getChangeInPostcode(){
			var self = this;
			let input = self.element;
			input.on("change keyup paste", function () {
				if(self.element.val().replace(/[^\d]/g, "").length === 8){
					self._getAddressByPostcode();
				}
			});
		},
		_getAddressByPostcode(){
			var self = this;
			let input = self.element;
			var cep = input.val().replace(/[^\d]/g, "");
			var formKey = $.cookie("form_key");
			var getaddress = url.build("autocompleteaddressbr/postcode/address/zipcode/" + cep + "/form_key/" + formKey + "/");
			$.ajax({
				url: getaddress,
				dataType: "json",
				timeout: 4000,
			}).done(function (data) {
				if (data.success) {
					Object.keys(data.street).forEach(function(i) {
						var index = Number(i);
						var indexForId = index+1;
						var streetId = "#street_"+indexForId;
						if($(streetId).length){
						  $(streetId).val(data.street[index]);
						}
					});

					if($("#city").length){
						$("#city").val(data.city);
					}

					if($("#country_id").length){
						$("#country_id").val(data.country_id).change();
					}

					if($("#region_id").length){
						$("#region_id").val(data.region_id).change();
					}
				}
			});
		}
	});

	return $.mage.autoCompleteAddressBr;
});