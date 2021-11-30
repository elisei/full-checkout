<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\CheckoutIdentificationStep\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptchaCheckout\Block\LayoutProcessor\Checkout\Onepage;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;
use O2TI\CheckoutIdentificationStep\Helper\Config;

/**
 *  ReCaptchaOnepage - Insert ReCaptcha to Step Identification.
 */
class ReCaptchaOnepage
{
    /**
     * @var UiConfigResolverInterface
     */
    protected $captchaUiConfigResolver;

    /**
     * @var IsCaptchaEnabledInterface
     */
    protected $isCaptchaEnabled;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param ScopeConfigInterface      $scopeConfig
     * @param UiConfigResolverInterface $captchaUiConfigResolver
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param Config                    $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UiConfigResolverInterface $captchaUiConfigResolver,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        Config $config
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->captchaUiConfigResolver = $captchaUiConfigResolver;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->config = $config;
    }

    /**
     * Select Components for Change.
     *
     * @param Onepage  $layoutProcessor
     * @param callable $proceed
     * @param array    $args
     *
     * @return array
     */
    public function aroundProcess(Onepage $layoutProcessor, callable $proceed, array $args): array
    {
        $jsLayout = $proceed($args);
        if ($this->config->isEnabled()) {
            $key = 'customer_login';
            if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)) {
                // phpcs:ignore
                $jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['recaptcha']['settings'] = $this->captchaUiConfigResolver->get($key);
            } else {
                // phpcs:ignore
                if (isset($jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['recaptcha'])) {
                    // phpcs:ignore
                    unset($jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['recaptcha']);
                }
            }
            $layoutProcessor = $layoutProcessor;
        }

        return $jsLayout;
    }
}
