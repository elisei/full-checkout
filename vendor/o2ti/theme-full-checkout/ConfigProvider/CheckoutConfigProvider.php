<?php
/**
 * Copyright © 2021 O2TI. All rights reserved.
 *
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

namespace O2TI\ThemeFullCheckout\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Theme\Block\Html\Header\Logo;
use O2TI\ThemeFullCheckout\Helper\Config;

/**
 * Checkout Config Provider Full Checkout Compoments.
 */
class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Logo
     */
    private $logo;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Logo                 $logo
     * @param Config               $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Logo $logo,
        Config $config
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->logo = $logo;
        $this->config = $config;
    }

    /**
     * Get Config to Checkout Config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'theme_full_checkout_enable'    => $this->config->isEnabled(),
            'move_address_billing'          => $this->config->isMoveAddressBilling(),
            'logo_src'                      => $this->logo->getLogoSrc(),
            'logo_width'                    => $this->logo->getLogoWidth(),
            'logo_height'                   => $this->logo->getLogoHeight(),
            'logo_alt'                      => $this->logo->getLogoAlt(),
        ];
    }
}
