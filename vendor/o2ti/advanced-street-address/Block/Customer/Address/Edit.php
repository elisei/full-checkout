<?php
/**
 * Copyright © 2021 O2TI. All rights reserved.
 *
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace O2TI\AdvancedStreetAddress\Block\Customer\Address;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use O2TI\AdvancedStreetAddress\Helper\Config;

/**
 *  Edit - Change Template Edit Account.
 */
class Edit extends Template
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
     * Get if Use Label.
     *
     * @param int $position
     *
     * @return bool|null
     */
    public function getUseLabel(int $position): ?bool
    {
        return $this->config->getConfigForLabel($position, 'use_label');
    }

    /**
     * Get Label.
     *
     * @param int $position
     *
     * @return string|null
     */
    public function getLabel(int $position): ?string
    {
        return $this->config->getConfigForLabel($position, 'label');
    }

    /**
     * Get Is Required.
     *
     * @param int $position
     *
     * @return string
     */
    public function getIsRequired(int $position): ?string
    {
        if ($this->config->getConfigForValidation($position, 'is_required')) {
            return 'required-entry';
        }

        return '';
    }

    /**
     * Get Max Length.
     *
     * @param int $position
     *
     * @return int|null
     */
    public function getMaxLength($position): ?int
    {
        return $this->config->getConfigForValidation($position, 'max_length');
    }
}
