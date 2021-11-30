<?php
/**
 * Copyright © 2021 O2TI. All rights reserved.
 *
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

namespace O2TI\ThemeFullCheckout\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use O2TI\ThemeFullCheckout\Helper\Config;

/**
 *  ThemeFullCheckoutLayoutPlugin - Change Components.
 */
class ThemeFullCheckoutLayoutPlugin
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

            $createAccountFields = $this->changeFields($createAccountFields);
            // phpcs:ignore
            $jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['template'] = 'O2TI_ThemeFullCheckout/O2TI/CheckoutIdentificationStep/form/step/identification';
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
            $shippingFields = $this->changeFields($shippingFields);
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
                $billingFields = $this->changeFields($billingFields);
            }
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->changeFields($billingAddressOnPage);
        }

        return $jsLayout;
    }

    /**
     * Change Components in Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function changeFields(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ($key === 'street') {
                if (array_key_exists('fieldTemplate', $fields[$key]['config'])) {
                    if ($fields[$key]['config']['fieldTemplate'] === 'O2TI_AdvancedStreetAddress/form/field') {
                        $fields[$key]['config']['fieldTemplate'] = 'O2TI_ThemeFullCheckout/form/field';
                    }
                }
            }
            if ($key === 'password') {
                if (array_key_exists('elementTmpl', $fields[$key]['config'])) {
                    // phpcs:ignore
                    $fields[$key]['config']['elementTmpl'] = 'O2TI_ThemeFullCheckout/O2TI/CheckoutIdentificationStep/form/element/password';
                }
            }
        }

        return $fields;
    }

    /**
     * Move Components in Address Billing.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function moveAddressBilling(array $jsLayout): array
    {
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'];
            // phpcs:ignore
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form'] = $billingAddressOnPage;
            // phpcs:ignore
            unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']);
        }

        return $jsLayout;
    }

    /**
     * Move Components Discount.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function moveDiscountComponent(array $jsLayout): array
    {
        // phpcs:ignore
        $discount = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['discount'];
        // phpcs:ignore
        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['discount'] = $discount;
        // phpcs:ignore
        unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['discount']);

        return $jsLayout;
    }

    /**
     * Implement elements in sidebar.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function changeSidebar(array $jsLayout): array
    {
        $newSidebar['components']['checkout']['children']['sidebar']['children']['summary'] = [
            'children' => [
                'cart_items' => [
                    'sortOrder' => 10,
                ],
                'discount' => [
                    'sortOrder' => 20,
                    'component' => 'Magento_SalesRule/js/view/payment/discount',
                    'children'  => [
                        'errors' => [
                            'sortOrder'   => 0,
                            'component'   => 'Magento_SalesRule/js/view/payment/discount-messages',
                            'displayArea' => 'messages',
                        ],
                    ],
                ],
                'totals' => [
                    'config' => [
                        'sortOrder' => 30,
                    ],
                ],
            ],
        ];
        // phpcs:ignore
        if ($jsLayout['components']['checkout']['children']['sidebar']['config']['template'] === 'Magento_Checkout/sidebar') {
            // phpcs:ignore
            $jsLayout['components']['checkout']['children']['sidebar']['config']['template'] = 'O2TI_ThemeFullCheckout/sidebar';
        // phpcs:ignore
        } elseif ($jsLayout['components']['checkout']['children']['sidebar']['config']['template'] === 'O2TI_CheckoutIdentificationStep/sidebar') {
            // phpcs:ignore
            $jsLayout['components']['checkout']['children']['sidebar']['config']['template'] = 'O2TI_ThemeFullCheckout/O2TI/CheckoutIdentificationStep/sidebar';
            // phpcs:ignore
            unset($jsLayout['components']['checkout']['children']['sidebar']['children']['identification-information']['config']['template']);
            // phpcs:ignore
            $jsLayout['components']['checkout']['children']['sidebar']['children']['identification-information']['config']['template'] = 'O2TI_ThemeFullCheckout/O2TI/CheckoutIdentificationStep/identification-information';
        }

        return array_merge_recursive($jsLayout, $newSidebar);
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
            $jsLayout = $this->changeSidebar($jsLayout);
            $jsLayout = $this->moveDiscountComponent($jsLayout);
            if ($this->config->isMoveAddressBilling()) {
                $jsLayout = $this->moveAddressBilling($jsLayout);
            }
            $layoutProcessor = $layoutProcessor;
        }

        return $jsLayout;
    }
}
