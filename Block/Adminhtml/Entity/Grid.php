<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Block\Adminhtml\Entity;

use Alekseon\AlekseonEav\Api\Data\AttributeInterface;

/**
 * Class Grid
 * @package Alekseon\AlekseonEav\Block\Adminhtml\Attribute
 */
abstract class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var array
     */
    private $columnAttributes = [];

    /**
     * @return void
     */
    protected function _construct() // @codingStandardsIgnoreLine
    {
        parent::_construct();
        $this->setId('entities_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->getCollection();
        $collection->addAttributeToSelect($this->columnAttributes);
        return parent::_prepareCollection();
    }

    /**
     * Return url of given row
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['entity_id' => $row->getEntityId()]);
    }

    /**
     * @throws \Exception
     */
    protected function addAttributeColumns()
    {
        $collection = $this->_collectionFactory->create();
        $resource = $collection->getResource();
        $resource->loadAllAttributes();
        $attributes = $resource->getAllLoadedAttributes();

        foreach ($attributes as $attribute) {
            if (!$attribute->getVisibleInGrid() || !$attribute->canDisplayInGrid()) {
                continue;
            }
            //$this->getCollection()->addAttributeToSelect($attribute->getAttributeCode());
            $this->addAttributeColumn($attribute);
        }
        return $this;
    }

    /**
     * @param $attribute
     * @throws \Exception
     */
    protected function addAttributeColumn(AttributeInterface $attribute)
    {
        $inputTypeModel = $attribute->getInputTypeModel();
        $columnConfig = [
            'type' => $inputTypeModel->getGridColumnType(),
            'header' => $attribute->getFrontendLabel(),
            'index' => $attribute->getAttributeCode(),
        ];

        $inputTypeModel->prepareGridColumnConfig($columnConfig);

        $this->columnAttributes[] = $attribute->getAttributeCode();
        $this->addColumn(
            $attribute->getAttributeCode(),
            $columnConfig
        );
    }
}
