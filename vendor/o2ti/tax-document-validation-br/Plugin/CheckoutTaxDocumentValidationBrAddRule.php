<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\TaxDocumentValidationBr\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use O2TI\TaxDocumentValidationBr\Helper\Config;

/**
 * Class CheckoutTaxDocumentValidationBrAddRule - Change componentes for validation atribute.
 */
class CheckoutTaxDocumentValidationBrAddRule
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
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'])
        ) {
            // phpcs:ignore
            $createAccountFields = &$jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'];
            $createAccountFields = $this->addRuleValidation($createAccountFields);
            $createAccountFields = $this->addTooltip($createAccountFields);
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
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'])
        ) {
            // phpcs:ignore
            $shippingFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
            $shippingFields = $this->addRuleValidation($shippingFields);
            $shippingFields = $this->addTooltip($shippingFields);
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
                if (isset($payment['children']['form-fields']['children'])) {
                    $billingFields = &$payment['children']['form-fields']['children'];
                    $billingFields = $this->addRuleValidation($billingFields);
                    $billingFields = $this->addTooltip($billingFields);
                }
            }
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])
        ) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->addRuleValidation($billingAddressOnPage);
            $billingAddressOnPage = $this->addTooltip($billingAddressOnPage);
        }
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form'])
        ) {
            // phpcs:ignore
            $billingAddressOnPage = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['billing-address-form']['children']['form-fields']['children'];
            $billingAddressOnPage = $this->addRuleValidation($billingAddressOnPage);
            $billingAddressOnPage = $this->addTooltip($billingAddressOnPage);
        }

        return $jsLayout;
    }

    /**
     * Change Rule Validation.
     *
     * @param array $fields
     *
     * @return array
     */
    public function addRuleValidation(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ($key === 'vat_id') {
                if ($this->config->getConfigByVatId('enabled_cpf') && $this->config->getConfigByVatId('enabled_cnpj')) {
                    $fields[$key]['validation'] = [
                        'required-entry'        => 1,
                        Config::VAT_CPF_OR_CNPJ => 1,
                    ];
                } elseif ($this->config->getConfigByVatId('enabled_cpf')) {
                    $fields[$key]['validation'] = [
                        'required-entry'     => 1,
                        Config::VAT_ONLY_CPF => 1,
                    ];
                } elseif ($this->config->getConfigByVatId('enabled_cnpj')) {
                    $fields[$key]['validation'] = [
                        'required-entry'      => 1,
                        Config::VAT_ONLY_CNPJ => 1,
                    ];
                }
            } elseif ($key === 'taxvat') {
                if ($this->config->getConfigByTaxvat('enabled_cpf')
                    && $this->config->getConfigByTaxvat('enabled_cnpj')
                ) {
                    $fields[$key]['validation'] = [
                        'required-entry'        => 1,
                        Config::VAT_CPF_OR_CNPJ => 1,
                    ];
                } elseif ($this->config->getConfigByTaxvat('enabled_cpf')) {
                    $fields[$key]['validation'] = [
                        'required-entry'     => 1,
                        Config::VAT_ONLY_CPF => 1,
                    ];
                } elseif ($this->config->getConfigByTaxvat('enabled_cnpj')) {
                    $fields[$key]['validation'] = [
                        'required-entry'      => 1,
                        Config::VAT_ONLY_CNPJ => 1,
                    ];
                }
            }
            continue;
        }

        return $fields;
    }

    /**
     * Add Tooltip.
     *
     * @param array $fields
     *
     * @return array
     */
    public function addTooltip(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ($key === 'vat_id') {
                if ($this->config->getConfigByVatId('enabled_cpf') && $this->config->getConfigByVatId('enabled_cnpj')) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CPF ou CNPJ é utilizado para envio e emissão de nota fiscal.'),
                    ];
                } elseif ($this->config->getConfigByVatId('enabled_cpf')) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CPF é utilizado para envio e emissão de nota fiscal.'),
                    ];
                } elseif ($this->config->getConfigByVatId('enabled_cnpj')) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CNPJ é utilizado para envio e emissão de nota fiscal.'),
                    ];
                }
            } elseif ($key === 'taxvat') {
                if ($this->config->getConfigByTaxvat('enabled_cpf')
                    && $this->config->getConfigByTaxvat('enabled_cnpj')
                ) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CPF ou CNPJ é utilizado para envio e emissão de nota fiscal.'),
                    ];
                } elseif ($this->config->getConfigByTaxvat('enabled_cpf')) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CPF é utilizado para envio e emissão de nota fiscal.'),
                    ];
                } elseif ($this->config->getConfigByTaxvat('enabled_cnpj')) {
                    $fields[$key]['config']['tooltip'] = [
                        'description' => __('O CNPJ é utilizado para envio e emissão de nota fiscal.'),
                    ];
                }
            }
            continue;
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
