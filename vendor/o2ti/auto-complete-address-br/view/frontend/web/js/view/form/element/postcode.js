/**
 * Copyright © O2TI. All rights reserved.
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

define([
	"uiRegistry",
	"Magento_Ui/js/form/element/abstract",
	"jquery",
	"mage/url"
], function (
	registry,
	abstract,
	$,
	url
) {
	"use strict";

	return abstract.extend({

		/**
         * {@inheritdoc}
         */
		initialize() {
			this._super();
			return this;
		},

		/**
         * on change
         */
		onChange() {
			if(this.value()) {
				if (this.value().replace(/[^\d]/g, "").length === 8) {
					this.getAddressByPostcode();
				}
			}
		},

		/**
         * on change
         */
		onUpdate() {
			if(this.value()) {
				if (this.value().replace(/[^\d]/g, "").length === 8) {
					this.getAddressByPostcode();
				}
			}
		},

		/**
         * Get Return Address API
         */
		getAddressByPostcode(){
			var element = this;
			var cep = this.value().replace(/[^\d]/g, "");
			var formKey = $.cookie("form_key");
			var getaddress = url.build("autocompleteaddressbr/postcode/address/zipcode/" + cep + "/form_key/" + formKey + "/");
			$.ajax({
				url: getaddress,
				dataType: "json",
				timeout: 4000,
			}).done(function (data) {
				if (data.success) {
					Object.keys(data.street).forEach(function(i) {
						if (registry.get(element.parentName + "." + "street."+i)) {
							registry.get(element.parentName + "." + "street."+i).value(data.street[i]);
						}
					});
					if (registry.get(element.parentName + "." + "city")) {
						registry.get(element.parentName + "." + "city").value(data.city);
					}
					
					if (registry.get(element.parentName + "." + "country_id")) {
						registry.get(element.parentName + "." + "country_id").value(data.country_id);
					}

					if (registry.get(element.parentName + "." + "region_id")) {
						registry.get(element.parentName + "." + "region_id").value(data.region_id);
					}
				}
			});
		}
	});
});