<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\CheckoutIdentificationStep\Plugin;

use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 *  AddtionalCustomerData - Insert Addtional Data in Customer.
 */
class AddtionalCustomerData
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Add Session Data.
     *
     * @param Customer $subject
     * @param array    $result
     */
    public function afterGetSectionData(Customer $subject, array $result): array
    {
        if ($this->currentCustomer->getCustomerId()) {
            $customer = $this->currentCustomer->getCustomer();

            if (!in_array('email', $result)) {
                $result['email'] = $customer->getEmail();
            }

            if (!in_array('firstname', $result)) {
                $result['firstname'] = $customer->getFirstname();
            }
        }

        return $result;
    }
}
