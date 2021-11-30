<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\CheckoutIdentificationStep\Observer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderCustomerManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use O2TI\CheckoutIdentificationStep\Helper\Config as IdentificationConfig;

/**
 *  Model AssociateOrdersGuestToCustomer - Convert Guest To Customer.
 */
class AssociateOrdersGuestToCustomer implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var IdentificationConfig
     */
    private $identificationConfig;

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

    /**
     * @param OrderRepositoryInterface         $orderRepository
     * @param OrderCustomerManagementInterface $orderCustomerService
     * @param AccountManagementInterface       $accountManagement
     * @param CustomerRepositoryInterface      $customerRepository
     * @param ScopeConfigInterface             $scopeConfig
     * @param IdentificationConfig             $identificationConfig
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderCustomerManagementInterface $orderCustomerService,
        AccountManagementInterface $accountManagement,
        CustomerRepositoryInterface $customerRepository,
        ScopeConfigInterface $scopeConfig,
        IdentificationConfig $identificationConfig
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderCustomerService = $orderCustomerService;
        $this->accountManagement = $accountManagement;
        $this->customerRepository = $customerRepository;
        $this->scopeConfig = $scopeConfig;
        $this->identificationConfig = $identificationConfig;
    }

    /**
     * Convert Guest To Customer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        /* @var $order \Magento\Sales\Model\Order */
        $orderIds = $observer->getEvent()->getOrderIds();

        $order = $this->orderRepository->get($orderIds[0]);
        if ($orderIds && $order->getId() && $order->getCustomerIsGuest()) {
            if ($this->identificationConfig->isSyncOrderAsGuest()) {
                try {
                    $customer = $this->customerRepository->get($order->getCustomerEmail());
                    if ($customer->getId()) {
                        $order->setCustomerIsGuest(0);
                        $order->setCustomerId($customer->getId());
                        $order->setCustomerGroupId($customer->getGroupId());
                        $this->orderRepository->save($order);
                    }
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $exception = $e;
                }
            }
        }
    }
}
