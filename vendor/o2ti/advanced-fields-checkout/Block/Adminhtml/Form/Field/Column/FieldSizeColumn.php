<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace O2TI\AdvancedFieldsCheckout\Block\Adminhtml\Form\Field\Column;

use Magento\Framework\View\Element\Html\Select;

/**
 * Class FieldSizeColumn - Create Field to Size Column.
 */
class FieldSizeColumn extends Select
{
    public const A_HUNDRED_PERCENT = 'a-hundred-percent';

    public const SIXTY_PERCENT = 'sixty-percent';

    public const FIFTY_PERCENT = 'fifty-percent';

    public const THIRTY_THREE_PERCENT = 'thirty-three-percent';

    public const TWENTY_PERCENT = 'twenty-percent';

    /**
     * Set "name" for <select> element.
     *
     * @param string $value
     *
     * @return void
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element.
     *
     * @param string $value
     *
     * @return void
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML.
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        return parent::_toHtml();
    }

    /**
     * Get Options.
     *
     * @return array
     */
    public function getSourceOptions(): array
    {
        return [
            ['value' => self::A_HUNDRED_PERCENT, 'label' => '100%'],
            ['value' => self::SIXTY_PERCENT, 'label' => '70%'],
            ['value' => self::FIFTY_PERCENT, 'label' => '50%'],
            ['value' => self::THIRTY_THREE_PERCENT, 'label' => '33%'],
            ['value' => self::TWENTY_PERCENT, 'label' => '20%'],
        ];
    }
}
