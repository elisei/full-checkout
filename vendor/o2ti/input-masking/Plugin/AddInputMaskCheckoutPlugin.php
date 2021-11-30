<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace O2TI\InputMasking\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use O2TI\InputMasking\Helper\Config;

/**
 *  AddInputMaskCheckoutPlugin - Change Components.
 */
class AddInputMaskCheckoutPlugin
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
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'])) {
            // phpcs:ignore
            $createAccountFields = &$jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'];
            $createAccountFields = $this->createMaskToAddressFields($createAccountFields);
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
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step'])) {
            // phpcs:ignore
            $shippingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
            $shippingFields = $this->createMaskToAddressFields($shippingFields);
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
                $billingFields = $this->createMaskToAddressFields($billingFields);
                $billingFields = $this->changeComponentFields($billingFields);
            }
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->createMaskToAddressFields($billingAddressOnPage);
            $billingAddressOnPage = $this->changeComponentFields($billingAddressOnPage);
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form'])) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->createMaskToAddressFields($billingAddressOnPage);
            $billingAddressOnPage = $this->changeComponentFields($billingAddressOnPage);
        }

        return $jsLayout;
    }

    /**
     * Change Components Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function changeComponentFields(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ($key === 'postcode') {
                $defaultPosition = (int) $fields[$key]['sortOrder'];
                $fields[$key]['sortOrder'] = $defaultPosition;
                $fields[$key]['component'] = 'O2TI_InputMasking/js/view/form/element/postcode';
            } elseif ($key === 'vat_id' || $key === 'taxvat') {
                $defaultPosition = (int) $fields[$key]['sortOrder'];
                $fields[$key]['sortOrder'] = $defaultPosition;
                $fields[$key]['component'] = 'O2TI_InputMasking/js/view/form/element/fiscal_document';
            } elseif ($key === 'telephone') {
                $defaultPosition = (int) $fields[$key]['sortOrder'];
                $fields[$key]['sortOrder'] = $defaultPosition;
                $fields[$key]['component'] = 'O2TI_InputMasking/js/view/form/element/telephone';
            }
            continue;
        }

        return $fields;
    }

    /**
     * Change Components at Create Mask to Address Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function createMaskToAddressFields(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ((!isset($data['mask']) || !$data['mask'])) {
                $useMask = $this->config->getConfigAddressInput($key, 'enable_mask');

                if ($useMask) {
                    $mask = $this->config->getConfigAddressInput($key, 'mask');
                    $cleanIfNotMatch = $this->config->getConfigAddressInput($key, 'clean_if_not_match');
                    $fields = $this->insertElementsMask($fields, $data, $key, $mask, $cleanIfNotMatch);
                }
            }
        }

        return $fields;
    }

    /**
     * Change Components at Create Mask to Account Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function createMaskToAccountFields(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ((!isset($data['mask']) || !$data['mask'])) {
                $useMask = $this->config->getConfigCustomerInput($key, 'enable_mask');

                if ($useMask) {
                    $mask = $this->config->getConfigCustomerInput($key, 'mask');
                    $cleanIfNotMatch = $this->config->getConfigCustomerInput($key, 'clean_if_not_match');
                    $fields = $this->insertElementsMask($fields, $data, $key, $mask, $cleanIfNotMatch);
                }
            }
        }

        return $fields;
    }

    /**
     * Insert Mask to Field.
     *
     * @param array  $fields
     * @param array  $data
     * @param string $field
     * @param string $mask
     * @param string $cleanIfNotMatch
     *
     * @return array
     */
    public function insertElementsMask(
        array $fields,
        array $data,
        string $field,
        string $mask,
        string $cleanIfNotMatch
    ): array {
        if (isset($data['type']) && $data['type'] === 'group'
            && isset($data['children']) && !empty($data['children'])
        ) {
            foreach ($data['children'] as $childrenKey => $childrenData) {
                if (!isset($data['mask']) || !$data['mask']) {
                    $fields[$field]['children'][$childrenKey]['mask'] = $mask;
                    $fields[$field]['children'][$childrenKey]['maskEnable'] = 1;
                    $fields[$field]['children'][$childrenKey]['maskClearIfNotMatch'] = $cleanIfNotMatch;
                }
                $childrenData = $childrenData;
            }
        } else {
            $fields[$field]['mask'] = $mask;
            $fields[$field]['maskEnable'] = 1;
            $fields[$field]['maskClearIfNotMatch'] = $cleanIfNotMatch;
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
