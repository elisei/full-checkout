<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\AutoCompleteAddressBr\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config - Helper configuration.
 */
class Config extends AbstractHelper
{
    public const CONFIG_PATH_GENERAL = 'auto_complete_address_br/general/%s';

    public const CONFIG_PATH_RELATIONSHIP = 'auto_complete_address_br/general/relationship/%s';

    public const CONFIG_PATH_UX = 'auto_complete_address_br/general/ux/%s';

    public const CONFIG_PATH_DEVELOPER = 'auto_complete_address_br/general/developer/%s';

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
     * Get Config For Relation Ship.
     *
     * @param string $field
     *
     * @return string
     */
    public function getConfigForRelationShip(string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $configPath = sprintf(self::CONFIG_PATH_RELATIONSHIP, $field);

        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get Config For Ux.
     *
     * @param string $field
     *
     * @return string
     */
    public function getConfigForUx(string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $configPath = sprintf(self::CONFIG_PATH_UX, $field);

        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get Config For Developer.
     *
     * @param string $field
     *
     * @return string
     */
    public function getConfigForDeveloper(string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $configPath = sprintf(self::CONFIG_PATH_DEVELOPER, $field);

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

    /**
     * If use Input Masking.
     *
     * @return bool
     */
    public function useInputMasking(): ?bool
    {
        return $this->getConfigForUx('compatibility_o2ti_inputmasking');
    }

    /**
     * If Hide Taret Fields.
     *
     * @return bool
     */
    public function isHideTargetFields(): ?bool
    {
        return $this->getConfigForUx('hide_target_fields');
    }
}
