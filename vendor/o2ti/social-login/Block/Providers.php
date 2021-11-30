<?php
/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace O2TI\SocialLogin\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url\DecoderInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\Url\HostChecker;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use O2TI\SocialLogin\Provider\Provider;

class Providers extends Template
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
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var DecoderInterface
     */
    private $urlDecoder;

    /**
     * @var HostChecker
     */
    private $hostChecker;

    /**
     * Construct.
     *
     * @param Context              $context
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface     $request
     * @param UrlInterface         $urlBuilder
     * @param EncoderInterface     $urlEncoder
     * @param DecoderInterface     $urlDecoder
     * @param HostChecker          $hostChecker
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        UrlInterface $urlBuilder,
        EncoderInterface $urlEncoder,
        DecoderInterface $urlDecoder = null,
        HostChecker $hostChecker = null,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->urlEncoder = $urlEncoder;
        $this->urlDecoder = $urlDecoder ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(DecoderInterface::class);
        $this->hostChecker = $hostChecker ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(HostChecker::class);
        parent::__construct($context, $data);
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
    public function getSocialConfig()
    {
        $params = [];
        $referer = $this->getRequestReferrer();
        if ($referer) {
            $params = [
                self::REFERER_QUERY_PARAM_NAME => $referer,
            ];
        } else {
            $current = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
            $refererCurrent = $this->urlEncoder->encode($current);
            $params = [
                self::REFERER_QUERY_PARAM_NAME => $refererCurrent,
            ];
        }

        return [
            'socialLogin' => [
                'social-login-url' => $this->getLoginPostUrlBase(),
                'enabled'          => (bool) $this->scopeConfig->getValue(
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

    /**
     * Retrieve customer login POST URL.
     *
     * @return string
     */
    public function getLoginPostUrlBase()
    {
        $params = [];
        $referer = $this->getRequestReferrer();
        if ($referer) {
            $params = [
                self::REFERER_QUERY_PARAM_NAME => $referer,
            ];
        }

        return $this->urlBuilder->getUrl('sociallogin/endpoint/index', $params);
    }
}
