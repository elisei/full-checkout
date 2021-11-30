<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\AdvancedStreetAddress\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use O2TI\AdvancedStreetAddress\Helper\Config;

/**
 *  CheckoutAdvancedStreetAddress - Change Components.
 */
class CheckoutAdvancedStreetAddress
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
    public function changeCreateAccount(array $jsLayout): ?array
    {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['identification-step'])) {
            // phpcs:ignore
            $createAccountFields = &$jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'];
            $createAccountFields = $this->changeComponentFields($createAccountFields);
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
    public function changeShippingFields(array $jsLayout): ?array
    {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step'])) {
            // phpcs:ignore
            $shippingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
            $shippingFields = $this->changeComponentFields($shippingFields);
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
                $billingFields = $this->changeComponentFields($billingFields);
            }
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->changeComponentFields($billingAddressOnPage);
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form'])) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->changeComponentFields($billingAddressOnPage);
        }

        return $jsLayout;
    }

    /**
     * Change Components to Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function changeComponentFields(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ($key == 'street') {
                $defaultPosition = (int) $fields[$key]['sortOrder'];

                $fields[$key]['config']['template'] = 'O2TI_AdvancedStreetAddress/form/element/addressline';
                $fields[$key]['config']['fieldTemplate'] = 'O2TI_AdvancedStreetAddress/form/field';

                foreach ($fields[$key]['children'] as $arrayPosition => $streetLine) {
                    $fields[$key]['children'][$arrayPosition]['sortOrder'] = $defaultPosition + $arrayPosition;

                    if ($this->config->getConfigForLabel($arrayPosition, 'use_label')) {
                        $labelStreet = $this->config->getConfigForLabel($arrayPosition, 'label');
                        $fields[$key]['children'][$arrayPosition]['label'] = __($labelStreet);
                    }

                    if ($isRequired = $this->config->getConfigForValidation($arrayPosition, 'is_number')) {
                        // phpcs:ignore
                        if ($fields[$key]['children'][$arrayPosition]['config']['elementTmpl'] === 'ui/form/element/input') {
                            // phpcs:ignore
                            $fields[$key]['children'][$arrayPosition]['config']['elementTmpl'] = 'O2TI_AdvancedStreetAddress/form/element/number';
                        // phpcs:ignore
                        } elseif ($fields[$key]['children'][$arrayPosition]['config']['elementTmpl'] === 'O2TI_AdvancedFieldsCheckout/form/element/input') {
                            $fields[$key]['children'][$arrayPosition]['config']['elementTmpl']
                                = 'O2TI_AdvancedStreetAddress/form/element/O2TI/AdvancedStreetAddress/number';
                        }
                    }

                    $isRequired = $this->config->getConfigForValidation($arrayPosition, 'is_required');
                    $maxLength = $this->config->getConfigForValidation($arrayPosition, 'max_length');
                    $fields[$key]['children'][$arrayPosition]['validation'] = [
                        'min_text_length' => 1,
                        'max_text_length' => $maxLength,
                    ];
                    if ($isRequired) {
                        $fields[$key]['children'][$arrayPosition]['validation'] = [
                            'required-entry'  => $isRequired,
                            'min_text_length' => 1,
                            'max_text_length' => $maxLength,
                        ];
                    }
                }
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
