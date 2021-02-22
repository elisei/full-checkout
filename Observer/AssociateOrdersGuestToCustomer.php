<?php
/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace O2TI\FullCheckout\Observer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderCustomerManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class AssociateOrdersGuestToCustomer implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderCustomerManagementInterface
     */
    protected $orderCustomerService;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $scopeConfig;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderCustomerManagementInterface $orderCustomerService,
        AccountManagementInterface $accountManagement,
        CustomerRepositoryInterface $customerRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderCustomerService = $orderCustomerService;
        $this->accountManagement = $accountManagement;
        $this->customerRepository = $customerRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Guest To Customer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        /* @var $order \Magento\Sales\Model\Order */
        $orderIds = $observer->getEvent()->getOrderIds();

        $order = $this->orderRepository->get($orderIds[0]);
        if ($orderIds && $order->getId() && $order->getCustomerIsGuest()) {
            if ($this->scopeConfig->getValue('full_checkout/general/associate_guest_to_customer')) {
                $customer = $this->customerRepository->get($order->getCustomerEmail());
                if ($customer->getId()) {
                    $order->setCustomerIsGuest(0);
                    $order->setCustomerId($customer->getId());
                    $order->setCustomerGroupId($customer->getGroupId());
                    $this->orderRepository->save($order);
                }
            }
        }
    }
}
