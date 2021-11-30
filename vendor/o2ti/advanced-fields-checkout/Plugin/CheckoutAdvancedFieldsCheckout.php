<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\AdvancedFieldsCheckout\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use O2TI\AdvancedFieldsCheckout\Helper\Config;

/**
 *  CheckoutAdvancedFieldsCheckout - Change Components.
 */
class CheckoutAdvancedFieldsCheckout
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManagerInterface
     * @param Config                $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManagerInterface,
        Config $config
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->config = $config;
    }

    /**
     * Change Components in Create Account.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function changeCreateAccount(array $jsLayout): array
    {
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'])) {
            // phpcs:ignore
            $createAccountFields = &$jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'];
            if ($this->config->isEnabledClasses()) {
                $createAccountFields = $this->changeClassesFields($createAccountFields);
            }
            if ($this->config->isEnabledAutocomplete()) {
                $createAccountFields = $this->setAutocompleteFields($createAccountFields);
            }
            if ($this->config->isEnabledPlaceholder()) {
                $createAccountFields = $this->setPlaceholderFields($createAccountFields);
            }
        }

        return $jsLayout;
    }

    /**
     * Change Components in Shipping.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function changeShippingFields(array $jsLayout): array
    {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step'])) {
            // phpcs:ignore
            $shippingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
            if ($this->config->isEnabledClasses()) {
                $shippingFields = $this->changeClassesFields($shippingFields);
            }
            if ($this->config->isEnabledAutocomplete()) {
                $shippingFields = $this->setAutocompleteFields($shippingFields);
            }
            if ($this->config->isEnabledPlaceholder()) {
                $shippingFields = $this->setPlaceholderFields($shippingFields);
            }
        }

        return $jsLayout;
    }

    /**
     * Change Components in Billing.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function changeBillingFields(array $jsLayout): array
    {
        // phpcs:ignore
        foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'] as &$payment) {
            if (isset($payment['children']['form-fields'])) {
                $billingFields = &$payment['children']['form-fields']['children'];
                if ($this->config->isEnabledClasses()) {
                    $billingFields = $this->changeClassesFields($billingFields);
                }
                if ($this->config->isEnabledAutocomplete()) {
                    $billingFields = $this->setAutocompleteFields($billingFields);
                }
                if ($this->config->isEnabledPlaceholder()) {
                    $billingFields = $this->setPlaceholderFields($billingFields);
                }
            }
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            if ($this->config->isEnabledClasses()) {
                $billingAddressOnPage = $this->changeClassesFields($billingAddressOnPage);
            }
            if ($this->config->isEnabledAutocomplete()) {
                $billingAddressOnPage = $this->setAutocompleteFields($billingAddressOnPage);
            }
            if ($this->config->isEnabledPlaceholder()) {
                $billingAddressOnPage = $this->setPlaceholderFields($billingAddressOnPage);
            }
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form'])) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            if ($this->config->isEnabledClasses()) {
                $billingAddressOnPage = $this->changeClassesFields($billingAddressOnPage);
            }
            if ($this->config->isEnabledAutocomplete()) {
                $billingAddressOnPage = $this->setAutocompleteFields($billingAddressOnPage);
            }
            if ($this->config->isEnabledPlaceholder()) {
                $billingAddressOnPage = $this->setPlaceholderFields($billingAddressOnPage);
            }
        }

        return $jsLayout;
    }

    /**
     * Change Classes in Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function changeClassesFields(array $fields): array
    {
        foreach ($fields as $key => $data) {
            $oldClass = null;
            if (in_array('config', $fields[$key])) {
                if ($fields[$key]['config']) {
                    $newClass = $this->config->getAddtionalClassesForField($key);
                    if ($newClass) {
                        if (in_array('additionalClasses', $fields[$key]['config'])) {
                            $oldClass = $fields[$key]['config']['additionalClasses'];
                        }
                        $fields[$key]['config']['additionalClasses'] = $oldClass.' '.$newClass;
                    }
                    if ($key === 'street') {
                        $fields = $this->setClassesToStreet($fields, $key);
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Set Components Classes in Street Lines.
     *
     * @param array  $fields
     * @param string $field
     *
     * @return array
     */
    public function setClassesToStreet(array $fields, string $field): array
    {
        $oldClass = null;
        foreach ($fields[$field]['children'] as $arrayPosition => $streetLine) {
            $streetKey = 'street_'.$arrayPosition;
            $newClass = $this->config->getAddtionalClassesForField($streetKey);
            if ($newClass) {
                if (in_array('additionalClasses', $fields[$field]['children'][$arrayPosition]['config'])) {
                    $oldClass = $fields[$field]['children'][$arrayPosition]['config']['additionalClasses'];
                }
                $fields[$field]['children'][$arrayPosition]['config']['additionalClasses'] = $oldClass.' '.$newClass;
            }
        }

        return $fields;
    }

    /**
     * Set Components Autocomplete in Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function setAutocompleteFields(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if (in_array('config', $fields[$key])) {
                if ($fields[$key]['config']) {
                    $autocomplete = $this->config->getAutocompleteForField($key);
                    if ($autocomplete) {
                        $fields[$key]['config']['autocomplete'] = $autocomplete;
                        if ($fields[$key]['config']['elementTmpl'] === 'ui/form/element/input') {
                            $fields[$key]['config']['elementTmpl'] = 'O2TI_AdvancedFieldsCheckout/form/element/input';
                        }
                        if ($fields[$key]['config']['elementTmpl'] === 'ui/form/element/select') {
                            $fields[$key]['config']['elementTmpl'] = 'O2TI_AdvancedFieldsCheckout/form/element/select';
                        }
                        if ($fields[$key]['config']['elementTmpl'] === 'ui/form/element/password') {
                            // phpcs:ignore
                            $fields[$key]['config']['elementTmpl'] = 'O2TI_AdvancedFieldsCheckout/form/element/password';
                        }
                        // phpcs:ignore
                        if ($fields[$key]['config']['elementTmpl'] === 'O2TI_CheckoutIdentificationStep/form/element/password') {
                            // phpcs:ignore
                            $fields[$key]['config']['elementTmpl'] = 'O2TI_AdvancedFieldsCheckout/form/element/O2TI/password';
                        }
                    }
                    if ($key === 'street') {
                        $fields = $this->setAutocompleteToStreet($fields, $key);
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Set Components Autocomplete in Street Lines.
     *
     * @param array  $fields
     * @param string $field
     *
     * @return array
     */
    public function setAutocompleteToStreet(array $fields, string $field): array
    {
        foreach ($fields[$field]['children'] as $arrayPosition => $streetLine) {
            $streetKey = 'street_'.$arrayPosition;
            $autocomplete = $this->config->getAutocompleteForField($streetKey);
            if ($autocomplete) {
                if ($fields[$field]['children'][$arrayPosition]['config']['elementTmpl'] === 'ui/form/element/input') {
                    // phpcs:ignore
                    $fields[$field]['children'][$arrayPosition]['config']['elementTmpl'] = 'O2TI_AdvancedFieldsCheckout/form/element/input';
                }
                // phpcs:ignore
                if ($fields[$field]['children'][$arrayPosition]['config']['elementTmpl'] === 'O2TI_AdvancedStreetAddress/form/element/number') {
                    // phpcs:ignore
                    $fields[$field]['children'][$arrayPosition]['config']['elementTmpl'] = 'O2TI_AdvancedFieldsCheckout/form/element/number';
                }

                $fields[$field]['children'][$arrayPosition]['config']['autocomplete'] = $autocomplete;
            }
        }

        return $fields;
    }

    /**
     * Set Components Placeholder in Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function setPlaceholderFields(array $fields): array
    {
        foreach ($fields as $key => $data) {
            $oldClass = null;
            if (in_array('label', $fields[$key])) {
                if ($fields[$key]['label']) {
                    $placeholder = $this->config->getPlaceholderForField($key);
                    $label = $fields[$key]['label'];
                    $fields[$key]['config']['placeholder'] = __($label);
                    if ($placeholder) {
                        $fields[$key]['config']['placeholder'] = __($placeholder);
                    }
                    if ($key === 'street') {
                        $fields = $this->setPlaceholderToStreet($fields, $key);
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Set Components Placeholder in Street Lines.
     *
     * @param array  $fields
     * @param string $field
     *
     * @return array
     */
    public function setPlaceholderToStreet(array $fields, string $field): array
    {
        foreach ($fields[$field]['children'] as $arrayPosition => $streetLine) {
            $streetKey = 'street_'.$arrayPosition;
            $placeholder = $this->config->getPlaceholderForField($streetKey);
            $label = $fields[$field]['children'][$arrayPosition]['label'];
            $fields[$field]['children'][$arrayPosition]['config']['placeholder'] = __($label);
            if ($placeholder) {
                $fields[$field]['children'][$arrayPosition]['config']['placeholder'] = __($placeholder);
            }
        }

        return $fields;
    }

    /**
     * Select Components for Change.
     *
     * @param LayoutProcessor $layoutProcessor
     * @param callable        $proceed
     * @param array           $args
     *
     * @return array
     */
    public function aroundProcess(LayoutProcessor $layoutProcessor, callable $proceed, array $args): array
    {
        $jsLayout = $proceed($args);
        if ($this->config->isEnabled()) {
            $jsLayout = $this->changeCreateAccount($jsLayout);
            $jsLayout = $this->changeShippingFields($jsLayout);
            $jsLayout = $this->changeBillingFields($jsLayout);
            $layoutProcessor = $layoutProcessor;
        }

        return $jsLayout;
    }
}
