<?php
/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace O2TI\SocialLogin\Controller\Endpoint;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Url\DecoderInterface;
use O2TI\SocialLogin\Provider\Provider;

class Index extends Action
{
    /**
     * @var DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var Provider
     */
    protected $provider;

    /**
     * Construct.
     *
     * @param Context          $context
     * @param Provider         $Provider
     * @param DecoderInterface $urlDecoder
     */
    public function __construct(
        Context $context,
        Provider $provider,
        DecoderInterface $urlDecoder
    ) {
        parent::__construct($context);
        $this->provider = $provider;
        $this->urlDecoder = $urlDecoder;
    }

    /**
     * Dispatch request.
     *
     * @return void
     */
    public function execute()
    {
        $provider = $this->_request->getParam('provider');
        $isSecure = $this->_request->isSecure();
        $referer = $this->_request->getParam('referer');

        $response = $this->provider->setAutenticateAndReferer($provider, $isSecure, $referer);

        return $this->_redirect($this->urlDecoder->decode($response['redirectUrl']));
    }
}
