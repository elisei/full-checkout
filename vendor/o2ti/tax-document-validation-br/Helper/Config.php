<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\TaxDocumentValidationBr\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config - Get Configs Module.
 */
class Config extends AbstractHelper
{
    public const CONFIG_PATH_GENERAL = 'tax_document_validation_br/general/%s';

    public const CONFIG_PATH_VAT_ID = 'tax_document_validation_br/general/vat_id/%s';

    public const CONFIG_PATH_TAXVAT = 'tax_document_validation_br/general/taxvat/%s';

    public const VAT_ONLY_CPF = 'vatid-br-rule-only-cpf';

    public const VAT_ONLY_CNPJ = 'vatid-br-rule-only-cnpj';

    public const VAT_CPF_OR_CNPJ = 'vatid-br-rule-cpf-or-cnpj';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    /**
     * Get Configs Module.
     *
     * @param string $field
     *
     * @return string
     */
    public function getConfigModule(string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $path = str_replace('%s', $field, self::CONFIG_PATH_GENERAL);

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get Config By VatId.
     *
     * @param string $field
     *
     * @return string
     */
    public function getConfigByVatId(string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $path = str_replace('%s', $field, self::CONFIG_PATH_VAT_ID);

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get Config By Taxvat.
     *
     * @param string $field
     *
     * @return string
     */
    public function getConfigByTaxvat(string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $path = str_replace('%s', $field, self::CONFIG_PATH_TAXVAT);

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get If is Enabled Module.
     *
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->getConfigModule('enabled');
    }
}
