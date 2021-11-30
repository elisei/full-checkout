<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\AdvancedFieldsCheckout\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use O2TI\AdvancedFieldsCheckout\Block\Adminhtml\Form\Field\Column\FieldColumn;
use O2TI\AdvancedFieldsCheckout\Block\Adminhtml\Form\Field\Column\FieldIsBreakLineColumn;
use O2TI\AdvancedFieldsCheckout\Block\Adminhtml\Form\Field\Column\FieldSizeColumn;

/**
 * Class AddClasses - Insert Class to Field.
 */
class AddClasses extends AbstractFieldArray
{
    /**
     * @var FieldColumn
     */
    private $fieldRenderer;

    /**
     * @var FieldSizeColumn
     */
    private $fieldSizeRenderer;

    /**
     * @var FieldIsBreakLineColumn
     */
    private $fieldIsBreakRenderer;

    /**
     * Prepare rendering the new field by adding all the needed columns.
     */
    protected function _prepareToRender()
    {
        $this->addColumn('field', [
            'label'    => __('Field'),
            'renderer' => $this->getFieldRenderer(),
        ]);

        $this->addColumn('size', [
            'label'    => __('Size'),
            'renderer' => $this->getFieldSizeRenderer(),
        ]);

        $this->addColumn('is_break_line', [
            'label'    => __('Break the line'),
            'renderer' => $this->getFieldIsBreakRenderer(),
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object.
     *
     * @param DataObject $row
     *
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $field = $row->getField();
        if ($field !== null) {
            $options['option_'.$this->getFieldRenderer()->calcOptionHash($field)] = 'selected="selected"';
            $options['option_'.$this->getFieldIsBreakRenderer()->calcOptionHash($field)] = 'selected="selected"';
            $options['option_'.$this->getFieldSizeRenderer()->calcOptionHash($field)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Create Block FieldColumn.
     *
     * @throws LocalizedException
     *
     * @return FieldColumn
     */
    private function getFieldRenderer()
    {
        if (!$this->fieldRenderer) {
            $this->fieldRenderer = $this->getLayout()->createBlock(
                FieldColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->fieldRenderer;
    }

    /**
     * Create Block FieldSizeColumn.
     *
     * @throws LocalizedException
     *
     * @return FieldSizeColumn
     */
    private function getFieldSizeRenderer()
    {
        if (!$this->fieldSizeRenderer) {
            $this->fieldSizeRenderer = $this->getLayout()->createBlock(
                FieldSizeColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->fieldSizeRenderer;
    }

    /**
     * Create Block FieldIsBreakLine.
     *
     * @throws LocalizedException
     *
     * @return FieldIsBreakLine
     */
    private function getFieldIsBreakRenderer()
    {
        if (!$this->fieldIsBreakRenderer) {
            $this->fieldIsBreakRenderer = $this->getLayout()->createBlock(
                FieldIsBreakLineColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->fieldIsBreakRenderer;
    }
}
