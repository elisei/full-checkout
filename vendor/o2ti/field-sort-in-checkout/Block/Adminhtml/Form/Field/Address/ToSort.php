<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\FieldSortInCheckout\Block\Adminhtml\Form\Field\Address;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Address Fields To Sort - Set Field to Sort.
 */
class ToSort extends AbstractFieldArray
{
    /**
     * @var FieldColumn
     */
    private $fieldRenderer;

    /**
     * Prepare rendering the new field by adding all the needed columns.
     */
    protected function _prepareToRender()
    {
        $this->addColumn('field', [
            'label'    => __('Field'),
            'renderer' => $this->getFieldRenderer(),
        ]);
        $this->addColumn('sort_order', [
            'label' => __('Sort Order'),
            'class' => 'required-entry validate-digits validate-digits-range digits-range-1-1000',
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
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Render Options.
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
}
