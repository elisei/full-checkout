<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\AdvancedStreetAddress\Plugin;

use Magento\Customer\Block\Address\Edit;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use O2TI\AdvancedStreetAddress\Helper\Config;

/**
 *  CustomerAdvancedStreetAddress - Change Template.
 */
class CustomerAdvancedStreetAddress
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
     * Change Template.
     *
     * @param Edit   $subject
     * @param string $result
     *
     * @return string
     */
    public function afterGetTemplate(Edit $subject, string $result): string
    {
        if ($this->config->isEnabled()) {
            if ($this->config->getConfigModule('apply_in_account')) {
                return 'O2TI_AdvancedStreetAddress::account/address/edit.phtml';
            }
        }

        return $result;
    }
}
