<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\InputMasking\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config - Helper configuration.
 */
class Config extends AbstractHelper
{
    public const CONFIG_PATH_GENERAL = 'input_masking/general/%s';

    public const CONFIG_PATH_ADDRESS_INPUT = 'input_masking/general/address/%s/%s';

    public const CONFIG_PATH_CUSTOMER_INPUT = 'input_masking/general/customer/%s/%s';

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
        $configPath = sprintf(self::CONFIG_PATH_GENERAL, $field);

        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get Config Address Input.
     *
     * @param string $input
     * @param string $field
     *
     * @return string
     */
    public function getConfigAddressInput(string $input, string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $configPath = sprintf(self::CONFIG_PATH_ADDRESS_INPUT, $input, $field);

        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get Config Customer Input.
     *
     * @param string $input
     * @param string $field
     *
     * @return string
     */
    public function getConfigCustomerInput(string $input, string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $configPath = sprintf(self::CONFIG_PATH_CUSTOMER_INPUT, $input, $field);

        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
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
