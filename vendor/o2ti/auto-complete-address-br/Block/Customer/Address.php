<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace O2TI\AutoCompleteAddressBr\Block\Customer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use O2TI\AutoCompleteAddressBr\Helper\Config;

/**
 *  Edit - Change Template Edit Account.
 */
class Address extends Template
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
     * @param Context               $context
     * @param Config                $config
     * @param array                 $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManagerInterface,
        Context $context,
        Config $config,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Get Configs Module.
     *
     * @param string $field
     *
     * @return string
     */
    public function getConfigForModule(string $field): ?string
    {
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $configPath = sprintf(Config::CONFIG_PATH_GENERAL, $field);

        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
