<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\AdvancedFieldsCheckout\Helper;

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
    public const CONFIG_PATH_GENERAL = 'advanced_fields_checkout/general/%s';

    public const CONFIG_PATH_CLASSES = 'advanced_fields_checkout/general/columns/classes';

    public const CONFIG_PATH_AUTOCOMPLETE = 'advanced_fields_checkout/general/autocomplete/autocomplete';

    public const CONFIG_PATH_PLACEHOLDER = 'advanced_fields_checkout/general/placeholder/placeholder';

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
     * Get Config Field to Classes.
     *
     * @return array
     */
    public function getConfigFieldForToClasses(): ?array
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $fields = $this->scopeConfig->getValue(self::CONFIG_PATH_CLASSES, ScopeInterface::SCOPE_STORE, $storeId);
        if (is_array($fields)) {
            return $fields;
        }

        return $this->json->unserialize($fields);
    }

    /**
     * Get Addtional Classes for Field.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getAddtionalClassesForField(string $fieldName): ?string
    {
        $fieldsToSort = $this->getConfigFieldForToClasses();
        foreach ($fieldsToSort as $key => $fields) {
            if (is_array($fields) && $fields['field'] == $fieldName) {
                $classSize = $fieldsToSort[$key]['size'];
                $classBreakLine = $fieldsToSort[$key]['is_break_line'] ? 'break-line' : 'parent-field';

                return $classSize.' '.$classBreakLine;
            }
        }

        return '';
    }

    /**
     * Get Config Field to Autocomplete.
     *
     * @return array
     */
    public function getConfigFieldForToAutocomplete(): ?array
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $fields = $this->scopeConfig->getValue(self::CONFIG_PATH_AUTOCOMPLETE, ScopeInterface::SCOPE_STORE, $storeId);
        if (is_array($fields)) {
            return $fields;
        }

        return $this->json->unserialize($fields);
    }

    /**
     * Get Autocomplete for Field.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getAutocompleteForField(string $fieldName): ?string
    {
        $fieldsToSort = $this->getConfigFieldForToAutocomplete();
        foreach ($fieldsToSort as $key => $fields) {
            if (is_array($fields) && $fields['field'] == $fieldName) {
                return $fieldsToSort[$key]['autocomplete'];
            }
        }

        return '';
    }

    /**
     * Get Config Field to Placeholder.
     *
     * @return array
     */
    public function getConfigFieldForToPlaceholder(): ?array
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $fields = $this->scopeConfig->getValue(self::CONFIG_PATH_PLACEHOLDER, ScopeInterface::SCOPE_STORE, $storeId);
        if (is_array($fields)) {
            return $fields;
        }

        return $this->json->unserialize($fields);
    }

    /**
     * Get Placeholder for Field.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getPlaceholderForField(string $fieldName): ?string
    {
        $fieldsToSort = $this->getConfigFieldForToPlaceholder();
        foreach ($fieldsToSort as $key => $fields) {
            if (is_array($fields) && $fields['field'] == $fieldName) {
                return $fieldsToSort[$key]['placeholder'];
            }
        }

        return '';
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

    /**
     * Get If is enabled Classes.
     *
     * @return bool
     */
    public function isEnabledClasses(): ?bool
    {
        return $this->getConfigModule('columns/enabled');
    }

    /**
     * Get If is enabled Autocomplete.
     *
     * @return bool
     */
    public function isEnabledAutocomplete(): ?bool
    {
        return $this->getConfigModule('autocomplete/enabled');
    }

    /**
     * Get If is enabled Placeholder.
     *
     * @return bool
     */
    public function isEnabledPlaceholder(): ?bool
    {
        return $this->getConfigModule('placeholder/enabled');
    }
}
