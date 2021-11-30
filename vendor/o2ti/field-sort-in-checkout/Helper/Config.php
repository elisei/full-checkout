<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\FieldSortInCheckout\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config - Helper configuration.
 */
class Config extends AbstractHelper
{
    public const CONFIG_PATH_GENERAL = 'field_sort_in_checkout/general/%s';

    public const CONFIG_PATH_ADDRESS_TO_SORT = 'field_sort_in_checkout/general/address/to_sort';

    public const CONFIG_PATH_CUSTOMER_TO_SORT = 'field_sort_in_checkout/general/customer/to_sort';

    /**
     * @var Json
     */
    protected $json;

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
     * @param Json                  $json
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManagerInterface,
        Json $json
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->json = $json;
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
     * Get Configs Field For to Sort Address.
     *
     * @return array
     */
    public function getConfigFieldForToSortAddress(): ?array
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $fields = $this->scopeConfig->getValue(
            self::CONFIG_PATH_ADDRESS_TO_SORT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (is_array($fields)) {
            return $fields;
        }

        return $this->json->unserialize($fields);
    }

    /**
     * Get Sort Order by Field Address.
     *
     * @param string $fieldName
     *
     * @return int
     */
    public function getSortOrderByFieldAddress(string $fieldName): ?int
    {
        $fieldsToSort = $this->getConfigFieldForToSortAddress();
        foreach ($fieldsToSort as $key => $fields) {
            if (is_array($fields) && $fields['field'] == $fieldName) {
                return $fieldsToSort[$key]['sort_order'];
            }
        }

        return 0;
    }

    /**
     * Get Configs Field For to Sort Customer.
     *
     * @return array
     */
    public function getConfigFieldForToSortCustomer(): ?array
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $fields = $this->scopeConfig->getValue(
            self::CONFIG_PATH_CUSTOMER_TO_SORT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (is_array($fields)) {
            return $fields;
        }

        return $this->json->unserialize($fields);
    }

    /**
     * Get Sort Order by Field Customer.
     *
     * @param string $fieldName
     *
     * @return int
     */
    public function getSortOrderByFieldCustomer(string $fieldName): ?int
    {
        $fieldsToSort = $this->getConfigFieldForToSortCustomer();
        foreach ($fieldsToSort as $key => $fields) {
            if (is_array($fields) && $fields['field'] == $fieldName) {
                return $fieldsToSort[$key]['sort_order'];
            }
        }

        return 0;
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
