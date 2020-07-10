/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
  "jquery",
  "ko",
  "uiComponent",
  "underscore",
], function (
  $,
  ko,
  Component,
  _,
) {
  "use strict";
  return Component.extend({
    defaults: {
      template: "O2TI_FullCheckout/header/logo",
    },
    getLogoSrc() {
      return window.checkoutConfig.logo_src;
    },
    getLogoWidth() {
      return window.checkoutConfig.logo_width;
    },
    getLogoHeight() {
      return window.checkoutConfig.logo_height;
    },
    getLogoAlt() {
      return window.checkoutConfig.logo_alt;
    },
  });
});
