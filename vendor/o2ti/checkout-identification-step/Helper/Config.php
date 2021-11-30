<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\CheckoutIdentificationStep\Helper;

use Magento\Customer\Model\AccountManagement;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config - Helper configuration.
 */
class Config extends AbstractHelper
{
    public const CONFIG_PATH_GENERAL = 'checkout_identification_step/general/%s';

    public const CONFIG_PATH_UX_CUSTOMER = 'checkout_identification_step/general/ux/customer/%s';

    public const CONFIG_PATH_UX_NEW_CUSTOMER = 'checkout_identification_step/general/ux/new_customer/%s';

    public const CONFIG_PATH_UX_GUEST = 'checkout_identification_step/general/ux/guest/%s';

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
     * Get Configs Ux Customer.
     *
     * @param string $field
     *
     * @return string
     */
    public function getConfigUxCustomer(string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $configPath = sprintf(self::CONFIG_PATH_UX_CUSTOMER, $field);

        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get Configs Ux New Customer.
     *
     * @param string $field
     *
     * @return string
     */
    public function getConfigUxNewCustomer(string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $configPath = sprintf(self::CONFIG_PATH_UX_NEW_CUSTOMER, $field);

        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get Configs Ux for Customer or New Customer.
     *
     * @param string $field
     *
     * @return string
     */
    public function getConfigUxGuest(string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $configPath = sprintf(self::CONFIG_PATH_UX_GUEST, $field);

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
     * If logout visible is Enabled.
     *
     * @return bool
     */
    public function isLogoutVisible(): ?bool
    {
        return $this->getConfigUxCustomer('logout');
    }

    /**
     * If continue as Guest is Enabled.
     *
     * @return bool
     */
    public function isContiuneAsGuest(): ?bool
    {
        return $this->getConfigUxGuest('contiune_as_guest');
    }

    /**
     * If Sync Order as Guest is Enabled.
     *
     * @return bool
     */
    public function isSyncOrderAsGuest(): ?bool
    {
        return $this->getConfigUxCustomer('sync_order_as_guest');
    }

    /**
     * If Create Account is Enabled.
     *
     * @return bool
     */
    public function isCreateAccount(): ?bool
    {
        return $this->getConfigUxNewCustomer('create_account');
    }

    /**
     * If Create Account Address is Enabled.
     *
     * @return bool
     */
    public function isCreateAccountAddress(): ?bool
    {
        return $this->getConfigUxNewCustomer('create_account_address');
    }

    /**
     * Get minimum password length.
     *
     * @return string
     *
     * @since 100.1.0
     */
    public function getMinimumPasswordLength(): string
    {
        return $this->scopeConfig->getValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    /**
     * Get number of password required character classes.
     *
     * @return string
     *
     * @since 100.1.0
     */
    public function getRequiredCharacterClassesNumber(): string
    {
        return $this->scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }
}
