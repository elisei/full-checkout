<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\FieldSortInCheckout\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use O2TI\FieldSortInCheckout\Helper\Config;

/**
 *  CheckoutFieldSortInCheckout - Change Components.
 */
class CheckoutFieldSortInCheckout
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
            $createAccountFields = $this->changeCreateAccountFieldSortOrder($createAccountFields);
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
            $shippingFields = $this->changeAddressFieldSortOrder($shippingFields);
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
                $billingFields = $this->changeAddressFieldSortOrder($billingFields);
            }
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->changeAddressFieldSortOrder($billingAddressOnPage);
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form'])) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->changeAddressFieldSortOrder($billingAddressOnPage);
        }

        return $jsLayout;
    }

    /**
     * Change Components at Address Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function changeAddressFieldSortOrder(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if (in_array('sortOrder', $fields[$key])) {
                if ($fields[$key]['sortOrder']) {
                    $newSortOrder = $this->config->getSortOrderByFieldAddress($key);
                    if ($newSortOrder) {
                        $fields[$key]['sortOrder'] = (int) $newSortOrder;
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Change Components at Customer Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function changeCreateAccountFieldSortOrder(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if (in_array('sortOrder', $fields[$key])) {
                if ($fields[$key]['sortOrder']) {
                    $newSortOrder = $this->config->getSortOrderByFieldCustomer($key);
                    if ($newSortOrder) {
                        $fields[$key]['sortOrder'] = (int) $newSortOrder;
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
