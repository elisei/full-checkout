<?php
/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace O2TI\FullCheckout\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class CheckoutLayoutPlugin
{
    const CONFIG_PATH_FIELD_ORDER_PATH = 'full_checkout/field_order';
    const CONFIG_PATH_FIELD_MASK_PATH = 'full_checkout/field_mask/%s';

    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    private function getFieldOrder()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_FIELD_ORDER_PATH, ScopeInterface::SCOPE_STORE);
    }

    private function disableAuthentication($jsLayout)
    {
        unset($jsLayout['components']['checkout']['children']['authentication']);

        return $jsLayout;
    }

    private function changeShippingFields($jsLayout)
    {
        $shippingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
        $shippingFields = $this->changeStreetStructure($shippingFields);
        $shippingFields = $this->createMaskFields($shippingFields);
        $shippingFields = $this->changeComponentFields($shippingFields);
        $shippingFields = $this->createValidationFields($shippingFields);
        $shippingFields = $this->createTooltipFields($shippingFields);
        $shippingFields = $this->createPlaceholders($shippingFields);
        $fieldOrder = $this->getFieldOrder();
        foreach ($shippingFields as $fieldName => $shippingField) {
            if (isset($fieldOrder[$fieldName])) {
                $shippingFields[$fieldName]['sortOrder'] = $fieldOrder[$fieldName];
                $shippingField = $shippingField;
            }
        }

        return $jsLayout;
    }

    private function changeStreetStructure($fields)
    {
        foreach ($fields as $key => $data) {
            $data = $data;
            if ($key == 'street') {
                $defaultPosition = (int) $fields[$key]['sortOrder'];
                $fields[$key]['config'] = ['template' => 'O2TI_FullCheckout/form/element/addressline', 'fieldTemplate' =>  'O2TI_FullCheckout/form/field'];
                $fields[$key]['children'][0]['label'] = __('Address');
                $fields[$key]['children'][0]['sortOrder'] = $defaultPosition;
                $fields[$key]['children'][1]['label'] = __('Number');
                $fields[$key]['children'][1]['sortOrder'] = $defaultPosition + 1;
                $fields[$key]['children'][1]['config']['elementTmpl'] = 'O2TI_FullCheckout/form/element/number';
                $fields[$key]['children'][2]['label'] = __('Complement');
                $fields[$key]['children'][2]['sortOrder'] = $defaultPosition + 2;
                $fields[$key]['children'][3]['label'] = __('Neighborhood');
                $fields[$key]['children'][3]['sortOrder'] = $defaultPosition + 3;
                $fields[$key]['children'][0]['validation'] = ['required-entry' => 1, 'min_text_len‌​gth' => 1, 'max_text_length' => 255];
                $fields[$key]['children'][1]['validation'] = ['required-entry' => 1, 'min_text_len‌​gth' => 1, 'max_text_length' => 15];
                $fields[$key]['children'][3]['validation'] = ['required-entry' => 1, 'min_text_len‌​gth' => 1, 'max_text_length' => 255];
            }
        }

        return $fields;
    }

    private function createTooltipFields($fields)
    {
        foreach ($fields as $key => $data) {
            $data = $data;
            if ($key == 'vat_id') {
                $fields[$key]['config']['tooltip'] = ['description' => __('O Cpf ou Cnpj é utilizado para envio e emissão de nota fiscal.')];
            }
        }

        return $fields;
    }

    private function createValidationFields($fields)
    {
        foreach ($fields as $key => $data) {
            $data = $data;
            if ($key == 'vat_id') {
                $fields[$key]['validation'] = ['required-entry' => 1, 'min_text_len‌​gth' => 14, 'max_text_length' => 18, 'vatid-br-rule' => 1];
            } elseif ($key == 'telephone') {
                $fields[$key]['validation'] = ['required-entry' => 1, 'min_text_len‌​gth' => 13, 'max_text_length' => 14, 'telephone-br-rule' => 1];
            } elseif ($key == 'company') {
                $fields[$key]['validation'] = ['required-entry' => 1];
            }
        }

        return $fields;
    }

    private function changeComponentFields($fields)
    {
        foreach ($fields as $key => $data) {
            $data = $data;
            if ($key == 'postcode') {
                $fields[$key]['component'] = 'O2TI_FullCheckout/js/view/form/element/postcode';
                $fields[$key]['config']['elementTmpl'] = 'O2TI_FullCheckout/form/element/number';
            } elseif ($key == 'vat_id') {
                $fields[$key]['component'] = 'O2TI_FullCheckout/js/view/form/element/vatid';
                $fields[$key]['config']['elementTmpl'] = 'O2TI_FullCheckout/form/element/number';
            } elseif ($key == 'telephone') {
                $fields[$key]['component'] = 'O2TI_FullCheckout/js/view/form/element/telephone';
                $fields[$key]['config']['elementTmpl'] = 'O2TI_FullCheckout/form/element/number';
            }
        }

        return $fields;
    }

    private function getPlaceholderForField($key)
    {
        $placeholder = '';
        $arrFields = [
            'fax' => __('Mobile'),
        ];
        if (isset($arrFields[$key])) {
            $placeholder = $arrFields[$key];
        }

        return $placeholder;
    }

    private function createPlaceholders($fields)
    {
        foreach ($fields as $key => $data) {
            if ((!isset($data['placeholder']) || !$data['placeholder'])) {
                $placeholder = isset($data['label']) && $data['label'] ?
                    $data['label'] : $this->getPlaceholderForField($key);
                if ($placeholder) {
                    if (isset($data['type']) && $data['type'] === 'group'
                        && isset($data['children']) && !empty($data['children'])
                    ) {
                        foreach ($data['children'] as $childrenKey => $childrenData) {
                            if (!isset($data['placeholder']) || !$data['placeholder']) {
                                if (isset($fields[$key]['children'][$childrenKey]['additionalClasses']) &&
                                    $fields[$key]['children'][$childrenKey]['additionalClasses'] === true
                                ) {
                                    $fields[$key]['children'][$childrenKey]['additionalClasses'] = 'additional';
                                }
                                $fields[$key]['children'][$childrenKey]['placeholder'] = $placeholder;
                            }
                            $childrenData = $childrenData;
                        }
                    } else {
                        $fields[$key]['placeholder'] = $placeholder;
                    }
                }
            }
        }

        return $fields;
    }

    private function createMaskFields($fields)
    {
        foreach ($fields as $key => $data) {
            if ((!isset($data['mask']) || !$data['mask'])) {
                $configPath = sprintf(self::CONFIG_PATH_FIELD_MASK_PATH, $key);

                $mask = $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);

                if ($mask) {
                    if (isset($data['type']) && $data['type'] === 'group'
                        && isset($data['children']) && !empty($data['children'])
                    ) {
                        foreach ($data['children'] as $childrenKey => $childrenData) {
                            if (!isset($data['mask']) || !$data['mask']) {
                                $fields[$key]['children'][$childrenKey]['mask'] = $mask;
                            }
                            $childrenData = $childrenData;
                        }
                    } else {
                        $fields[$key]['mask'] = $mask;
                    }
                }
            }
        }

        return $fields;
    }

    private function changeBillingFields($jsLayout)
    {
        $fieldOrder = $this->getFieldOrder();

        foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'] as &$payment) {
            if (isset($payment['children']['form-fields'])) {
                $billingFields = &$payment['children']['form-fields']['children'];
                $billingFields = $this->changeStreetStructure($billingFields);
                $billingFields = $this->createMaskFields($billingFields);
                $billingFields = $this->changeComponentFields($billingFields);
                $billingFields = $this->createValidationFields($billingFields);
                $billingFields = $this->createTooltipFields($billingFields);
                $billingFields = $this->createPlaceholders($billingFields);

                foreach ($billingFields as $fieldName => $billingField) {
                    $billingField = $billingField;
                    if (isset($fieldOrder[$fieldName])) {
                        $billingFields[$fieldName]['sortOrder'] = $fieldOrder[$fieldName];
                    }
                }
            }
        }
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->changeStreetStructure($billingAddressOnPage);
            $billingAddressOnPage = $this->createMaskFields($billingAddressOnPage);
            $billingAddressOnPage = $this->changeComponentFields($billingAddressOnPage);
            $billingAddressOnPage = $this->createValidationFields($billingAddressOnPage);
            $billingAddressOnPage = $this->createTooltipFields($billingAddressOnPage);
            $billingAddressOnPage = $this->createPlaceholders($billingAddressOnPage);

            foreach ($billingAddressOnPage as $fieldName => $billingField) {
                $billingField = $billingField;
                if (isset($fieldOrder[$fieldName])) {
                    $billingAddressOnPage[$fieldName]['sortOrder'] = $fieldOrder[$fieldName];
                }
            }
        }

        return $jsLayout;
    }

    private function moveAddressBilling($jsLayout)
    {
        if ($this->scopeConfig->getValue('full_checkout/general/move_address_billing', ScopeInterface::SCOPE_STORE)) {
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
                $billingAddressForm = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'];
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['billingAddress']['children']['billing-address-form'] = $billingAddressForm;
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']);
            }
        }

        return $jsLayout;
    }

    private function disableDiscountComponent($jsLayout)
    {
        if ($this->scopeConfig->getValue('full_checkout/general/disable_discount', ScopeInterface::SCOPE_STORE)) {
            unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['discount']);
        }

        return $jsLayout;
    }

    public function aroundProcess(LayoutProcessor $layoutProcessor, callable $proceed, ...$args)
    {
        $jsLayout = $proceed(...$args);
        $jsLayout = $this->disableAuthentication($jsLayout);
        $jsLayout = $this->changeShippingFields($jsLayout);
        $jsLayout = $this->changeBillingFields($jsLayout);
        $jsLayout = $this->disableDiscountComponent($jsLayout);
        $jsLayout = $this->moveAddressBilling($jsLayout);
        $layoutProcessor = $layoutProcessor;

        return $jsLayout;
    }
}
