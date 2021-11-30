<?php
/**
 * Copyright © 2021 O2TI. All rights reserved.
 *
 * @author  Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE.txt for license details.
 */

namespace O2TI\ThemeFullCheckout\Plugin;

/**
 * Class Success - Checkout Page Success.
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
     * Set Current Order.
     *
     * @param \Magento\Checkout\Controller\Onepage\Success $subject
     *
     * @return void
     */
    public function beforeExecute()
    {
        $currentOrder = $this->_checkoutSession->getLastRealOrder();
        $this->_coreRegistry->register('current_order', $currentOrder);
    }
}
