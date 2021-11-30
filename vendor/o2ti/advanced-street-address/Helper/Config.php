<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\AdvancedStreetAddress\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config - Helper configuration.
 */
class Config extends AbstractHelper
{
    public const CONFIG_PATH_GENERAL = 'advanced_street_address/general/%s';

    public const CONFIG_PATH_ARRAY_LABEL = 'advanced_street_address/general/street_%s/label/%s';

    public const CONFIG_PATH_ARRAY_VALIDATION = 'advanced_street_address/general/street_%s/validation/%s';

    /**
     * @var mapArrayName
     */
    private $mapArrayName = [
        0 => 'first',
        1 => 'second',
        2 => 'third',
        3 => 'fourth',
    ];

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
     * Get Configs For Label.
     *
     * @param int    $position
     * @param string $field
     *
     * @return string
     */
    public function getConfigForLabel(int $position, string $field): ?string
    {
        $arrayName = $this->mapArrayName[$position];
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $configPath = sprintf(self::CONFIG_PATH_ARRAY_LABEL, $arrayName, $field);

        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get Configs For Validation.
     *
     * @param int    $position
     * @param string $field
     *
     * @return int
     */
    public function getConfigForValidation(int $position, string $field): ?int
    {
        $arrayName = $this->mapArrayName[$position];
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $configPath = sprintf(self::CONFIG_PATH_ARRAY_VALIDATION, $arrayName, $field);

        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * If module is Enabled.
     *
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->getConfigModule('enabled');
    }
}
