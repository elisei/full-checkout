<?php
/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace O2TI\FullCheckout\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Theme\Block\Html\Header\Logo;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    protected $_scopeConfig;


    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $_fileStorageHelper;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Logo $logo
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_logo = $logo;
    }

    public function getConfig()
    {
        return [
        	'move_address_billing' => $this->_scopeConfig->getValue('full_checkout/general/move_address_billing', ScopeInterface::SCOPE_STORE),
            'logo_src' => $this->_logo->getLogoSrc(),
            'logo_width' => $this->_logo->getLogoWidth(),
            'logo_height' => $this->_logo->getLogoHeight(),
            'logo_alt' => $this->_logo->getLogoAlt()
        ];
    }

}
