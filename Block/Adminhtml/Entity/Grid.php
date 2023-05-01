<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

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
    private $attributesToAddToCollection = [];
    /**
     * @var bool
     */
    private $isCollectionPrepared = false;
    /**
     * @var bool
     */
    private $allowedPrepareCollection = true;

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
        if (!empty($this->attributesToAddToCollection)) {
            $collection = $this->getCollection();
            if (!$collection->isLoaded()) {
                $collection->addAttributeToSelect($this->attributesToAddToCollection);
            }
            $this->attributesToAddToCollection = [];
        }
        if (!$this->isCollectionPrepared && $this->allowedPrepareCollection) {
            $this->isCollectionPrepared = true;
            parent::_prepareCollection();
        }
        return $this;
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
        $this->allowedPrepareCollection = false;
        $this->_prepareCollection();
        $this->allowedPrepareCollection = true;
        $collection = $this->getCollection();
        $resource = $collection->getResource();
        $resource->loadAllAttributes();
        $attributes = $resource->getAllLoadedAttributes();

        foreach ($attributes as $attribute) {
            if (!$attribute->getVisibleInGrid() || !$attribute->canDisplayInGrid()) {
                continue;
            }

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
            'header' => $attribute->getDefaultFrontendLabel(),
            'index' => $attribute->getAttributeCode(),
        ];

        $inputTypeModel->prepareGridColumnConfig($columnConfig);
        if (isset($columnConfig['options'])) {
            $optionNone = ['' => ' '];
            $columnConfig['options'] = $optionNone + $columnConfig['options'];
        }

        $this->attributesToAddToCollection[] = $attribute->getAttributeCode();
        $this->addColumn(
            $attribute->getAttributeCode(),
            $columnConfig
        );
    }
}
