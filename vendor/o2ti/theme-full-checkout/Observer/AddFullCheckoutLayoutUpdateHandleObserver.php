<?php
/**
 * Copyright © 2021 O2TI. All rights reserved.
 *
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace O2TI\ThemeFullCheckout\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout as Layout;
use Magento\Framework\View\Layout\ProcessorInterface as LayoutProcessor;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use O2TI\ThemeFullCheckout\Helper\Config;

/**
 *  AddFullCheckoutLayoutUpdateHandleObserver - Add Theme Full Checkout.
 */
class AddFullCheckoutLayoutUpdateHandleObserver implements ObserverInterface
{
    /**
     * Category Custom Layout.
     */
    public const LAYOUT_HANDLE_FULL_CHECKOUT = 'theme_full_checkout';

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
     * Add Handle to Checkout.
     *
     * @param EventObserver $observer
     *
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if ($this->config->isEnabled()) {
            /** @var Event $event */
            $event = $observer->getEvent();
            $actionName = $event->getData('full_action_name');

            if ($actionName === 'checkout_index_index') {
                /** @var Layout $layout */
                $layout = $event->getData('layout');

                /** @var LayoutProcessor $layoutUpdate */
                $layoutUpdate = $layout->getUpdate();
                $layoutUpdate->addHandle(static::LAYOUT_HANDLE_FULL_CHECKOUT);
            }
        }
    }
}
