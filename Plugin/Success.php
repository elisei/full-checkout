<?php
/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace O2TI\FullCheckout\Plugin;

/**
 * Class Success.
 */
class Success
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Success constructor.
     *
     * @param \Magento\Framework\Registry     $coreRegistry
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Checkout\Controller\Onepage\Success $subject
     */
    public function beforeExecute(\Magento\Checkout\Controller\Onepage\Success $subject)
    {
        $currentOrder = $this->_checkoutSession->getLastRealOrder();
        $this->_coreRegistry->register('current_order', $currentOrder);
    }
}
