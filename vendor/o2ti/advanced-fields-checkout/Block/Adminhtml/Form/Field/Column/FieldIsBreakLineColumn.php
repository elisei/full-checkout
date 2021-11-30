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
 * Class FieldIsBreakLineColumn - Create Field to BreakLine.
 */
class FieldIsBreakLineColumn extends Select
{
    /**
     * Value which equal Enable for Enabledisable dropdown.
     */
    public const ENABLE_VALUE = 1;

    /**
     * Value which equal Disable for Enabledisable dropdown.
     */
    public const DISABLE_VALUE = 0;

    /**
     * Set "name" for <select> element.
     *
     * @param string $value
     *
     * @return $this
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
     * @return $this
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
     * Render Options.
     *
     * @return array
     */
    private function getSourceOptions(): array
    {
        return [
            ['value' => self::ENABLE_VALUE, 'label' => __('Enable')],
            ['value' => self::DISABLE_VALUE, 'label' => __('Disable')],
        ];
    }
}
