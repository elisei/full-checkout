<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\AutoCompleteAddressBr\Block\Adminhtml\System\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Api - Defines Api call.
 */
class Api implements ArrayInterface
{
    /**
     * Returns Options.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            'ecorreios'           => __('E-Correios (Não oficial)'),
            'republicavirtual'    => __('Republica Virtual'),
            'viacep'              => __('Via Cep'),
        ];
    }
}
