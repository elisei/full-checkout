<?php
/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace O2TI\SocialLogin\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url\DecoderInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\Url\HostChecker;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use O2TI\SocialLogin\Provider\Provider;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * Query param name for last url visited.
     */
    const REFERER_QUERY_PARAM_NAME = 'referer';

    /**
     * Module is Enabled.
     */
    const CONFIG_PATH_SOCIAL_LOGIN_ENABLED = 'social_login/config/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var DecoderInterface
     */
    private $urlDecoder;

    /**
     * HostChecker.
     */
    private $hostChecker;

    /**
     * Construct.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface     $request
     * @param UrlInterface         $url
     * @param EncoderInterface     $urlEncoder
     * @param DecoderInterface     $urlDecoder
     * @param HostChecker          $hostChecker
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        UrlInterface $urlBuilder,
        EncoderInterface $urlEncoder,
        DecoderInterface $urlDecoder = null,
        HostChecker $hostChecker = null
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->urlEncoder = $urlEncoder;
        $this->urlDecoder = $urlDecoder ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(DecoderInterface::class);
        $this->hostChecker = $hostChecker ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(HostChecker::class);
    }

    /**
     * Enabled.
     *
     * @param string $provider
     *
     * @return bool
     */
    private function isEnabled($provider)
    {
        return (bool) $this->scopeConfig->getValue(
            sprintf(Provider::CONFIG_PATH_SOCIAL_LOGIN_PROVIDER_ENABLED, $provider),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Configs.
     *
     * @return array
     */
    public function getConfig()
    {
        $params = [];
        $referer = $this->getRequestReferrer();
        if ($referer) {
            $params = [
                self::REFERER_QUERY_PARAM_NAME => $referer,
            ];
        } else {
            $current = $this->urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
            $refererCurrent = $this->urlEncoder->encode($current);
            $params = [
                self::REFERER_QUERY_PARAM_NAME => $refererCurrent,
            ];
        }

        return [
            'socialLogin' => [
                'enabled' => (bool) $this->scopeConfig->getValue(
                    self::CONFIG_PATH_SOCIAL_LOGIN_ENABLED,
                    ScopeInterface::SCOPE_STORE
                ),
                'redirectUrl'           => $this->urlBuilder->getUrl('sociallogin/endpoint/index', $params),
                'providers'             => [
                    'facebook'      => $this->isEnabled('facebook'),
                    'google'        => $this->isEnabled('google'),
                    'WindowsLive'   => $this->isEnabled('WindowsLive'),
                ],
            ],
        ];
    }

    /**
     * Retrieve form posting url.
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getLoginPostUrl();
    }

    /**
     * Referrer.
     *
     * @return mixed|null
     */
    private function getRequestReferrer()
    {
        $referer = $this->request->getParam(self::REFERER_QUERY_PARAM_NAME);
        if ($referer && $this->hostChecker->isOwnOrigin($this->urlDecoder->decode($referer))) {
            return $referer;
        }

        return null;
    }
}
