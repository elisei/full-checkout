<?php
/**
 * Copyright © 2019 O2TI. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace O2TI\FullCheckout\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Footer extends Template
{
    public function getFooterContent()
    {
        $content = $this->_scopeConfig->getValue('full_checkout/general/footer_content', ScopeInterface::SCOPE_STORE);

        return $content;
    }
}
